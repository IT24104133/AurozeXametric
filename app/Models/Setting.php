<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key
     */
    public static function set($key, $value)
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get a JSON setting value
     */
    public static function getJson($key, $default = [])
    {
        $value = self::get($key);
        return $value ? json_decode($value, true) : $default;
    }

    /**
     * Set a JSON setting value
     */
    public static function setJson($key, $value)
    {
        return self::set($key, json_encode($value));
    }
}
