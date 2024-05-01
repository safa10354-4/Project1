<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{

    public function handle(Request $request, Closure $next)
    {



        if (!empty(Auth::user()) && Auth()->user()->role != 1) {
            return response()->json([
                'status' => false,
            ], 401);
        } else {
            return $next($request);
        }








    }






}
