<?php

namespace App\Http\Middleware;

use App\Models\Visitor;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class LogVisitor
{
    private const CACHE_KEY = 'visitor_log:';

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->hasAnyRole(['admin', 'super_admin'])) {
            return $next($request);
        }

        try {
            $ip = $request->ip();
            $today = now()->toDateString();
            $cacheKey = self::CACHE_KEY . md5($ip . $today);

            if (!Cache::has($cacheKey)) {
                Visitor::firstOrCreate([
                    'ip_address' => $ip,
                    'visited_date' => $today,
                ]);

                Cache::put($cacheKey, true, 3600);
            }
        } catch (\Exception $e) {
        }

        return $next($request);
    }
}
