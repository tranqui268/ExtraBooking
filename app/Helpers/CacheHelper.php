<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheHelper
{
     public static function clearCache($cacheKey){
        Cache::store('redis')->forget($cacheKey);
        Log::info('Cleared services cache');
    }

}
