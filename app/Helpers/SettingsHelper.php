<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

if (!function_exists('setting')) {
    /**
     * Get the value of a setting by its key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key, $default = null)
    {
        // Cache the settings for performance
        $settings = Cache::rememberForever('settings', function () {
            return Setting::all()->pluck('value', 'key')->all();
        });

        return $settings[$key] ?? $default;
    }
}
