<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } else {
            // Automatic Detection: Default to Bangla for BN browsers, English otherwise
            $browserLanguage = $request->getPreferredLanguage(['en', 'bn']);
            
            if ($browserLanguage === 'bn') {
                App::setLocale('bn');
                Session::put('locale', 'bn');
            } else {
                App::setLocale('en');
                Session::put('locale', 'en');
            }
        }

        return $next($request);
    }
}
