<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Routing\Controller as BaseController;

// use App\Models\User;
// use Firebase\JWT\JWT;
// use Firebase\JWT\ExpiredException;

/**
 * @OA\Info(
 *      title="Mitrais Test API",
 *      version="1.0.0",
 *      @OA\Contact(
 *          email="firman.alhadiansyah@gmail.com"
 *      ),
 *      @OA\Server(
 *          description="OpenApi host",
 *          url=API_HOST
 *      ),
 * ),
 * @OA\SecurityScheme(
 *   securityScheme="access-token",
 *   type="apiKey",
 *   in="header",
 *   name="access-token"
 * )
 */
class ApiController extends BaseController
{
    protected $result = [
        'status' => 200,
        'success' => true,
        'message' => null,
        'data' => null,
        'exceptions' => null
    ];

    /**
     * Validate the given request with the given rules.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return array
     *
     * @throws ValidationException
     */
    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        try {
            parent::validate($request, $rules, $messages, $customAttributes);

            $this->result['status'] = 200;
            $this->result['success'] = true;
        }
        catch (ValidationException $e) {
            $this->result['status'] = $e->status ? $e->status : 422;
            $this->result['success'] = false;
            $this->result['message'] = 'Error(s) occured when validating field(s)';
            $this->result['exceptions'] = $e->errors() ? $e->errors() : [];
        }
        catch (Exception $e) {
            $this->result['status'] = $e->status ? $e->status : 422;
            $this->result['success'] = false;
            $this->result['message'] = 'Error(s) occured when validating field(s)';
            $this->result['exceptions'] = $e->errors() ? $e->errors() : [];
        }

        return $this->result;
    }

    /**
     * Success response
     */
    protected function successResponse($data = null, $message = '', $code = 200) {
        $response = [
            'success' => true,
            'data' => $data,
            'message' => $message
        ];

        return get_json_response($response, $code);
    }

    /**
     * Client error response
     */
    protected function errorResponse($message = '', $exceptions = null, $code = 422) {
        $response = [
            'success' => false,
            'data' => null,
            'message' => $message ? $message : 'Error(s) occured when inserting new data',
            'exceptions' => $exceptions,
        ];

        return get_json_response($response, $code);
    }

    protected function notValidResponse($response, $code = 422) {
        return get_json_response($response, $code);
    }

    /**
     * Not found response
     * @return type
     */
    protected function notFoundResponse($message = '', $code = 404) {
        $response = [
            'success' => false,
            'data' => null,
            'message' => $message ? $message : 'Resource Not Found'
        ];

        return get_json_response($response, $code);
    }
}
