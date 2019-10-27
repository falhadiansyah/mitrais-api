<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

class JwtMiddleware
{
    protected $result = [
        'success' => true,
        'message' => null,
        'data' => null,
        'exceptions' => null
    ];

    public function handle($request, Closure $next, $guard = null)
    {
        $token = $request->header('access-token');

        if(!$token) {
            // Unauthorized response if token not there
            $this->result['success'] = false;
            $this->result['message'] = 'Token not provided.';

            return get_json_response($this->result, 401);
        }

        try {
            $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
        }
        catch(ExpiredException $e) {
            $this->result['success'] = false;
            $this->result['message'] = 'Provided token is expired.';

            return get_json_response($this->result, 400);
        }
        catch(Exception $e) {
            $this->result['success'] = false;
            $this->result['message'] = 'An error while decoding token.';

            return get_json_response($this->result, 400);
        }

        $user = User::find($credentials->sub);

        // Now let's put the user in the request class so that you can grab it from there
        $request->auth = $user;
        return $next($request);
    }
}
