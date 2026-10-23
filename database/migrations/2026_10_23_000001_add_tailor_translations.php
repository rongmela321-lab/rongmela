<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Translation;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $translations = [
            'en' => Translation::getDefaultTranslations('en'),
            'bn' => Translation::getDefaultTranslations('bn'),
        ];

        foreach ($translations as $locale => $keys) {
            foreach ($keys as $fullKey => $value) {
                $key = str_starts_with($fullKey, 'db.') ? substr($fullKey, 3) : $fullKey;
                $exists = DB::table('translations')
                    ->where('locale', $locale)
                    ->where('group', 'db')
                    ->where('key', $key)
                    ->exists();

                if (!$exists) {
                    DB::table('translations')->insert([
                        'locale' => $locale,
                        'group' => 'db',
                        'key' => $key,
                        'value' => $value,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        if (class_exists(Translation::class)) {
            Translation::forgetCachedTranslations();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $enKeys = array_keys(Translation::getDefaultTranslations('en'));
        $keys = array_map(function ($k) {
            return str_starts_with($k, 'db.') ? substr($k, 3) : $k;
        }, $enKeys);

        DB::table('translations')
            ->where('group', 'db')
            ->whereIn('key', $keys)
            ->delete();

        if (class_exists(Translation::class)) {
            Translation::forgetCachedTranslations();
        }
    }
};

