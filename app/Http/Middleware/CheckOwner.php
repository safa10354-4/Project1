<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckOwner
{

    public function handle(Request $request, Closure $next): Response
    {



        if (!empty(Auth::admin()) && Auth()->admin()->role!=0) {
            return response()->json([
                'status' => false,
            ], 401);
        }

        else {
            return $next($request);
        }






    }
}
