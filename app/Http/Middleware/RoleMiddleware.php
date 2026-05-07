<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /* | [FUNGSI UTAMA] | 
       | Kegunaan: Mengecek apakah pengguna memiliki hak akses (role) yang tepat.
       | Cara kerja: Memeriksa kolom 'role' di tabel users dan mencocokkannya dengan aturan di web.php.
    */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            abort(403, 'Maaf, Anda harus login terlebih dahulu.');
        }

        $user = auth()->user();

        // Cek apakah user memiliki salah satu dari role yang diizinkan (atau level di atasnya)
        foreach ($roles as $role) {
            if ($user->hasRoleLevel($role)) {
                return $next($request);
            }
        }

        abort(403, 'Maaf, Anda tidak memiliki akses yang cukup untuk fitur ini.');
    }
}
