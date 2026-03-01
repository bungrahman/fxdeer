<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    protected $casts = [
        'value' => 'json',
    ];

    /**
     * Get setting value by key (with caching)
     */
    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set setting value by key
     */
    public static function set(string $key, $value, $description = null)
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'description' => $description]
        );

        Cache::forget("setting.{$key}");
        
        return $setting;
    }

    /**
     * Check if a pipeline is globally disabled
     */
    public static function isPipelineDisabled(string $pipeline): bool
    {
        return (bool) self::get("kill_switch_pipeline_{$pipeline}", false);
    }

    /**
     * Check if emergency pause is active
     */
    public static function isEmergencyPauseActive(): bool
    {
        return (bool) self::get("emergency_pause", false);
    }

    /**
     * Get list of supported languages from JSON setting
     */
    public static function getSupportedLanguages(): array
    {
        $langs = self::get('supported_languages', []);
        return is_array($langs) ? $langs : [];
    }

    /**
     * Get specific language config (bot_token, chat_id)
     */
    public static function getLanguageConfig(string $code): ?array
    {
        $langs = self::getSupportedLanguages();
        foreach ($langs as $lang) {
            if (isset($lang['code']) && $lang['code'] === $code) {
                return $lang;
            }
        }
        return null;
    }
}
