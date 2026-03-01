<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TranslationCache extends Model
{
    use HasFactory;

    protected $table = 'translations_cache';

    protected $fillable = [
        'content_hash',
        'language',
        'translated_text',
    ];

    /**
     * Get cached translation
     */
    public static function getCached(string $content, string $language): ?string
    {
        $hash = hash('sha256', $content);
        $cached = self::where('content_hash', $hash)
            ->where('language', $language)
            ->first();

        return $cached?->translated_text;
    }

    /**
     * Store translation in cache
     */
    public static function store(string $content, string $language, string $translatedText): void
    {
        $hash = hash('sha256', $content);
        
        self::updateOrCreate(
            [
                'content_hash' => $hash,
                'language' => $language,
            ],
            [
                'translated_text' => $translatedText,
            ]
        );
    }
}
