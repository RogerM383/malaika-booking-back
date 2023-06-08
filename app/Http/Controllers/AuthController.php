<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private UserService $userService;
    protected string $redirectTo = 'opendepartures';

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     *  @OA\Post(
     *      path="/api/auth/register",
     *      tags={"Auth"},
     *      summary="Register new user",
     *      description="Registra un nuevo usuario sin verificar, a la espera de ser verificado por un administrador de Sherwoodmedia.",
     *      operationId="createUser",
     *      @OA\Response(
     *          response="200",
     *          description="User created successfully"
     *      ),
     *      @OA\RequestBody(
     *          description="Create user",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"nif", "name", "username", "email", "password", "c_password"},
     *              @OA\Property(property="nif", type="string", format="text", example="48965258Z"),
     *              @OA\Property(property="name", type="string", format="text", example="Selene"),
     *              @OA\Property(property="username", type="string", format="text", example="selene"),
     *              @OA\Property(property="email", type="email", format="text", example="selene@gmail.com"),
     *              @OA\Property(property="password", type="string", format="text", example="1234567"),
     *              @OA\Property(property="c_password", type="string", format="text", example="1234567")
     *          )
     *     )
     * )
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $data = $request->all();

            $validator = Validator::make($data, [
                'name'          => 'required|string|max:255',
                'email'         => 'required|email|max:255|unique:users',
                'password'      => 'required|string|min:6',
            ]);

            Log::debug('TU VIEHE');

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors(), 409);
            }

            Log::debug('FILES');

            $user = $this->userService->create($data);




            $success['token'] = $user->createToken(config('app.name'))->accessToken;
            $success['name'] = $user->name;
            $success['success'] = true;

            return $this->sendResponse($success, 'User registered successfully.');
        } catch (Exception $e) {
            Log::debug(json_encode($e));
            return $this->sendError("Can't register the requested user", $e->getMessage(), 409);
        }
    }

    /**
     *  @OA\Post(
     *      path="/api/auth/login",
     *      tags={"Auth"},
     *      summary="Login user with username & pass",
     *      description="Loguea un usuario",
     *      operationId="loginUser",
     *      @OA\Response(
     *          response="200",
     *          description="User loged successfully"
     *      ),
     *      @OA\RequestBody(
     *          description="login user",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email", "password"},
     *              @OA\Property(property="email", type="string", format="email", example="selene@gmail.com"),
     *              @OA\Property(property="password", type="string", format="text", example="1234567")
     *          )
     *     )
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password'  => 'required|string',
            //'remember_me' => 'boolean'
        ]);

        $credentials = request(['email', 'password']);

        if (Auth::attempt($credentials)) {
            if ($request->is("api/*")) {
                $user = Auth::user();
                $success['token'] = $user->createToken(config('app.name'))->accessToken;
                $success['name'] = $user->name;
                return $this->sendResponse($success, 'User login successfully.');
            } else {
                return redirect($this->redirectTo);
            }
        } else {
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }
}
