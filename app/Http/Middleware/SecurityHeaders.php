<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent clickjacking attacks
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        
        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        
        // Enable XSS protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        
        $isDev = app()->environment('local') || config('app.debug');
        $devViteSources = "";
        $devViteConnect = "";
        if ($isDev) {
            $devViteSources = " http://127.0.0.1:5173 http://localhost:5173 http://[::1]:5173" .
                " http://127.0.0.1:5174 http://localhost:5174";
            $devViteConnect = " http://127.0.0.1:5173 http://localhost:5173 http://[::1]:5173" .
                " ws://127.0.0.1:5173 ws://localhost:5173 ws://[::1]:5173" .
                " http://127.0.0.1:5174 http://localhost:5174";
        }

        // Content Security Policy
        $response->headers->set('Content-Security-Policy',
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.tailwindcss.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://fonts.googleapis.com" . $devViteSources . "; " .
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdnjs.cloudflare.com" . $devViteSources . "; " .
            "img-src 'self' data: https:; " .
            "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com; " .
            "connect-src 'self'" . $devViteConnect . "; " .
            "frame-ancestors 'self';"
        );
        
        // Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Permissions Policy
        $response->headers->set('Permissions-Policy', 
            'geolocation=(), microphone=(), camera=()'
        );
        
        // Strict Transport Security (HTTPS only)
        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
