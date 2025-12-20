<?php

namespace App\Http\Middleware;

use App\Utils\Exceptions\AccessDeniedException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeAbility
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     * @throws AccessDeniedException
     */
    public function handle(Request $request, Closure $next, string $ability): Response
    {
        $abilities = $request->user('user')->role->abilities;
        if (!in_array($ability, $abilities) && !in_array("*", $abilities)) {
            throw new AccessDeniedException();
        }

        return $next($request);
    }
}
