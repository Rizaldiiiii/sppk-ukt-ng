<?php

namespace App\Http\Middleware;

use App\Services\AuditService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    public function __construct(
        private readonly AuditService $auditService,
    ) {}

    /**
     * Periksa apakah pengguna yang login memiliki salah satu peran yang diizinkan.
     * Bila ditolak, catat ke audit_logs dan kembalikan 403.
     */
    public function handle(Request $request, Closure $next, string ...$peranDiizinkan): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
        }

        if (! in_array($user->peran, $peranDiizinkan, true)) {
            $this->auditService->catatPenolakanAkses(
                pelaku: $user,
                subjekTipe: 'route',
                subjekId: $request->path(),
                alasan: sprintf(
                    'Peran "%s" mencoba mengakses rute yang memerlukan peran [%s]',
                    $user->peran,
                    implode(', ', $peranDiizinkan)
                )
            );

            abort(403, 'Akses ditolak. Anda tidak memiliki kewenangan peran untuk membuka halaman ini.');
        }

        return $next($request);
    }
}

