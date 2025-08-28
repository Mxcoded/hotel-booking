<?php

namespace App\Http\Middleware;

use App\Models\Visitor;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Log the visitor's IP address and the date of the visit
            Visitor::firstOrCreate([
                'ip_address' => $request->ip(),
                'visited_date' => now()->toDateString(),
            ]);
        } catch (\Exception $e) {
            // If there's an error (e.g., duplicate entry), just continue
        }

        return $next($request);
    }
}
