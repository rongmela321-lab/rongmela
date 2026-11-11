<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class WhatsappSetting extends Model
{
    protected $fillable = [
        'phone_number_id',
        'business_account_id',
        'permanent_access_token',
    ];

    private $baseUrl = "https://graph.facebook.com/v22.0";

    public function sendMessage(array $phoneNumbers, string $type, $messageContent)
    {
        // Set endpoint and headers
        $messageEndpoint = "{$this->baseUrl}/{$this->phone_number_id}/messages";
        $headers = [
            'Authorization' => "Bearer {$this->permanent_access_token}",
            'Accept' => 'application/json',
        ];

        try {
            // Base payload
            $payload = [
                'messaging_product' => 'whatsapp',
            ];

            // Template message
            if ($type == 'template') {
                $payload['type'] = 'template';
                $payload['template'] = [
                        'name' => $messageContent['name'],
                        'language' => ['code' => $messageContent['lang_code']],
                ];
                if (!empty($messageContent['components'])) {
                    $payload['template']['components'] = $messageContent['components'];
                }
            }
            // Text message
            elseif ($type == 'text') {
                $payload['type'] = 'text';
                $payload['text'] = ['body' => $messageContent];
            }
            // Image or Document
            elseif (in_array($type, ['image', 'document'])) {
                $file = $messageContent['file'];
                $originalName = $file->getClientOriginalName();

                // 🟢 Step 1: Upload media to WhatsApp
                $uploadResponse = Http::withOptions(['verify' => false])
                    ->withHeaders([
                        'Authorization' => "Bearer {$this->permanent_access_token}",
                    ])->attach(
                        'file',
                        file_get_contents($file->getRealPath()),
                        $originalName
                    )->post("{$this->baseUrl}/{$this->phone_number_id}/media", [
                        'messaging_product' => 'whatsapp',
                    ]);

                if (!$uploadResponse->successful()) {
                    return [
                        'success' => false,
                        'message' => __('db.failed_upload_media_whatsApp'),
                        'body' => $uploadResponse->body(),
                    ];
                }

                $mediaId = $uploadResponse->json('id');
                if (!$mediaId) {
                    return [
                        'success' => false,
                        'message' => __('db.media_id_not_returned_from_WhatsApp'),
                    ];
                }

                $payload['type'] = $type;
                $payload[$type] = ['id' => $mediaId];

                if ($type == 'document') {
                    $payload[$type]['filename'] = $originalName;
                }

                if (!empty($messageContent['caption'])) {
                    $payload[$type]['caption'] = $messageContent['caption'];
                }
            }

            $results = [];// Store response per number for debug purpose

            // Loop through all numbers
            $hasError = false;
            $lastErrorMessage = '';

            foreach ($phoneNumbers as $phoneNumber) {
                // Ensure international format (e.g. Bangladesh: 017... -> 88017...)
                $cleanPhone = preg_replace('/\D/', '', $phoneNumber);
                if (strlen($cleanPhone) === 11 && str_starts_with($cleanPhone, '01')) {
                    $cleanPhone = '88' . $cleanPhone;
                }
                $payload['to'] = $cleanPhone;

                // Send message request
                $response = Http::withOptions(['verify' => false])
                    ->withHeaders($headers)
                    ->post($messageEndpoint, $payload);

                \Illuminate\Support\Facades\Log::info('WhatsApp sendMessage response', [
                    'to' => $cleanPhone,
                    'endpoint' => $messageEndpoint,
                    'payload' => $payload,
                    'status' => $response->status(),
                    'response' => $response->json() ?? $response->body()
                ]);

                if (!$response->successful()) {
                    $hasError = true;
                    $errorData = $response->json('error');
                    $lastErrorMessage = $errorData['message'] ?? $response->body();
                }

                $results[$cleanPhone] = $response->json();
            }

            if ($hasError) {
                return [
                    'success' => false,
                    'message' => $lastErrorMessage,
                ];
            }

            return [
                'success' => true,
                'message' => __('db.message_sent_successfully'),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Exception: ' . $e->getMessage(),
            ];
        }
    }

    // 🔹 Get templates from Facebook
    public function getTemplates()
    {
        try {
            $phoneCheck = Http::withOptions(['verify' => false])
                ->withHeaders(['Authorization' => "Bearer {$this->permanent_access_token}"])
                ->get("{$this->baseUrl}/{$this->phone_number_id}?fields=id,display_phone_number,whatsapp_business_account");
            \Illuminate\Support\Facades\Log::info('Phone Number Info from Meta', [
                'phone_id' => $this->phone_number_id,
                'business_account_id_in_db' => $this->business_account_id,
                'response' => $phoneCheck->json()
            ]);

            $url = "{$this->baseUrl}/{$this->business_account_id}/message_templates";
            $headers = ['Authorization' => "Bearer {$this->permanent_access_token}"];

            $response = Http::withOptions(['verify' => false])->withHeaders($headers)->get($url);

            if ($response->successful()) {
                $data = $response->json('data') ?? [];
                foreach ($data as $t) {
                    if (($t['name'] ?? '') === 'rongmela_invoice') {
                        \Illuminate\Support\Facades\Log::info('Full rongmela_invoice template object', $t);
                    }
                }
                return $data;
            }

            \Illuminate\Support\Facades\Log::error('WhatsApp templates fetch error', [
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body()
            ]);

            $errorMsg = $response->json('error.message') ?? __('db.failed_to_fetch_templates');
            return ['error' => $errorMsg];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('WhatsApp templates exception: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    // 🔹 Delete template from Facebook
    public function deleteTemplate(string $name)
    {
        try {
            $url = "{$this->baseUrl}/{$this->business_account_id}/message_templates?name={$name}";
            $headers = ['Authorization' => "Bearer {$this->permanent_access_token}"];

            $response = Http::withOptions(['verify' => false])->withHeaders($headers)->delete($url);

            if ($response->successful()) {
                return ['success' => true, 'message' => __('db.template_deleted_successfully')];
            }

            \Illuminate\Support\Facades\Log::error('WhatsApp deleteTemplate error', [
                'name' => $name,
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body()
            ]);

            return [
                'success' => false,
                'message' => $response->json('error.message') ?? __('db.failed_delete_template'),
                'response' => $response->json(),
            ];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('WhatsApp deleteTemplate exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

}
