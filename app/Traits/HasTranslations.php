<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;

trait HasTranslations
{
    /**
     * Get the translated value for a given attribute.
     * Use this in your Blade/Vue props: $post->translate('title')
     */
    public function translate(string $attribute, ?string $locale = null): mixed
    {
        $locale = $locale ?? App::getLocale();
        $translations = $this->getAttribute($attribute);

        // If it's already an array (JSON casted)
        if (is_array($translations)) {
            return $translations[$locale] ?? ($translations[config('app.fallback_locale')] ?? null);
        }

        // Try decoding if it's a string
        $decoded = json_decode($translations, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded[$locale] ?? ($decoded[config('app.fallback_locale')] ?? null);
        }

        return $translations;
    }
}
