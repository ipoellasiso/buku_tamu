<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUmkmOwner
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->umkm) {
            return response()->json([
                'message' => 'Profil UMKM belum dibuat. Silakan lengkapi profil usaha Anda terlebih dahulu.',
                'code'    => 'UMKM_NOT_FOUND',
            ], 403);
        }

        return $next($request);
    }
}
