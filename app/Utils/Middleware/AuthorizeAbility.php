<?php

namespace App\Utils\Middleware;

use App\Utils\Exceptions\CustomException;
use App\Utils\Functions\FunctionUtils;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizeAbility
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     * @throws CustomException
     */
    public function handle(Request $request, Closure $next, string $ability): Response
    {

        if (!FunctionUtils::isAuthorized($request->user('user'), $ability)) {
            throw new CustomException("Access Denied");
        }

        return $next($request);
    }
}
