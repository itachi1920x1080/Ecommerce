<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // ឆែកមើលថាបើគេ Login ហើយ និងមានសិទ្ធិជា 'admin' ទើបឲ្យដើរទៅមុខបន្ត
        if ($request->user() && $request->user()->role === 'admin') {
            return $next($request);
        }

        // បើមិនមែនជា admin ទេ បោះសារ Error បដិសេធ (403 Forbidden)
        return response()->json([
            'message' => 'សុំទោស! មានតែ Admin ទេទើបមានសិទ្ធិធ្វើរឿងនេះបាន។'
        ], 403);
    }
}