<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
        'category',
        'description',
    ];

    /**
     * Get a setting value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getValue($key, $default = null)
    {
        // Check cache first
        $cacheKey = 'setting_' . $key;
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        // Get from database
        $setting = self::where('key', $key)->first();
        
        if ($setting) {
            // Cache for 24 hours
            Cache::put($cacheKey, $setting->value, now()->addHours(24));
            return $setting->value;
        }
        
        return $default;
    }
    
    /**
     * Set a setting value.
     *
     * @param string $key
     * @param mixed $value
     * @param string $category
     * @param string $description
     * @return Setting
     */
    public static function setValue($key, $value, $category = 'general', $description = null)
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'category' => $category,
                'description' => $description,
            ]
        );
        
        // Update cache
        Cache::put('setting_' . $key, $value, now()->addHours(24));
        
        return $setting;
    }
    
    /**
     * Clear cache for a setting.
     *
     * @param string $key
     * @return void
     */
    public static function clearCache($key)
    {
        Cache::forget('setting_' . $key);
    }
    
    /**
     * Clear all settings cache.
     *
     * @return void
     */
    public static function clearAllCache()
    {
        $settings = self::all();
        
        foreach ($settings as $setting) {
            Cache::forget('setting_' . $setting->key);
        }
    }
}
