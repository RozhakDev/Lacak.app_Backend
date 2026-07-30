<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;

class AppLogger
{
    public static function context(array $extra = []): array
    {
        $request = request();

        return array_merge([
            'user_id' => auth()->id() ?? 'guest',
            'ip' => $request->ip(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'user_agent' => $request->userAgent(),
        ], $extra);
    }

    public static function api(): \Illuminate\Log\Logger
    {
        return Log::channel('api');
    }

    public static function auth(): \Illuminate\Log\Logger
    {
        return Log::channel('auth');
    }

    public static function export(): \Illuminate\Log\Logger
    {
        return Log::channel('export');
    }
}
