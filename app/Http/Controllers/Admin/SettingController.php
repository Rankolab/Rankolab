<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    /**
     * Display and manage system settings.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        
        // Group settings by category
        $generalSettings = $settings->filter(function ($setting) {
            return $setting->category === 'general';
        });
        
        $apiSettings = $settings->filter(function ($setting) {
            return $setting->category === 'api';
        });
        
        $contentSettings = $settings->filter(function ($setting) {
            return $setting->category === 'content';
        });
        
        $emailSettings = $settings->filter(function ($setting) {
            return $setting->category === 'email';
        });
        
        return view('admin.settings.index', compact(
            'generalSettings',
            'apiSettings',
            'contentSettings',
            'emailSettings'
        ));
    }

    /**
     * Update the specified settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $inputs = $request->except('_token', '_method');
        
        foreach ($inputs as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
            
            // Clear cache for this setting
            Cache::forget('setting_' . $key);
        }
        
        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully.');
    }
    
    /**
     * Get a setting value by key.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        // Try to get from cache first
        $cachedValue = Cache::get('setting_' . $key);
        
        if ($cachedValue !== null) {
            return $cachedValue;
        }
        
        // If not in cache, get from database
        $setting = Setting::where('key', $key)->first();
        
        if ($setting) {
            // Store in cache for future use
            Cache::put('setting_' . $key, $setting->value, now()->addHours(24));
            return $setting->value;
        }
        
        return $default;
    }
}
