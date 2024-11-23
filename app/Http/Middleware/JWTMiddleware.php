<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        ## Get the token from the Authorization header (Bearer token)
        $token = $request->bearerToken();

        if (!$token) {
            return jsonResponse("error", 'Token not provided', 401); ## 401 Unauthorized
        }

        try {
            ## Decode the JWT token
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), env('JWT_ALGO', 'HS256')));

            ## Set the decoded token data to the request for later use
            $request->attributes->set('jwt_payload', $decoded);

        } catch (ExpiredException $e) {
            ## Handle token expiration separately
            return jsonResponse("error", 'Token has expired', 401); ## 401 Unauthorized
        } catch (\Exception $e) {
            ## Handle other token-related errors (invalid token, etc.)
            return jsonResponse("error", 'Invalid token', 401, $e->getMessage()); ## 401 Unauthorized
        }

        return $next($request);
    }
}
