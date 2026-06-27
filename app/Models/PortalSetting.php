<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class PortalSetting extends Model
{
    use HasTranslations;

    protected $fillable = [
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    /**
     * Get a translated setting value or fall back to default.
     */
    public static function getValue(string $key, $default = null): mixed
    {
        try {
            $setting = self::where('key', $key)->first();
            if ($setting) {
                $translated = $setting->translate('value');
                if ($translated === null && is_array($setting->value)) {
                    $keys = array_keys($setting->value);
                    $hasLocaleKey = in_array('en', $keys) || in_array('bn', $keys);
                    if (!$hasLocaleKey) {
                        return $setting->value;
                    }
                }
                return $translated;
            }
        } catch (\Throwable $e) {
            // Table doesn't exist yet (e.g. during initial migrations or basic tests)
        }

        return $default;
    }
}
