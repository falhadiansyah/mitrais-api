<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use App\Models\User;

class AuthController extends ApiController {
    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(Request $request) {
        $this->request = $request;
    }

    /**
     * Create a new token.
     *
     * @param  \App\Models\User   $user
     * @return string
     */
    protected function jwt(User $user) {
        $payload = [
            'iss' => "lumen-jwt", // Issuer of the token
            'sub' => $user->id, // Subject of the token
            'iat' => time(), // Time when JWT was issued.
            'exp' => time() + 24*60*60 // Expiration time
        ];

        // As you can see we are passing `JWT_SECRET` as the second parameter that will
        // be used to decode the token in the future.
        return JWT::encode($payload, env('JWT_SECRET'));
    }

    /**
     * @OA\Post(
     *      path="/auth/login",
     *      tags={"Authentication"},
     *      summary="Authenticate a user and return the token if the provided credentials are correct.",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="username", type="string"),
     *                  @OA\Property(property="password", type="string"),
     *                  required={"username", "password"},
     *                  example={"username": "webdev@mailinator.com", "password": "secret@123"}
     *              )
     *          )
     *      ),
     *      @OA\Response( response="200", description="Success",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="success", type="boolean"),
     *                  @OA\Property(property="status", type="integer"),
     *                  @OA\Property(property="message", type="string|null"),
     *                  @OA\Property(property="data", type="array|object|null"),
     *                  @OA\Property(property="exceptions", type="array|object|null"),
     *                  example={"success": true, "status": 200, "message": null,
     *                      "data": {
     *                          "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJsdW1lbi1qd3QiLCJzdWIiOjIsImlhdCI6MTU2MDkwNTQyNywiZXhwIjoxNTYwOTkxODI3fQ.dU-BsGjUKIK7HjeS9YbFjECYK9dOrePnRVqK0vhD_ZM",
     *                          "user": {
     *                              "id": 2,
     *                              "role_id": 2,
     *                              "email": "webdev@mailinator.com",
     *                              "handphone": "081234567891",
     *                              "first_name": "Web",
     *                              "last_name": "developer",
     *                              "api_token": null,
     *                              "remember_token": null,
     *                              "confirmation_code": "bAGTY90Ov4tCMsbxCyBq",
     *                              "confirmed_at": "2019-06-19 07:30:16",
     *                              "created_at": "2019-06-19 07:30:16",
     *                              "updated_at": "2019-06-19 07:30:16"
     *                          }
     *                      },
     *                      "exceptions": null
     *                  }
     *              )
     *          )
     *      ),
     *      @OA\Response( response="400", description="Validation Error",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="success", type="boolean"),
     *                  @OA\Property(property="status", type="integer"),
     *                  @OA\Property(property="message", type="string|null"),
     *                  @OA\Property(property="data", type="array|object|null"),
     *                  @OA\Property(property="exceptions", type="array|object|null"),
     *                  example={"success": false, "status": 400, "message": "Missing required field(s)", "data": null,
     *                      "exceptions": {
     *                          "username": {"The email field is required."},
     *                          "password": {"The password field is required."},
     *                      }
     *                  }
     *              )
     *          )
     *      ),
     *      @OA\Response( response="500", description="Fatal Error",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="success", type="boolean"),
     *                  @OA\Property(property="status", type="integer"),
     *                  @OA\Property(property="message", type="string|null"),
     *                  @OA\Property(property="data", type="array|object|null"),
     *                  @OA\Property(property="exceptions", type="array|object|null"),
     *                  example={"success": false, "status": 500, "message": "HTTP_INTERNAL_SERVER_ERROR", "data": null, "exceptions": null
     *                  }
     *              )
     *          )
     *      ),
     * )
     */
    public function authenticate() {
        $rules = [
            'username' => 'required|email',
            'password' => 'required|string'
        ];

        $validate = $this->validate($this->request, $rules);
        if ($validate['status'] !== 200 || $validate['success'] !== true) {
            return $this->notValidResponse($validate, $validate['status']);
        }

        // Find the user by username/email
        $query = User::where('email', $this->request->input('username'));
        $user = $query->first();
        if (!$user) {
            return $this->notFoundResponse('User does not exist.');
        }

        // Verify the password and generate the token
        if (Hash::check($this->request->input('password'), $user->password)) {
            return $this->successResponse([
                'access_token' => $this->jwt($user),
                'user' => $user
            ]);
        }

        // Bad Request response
        return $this->errorResponse("Username or password is wrong.");
    }

