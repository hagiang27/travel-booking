<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$vaiTroChoPhep): Response
    {
        $vaiTro = $request->session()->get('vai_tro');

        if (! $vaiTro || ! in_array($vaiTro, $vaiTroChoPhep, true)) {
            abort(403, 'Không đủ quyền truy cập.');
        }

        return $next($request);
    }
}