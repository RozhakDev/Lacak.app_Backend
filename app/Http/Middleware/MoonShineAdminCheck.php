<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MoonShineAdminCheck
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !auth()->user()->hasAnyRole(['Super Admin', 'Admin BKK'])) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('moonshine.login')->withErrors(['username' => 'Akses Ditolak. Anda bukan Administrator.']);
        }

        return $next($request);
    }
}
