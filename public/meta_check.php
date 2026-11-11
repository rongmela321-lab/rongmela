<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$whSetting = App\Models\WhatsappSetting::first();
if (!$whSetting) {
    die("No whatsapp setting");
}

$baseUrl = "https://graph.facebook.com/v22.0";
$headers = ['Authorization' => "Bearer {$whSetting->permanent_access_token}"];

// Check status of template 2035334840492107 in real WABA 1122608607200684
$tplStatusResp = Illuminate\Support\Facades\Http::withOptions(['verify' => false])
    ->withHeaders($headers)
    ->get("{$baseUrl}/2035334840492107");

$tplData = $tplStatusResp->json();
$status = $tplData['status'] ?? 'UNKNOWN';

$sendResult = null;
if ($status === 'APPROVED') {
    // Test send immediately!
    $testPayload = [
        'messaging_product' => 'whatsapp',
        'type' => 'template',
        'template' => [
            'name' => 'rongmela_invoice',
            'language' => ['code' => 'bn'],
            'components' => [
                [
                    'type' => 'body',
                    'parameters' => [
                        ['type' => 'text', 'text' => 'Sajjad Hossain'],
                        ['type' => 'text', 'text' => 'RONGMELA-TEST-01'],
                        ['type' => 'text', 'text' => date('d-m-Y')],
                        ['type' => 'text', 'text' => '135.00'],
                        ['type' => 'text', 'text' => '5.00'],
                        ['type' => 'text', 'text' => '130.00'],
                    ]
                ]
            ]
        ],
        'to' => '8801684067842'
    ];

    $sendResp = Illuminate\Support\Facades\Http::withOptions(['verify' => false])
        ->withHeaders($headers)
        ->post("{$baseUrl}/{$whSetting->phone_number_id}/messages", $testPayload);

    $sendResult = [
        'status' => $sendResp->status(),
        'response' => $sendResp->json()
    ];
}

header('Content-Type: application/json');
echo json_encode([
    'template_id' => '2035334840492107',
    'status' => $status,
    'template_details' => $tplData,
    'test_send_result' => $sendResult,
], JSON_PRETTY_PRINT);
exit;