    /**
     * @OA\Post(
     *      path="/auth/register",
     *      tags={"Authentication"},
     *      summary="User Registration",
     *      description="",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  @OA\Property(property="phone", type="number"),
     *                  @OA\Property(property="email", type="string"),
     *                  @OA\Property(property="password", type="string"),
     *                  @OA\Property(property="password_confirmation", type="string"),
     *                  @OA\Property(property="first_name", type="string"),
     *                  @OA\Property(property="last_name", type="string"),
     *                  @OA\Property(property="birth_date", type="date"),
     *                  required={"phone", "email", "password", "password_confirmation", "first_name", "last_name"},
     *                  example={
     *                      "phone": "081234567892",
     *                      "email": "userdev1@mailinator.com",
     *                      "password": "secret@123",
     *                      "password_confirmation": "secret@123",
     *                      "first_name": "userdev1",
     *                      "last_name": "test",
     *                      "birth_date": "2000-02-02"
     *                  }
     *              )
     *          )
     *      ),
     *      @OA\Response( response="200", description="Success",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="success", type="boolean"),
     *                  @OA\Property(property="status", type="integer"),
     *                  @OA\Property(property="message", type="string|null"),
     *                  @OA\Property(property="data", type="array|object|null"),
     *                  @OA\Property(property="exceptions", type="array|object|null"),
     *                  example={"success": true, "status": 200, "message": "New Daop Successfully Created",
     *                      "data": {
     *                          "id": 12,
     *                          "role_id": 4,
     *                          "email": "userdev6@mailinator.com",
     *                          "username": "userdev6",
     *                          "id_path": "http://apiabm.local/storage/user/images/1547451840-download.jpg",
     *                          "updated_at": "2019-01-14 07:44:01",
     *                          "created_at": "2019-01-14 07:44:01",
     *                      },
     *                      "exceptions": null
     *                  }
     *              )
     *          )
     *      ),
     *      @OA\Response( response="400", description="Validation Error",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="success", type="boolean"),
     *                  @OA\Property(property="status", type="integer"),
     *                  @OA\Property(property="message", type="string|null"),
     *                  @OA\Property(property="data", type="array|object|null"),
     *                  @OA\Property(property="exceptions", type="array|object|null"),
     *                  example={"success": false, "status": 400, "message": "Error(s) occured when validating field(s)", "data": null,
     *                      "exceptions": {
     *                          "email": {"The email field is required."},
     *                          "password": {"The password field is required."}
     *                      }
     *                  }
     *              )
     *          )
     *      ),
     *      @OA\Response( response="500", description="Fatal Error",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="success", type="boolean"),
     *                  @OA\Property(property="status", type="integer"),
     *                  @OA\Property(property="message", type="string|null"),
     *                  @OA\Property(property="data", type="array|object|null"),
     *                  @OA\Property(property="exceptions", type="array|object|null"),
     *                  example={"success": false, "status": 500, "message": "HTTP_INTERNAL_SERVER_ERROR", "data": null, "exceptions": null}
     *              )
     *          )
     *      ),
     * )
     */
    public function register(Request $request) {
        $rules = [
            'phone' => 'required|numeric|unique:users,phone',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'birth_date' => 'sometimes|date_format:Y-m-d|nullable',
        ];

        $validate = $this->validate($request, $rules);
        if ($validate['status'] !== 200 || $validate['success'] !== true) {
            return $this->notValidResponse($validate, $validate['status']);
        }

        try {
            $params = $request->only('phone', 'email', 'first_name', 'last_name', 'birth_date');
            $params['role_id'] = 3; // user
            $params['password'] = bcrypt($request->password);
            $params['confirmation_code'] = str_random(60);

            $user = User::create(
                $params
            );

            return $this->successResponse($user, 'User successfully registered');
        }
        catch (QueryException $e) {
            return $this->errorResponse('Error(s) occured when registering the new user.', $e->getMessage());
        }
        catch (\Exception $e) {
            return $this->errorResponse('Error(s) occured when registering the new user.', $e->getMessage());
        }
    }

    
}
