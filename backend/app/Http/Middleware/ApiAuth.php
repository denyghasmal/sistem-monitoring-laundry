<?php

namespace App\Http\Middleware;

use App\Models\PersonalAccessToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $bearer = $request->bearerToken();

        if (!$bearer) {
            return response()->json([
                'success' => false,
                'message' => 'Token otentikasi tidak ditemukan (Bearer token missing).',
                'data' => null,
            ], 401);
        }

        $tokenRecord = PersonalAccessToken::findToken($bearer);

        if (!$tokenRecord || !$tokenRecord->user) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi login tidak valid atau token telah kedaluwarsa.',
                'data' => null,
            ], 401);
        }

        $user = $tokenRecord->user;

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun pengguna sedang dinonaktifkan.',
                'data' => null,
            ], 403);
        }

        // Set user in request & auth guard
        $request->setUserResolver(fn () => $user);
        Auth::setUser($user);

        return $next($request);
    }
}

