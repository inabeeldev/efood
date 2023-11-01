<?php

namespace App\Http\Middleware;

use App\Model\DeliveryMan;
use Closure;
use Illuminate\Support\Facades\Auth;


class ApiActiveDeliveryMan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->header('token') ? $request->header('token') :  $request->token;
        $dm = DeliveryMan::firstWhere('auth_token', $token);

        if (isset($dm) && $dm->is_active == 1) {
            return $next($request);
        }
        $errors = [];
        $errors[] = ['code' => 'auth-001', 'message' => 'Delivery man is inactive!'];
        return response()->json([
            'errors' => $errors
        ], 401);
    }
}
