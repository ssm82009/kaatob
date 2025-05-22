<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'description'];
    
    // Get setting by key
    public static function getByKey($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }
    
    // Set setting by key
    public static function setByKey($key, $value, $description = null)
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            // Create new setting
            $setting = new self();
            $setting->key = $key;
            if ($description) {
                $setting->description = $description;
            }
        }
        
        $setting->value = $value;
        $setting->save();
        
        return $setting;
    }
}
