<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AdminSetting extends Model
{
    protected $table    = 'admin_settings';
    protected $fillable = ['key', 'group', 'label', 'value', 'type'];

    // ── Static helpers ───────────────────────────────────────────────────────

    /** Get a single setting value (with optional default). */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /** Set / upsert a setting value. */
    public static function set(string $key, mixed $value): void
    {
        static::where('key', $key)->update(['value' => $value]);
    }

    /** Return all settings for a group as key => value map. */
    public static function group(string $group): array
    {
        return static::where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }
}
