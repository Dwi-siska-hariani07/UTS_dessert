<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!in_array($user->role, $roles)) {
            // Jika akun Customer mencoba membuka dashboard Admin/Staff, alihkan dengan ramah ke Toko
            if ($user->isCustomer()) {
                return redirect()->route('shop')->with('info', 'ℹ️ Halaman Dashboard & Kelola Data hanya untuk Admin & Staff. Jika ingin masuk sebagai Admin/Staff, silakan klik tombol "Keluar" di pojok kanan atas lalu Login kembali.');
            }

            abort(403, 'Akses Ditolak: Peran akun Anda (' . ucfirst($user->role) . ') tidak diizinkan mengakses fitur ini.');
        }

        return $next($request);
    }
}
