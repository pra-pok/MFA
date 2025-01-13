<?php

use Illuminate\Support\Str;

if (!function_exists('generateAutoKey')) {
    function generateAutoKey($teamId, $userId)
    {

        $key = "team_{$teamId}_user_{$userId}";

        return hash('sha256', $key);
    }
}
