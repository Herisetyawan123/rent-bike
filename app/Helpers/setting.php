<?php

use App\Models\BusinessSetting;

if (!function_exists('getSetting')) {
    function getSetting(string $key, $default = null): mixed
    {
        return BusinessSetting::where('setting_key', $key)->first()?->setting_value ?? $default;
    }
}
