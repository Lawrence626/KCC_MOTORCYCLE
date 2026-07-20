<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DSSSettings extends Model
{
    protected $table = 'dss_settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * Get a setting value by key.
     */
    public static function getSetting(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        return match ($setting->type) {
            'integer' => (int) $setting->value,
            'boolean' => (bool) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value,
        };
    }

    /**
     * Update a setting value by key.
     */
    public static function setSetting(string $key, $value, string $type = 'string'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : (string) $value,
                'type' => $type,
            ]
        );
    }

    /**
     * Get all settings as an associative array.
     */
    public static function allSettings(): array
    {
        return static::all()->mapWithKeys(function ($setting) {
            return [$setting->key => static::getSetting($setting->key)];
        })->toArray();
    }
}
