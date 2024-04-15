<?php

namespace App\Http\Middleware;

use Closure;
use App\Utils\Notify;
use App\Security\Guardian;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if(!Guardian::checkToken()){
            return Response()->json(Notify::info("Login expirado, realize o login novamente!"), 401);
        }

        if(!Guardian::checkAccess($request->route()->getPrefix())){
            return Response()->json(Notify::warning("Você não possui acesso a este módulo!"), 403);
        }
        
        return $next($request);
    }
}
