<?php

namespace App\Http\Controllers;

use JWTAuth;
use App\Events\JwtHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Webpatser\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Auth;

class AuthenticateController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/v1/user/create",
     * summary="Create endpoint for Users",
     * description="Register Users",
     * operationId="Registration",
     * tags={"User Endpoint"},
     * @OA\Parameter(
     *    name="email",
     *    in="query",
     *    description="Email",
     *    required=true,
     *
     *    ),
     * @OA\Parameter(
     *    name="password",
     *    in="query",
     *    description="Password",
     *    required=true,
     *
     *    ),
     * @OA\Parameter(
     *    name="password_confirmation",
     *    in="query",
     *    description="Password Confirmation",
     *    required=true,
     *
     *    ),
     * @OA\Parameter(
     *    name="first_name",
     *    in="query",
     *    description="Firstname",
     *    required=true,
     *
     *    ),
     * @OA\Parameter(
     *    name="last_name",
     *    in="query",
     *    description="Lastname",
     *    required=true,
     *
     *    ),
     * @OA\Parameter(
     *    name="address",
     *    in="query",
     *    description="Address",
     *    required=true,
     *
     *    ),
     * @OA\Parameter(
     *    name="phone_number",
     *    in="query",
     *    description="Phone Number",
     *    required=true,
     *
     *    ),
     * @OA\Parameter(
     *    name="avatar",
     *    in="query",
     *    description="Avatar Image UUID",
     *
     *
     *    ),
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *
     *  ),
     *
     * @OA\Response(
     *    response=422,
     *    description="Unprocessable Entity",
     *
     * ),
     * @OA\Response(
     *    response=401,
     *    description="Unprocessable Entity",
     *
     * ),
     *
     * @OA\Response(
     *    response=404,
     *    description="Page not found",
     *
     * ),
     *
     * )
     */

    public function register(Request $request)
    {
        //Validate data
        $data = $request->all();
        $validator = Validator::make($data, [
            "first_name" => "required",
            "last_name" => "required",
            "address" => "required",
            "phone_number" => "required",
            "email" => "required|email|unique:users",
            "password" => "required|string|confirmed|min:6|max:50",
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(["error" => $validator->messages()], 200);
        }

        //Request is valid, create new user
        $data["uuid"] = (string) Uuid::generate();
        $data["password"] = bcrypt($request->password);

        $user = User::create($data);

        $token = JWTAuth::fromUser($user);

        $type = "Registeration";

        JwtHistory::dispatch($token, $user, $type);

        //User created, return success response
        return response()->json(
            [
                "success" => true,
                "message" => "User created successfully",
                "data" => $user,
                "token" => $token,
            ],
            Response::HTTP_OK
        );
    }

    /**
     * @OA\Post(
     * path="/api/v1/user/login",
     * summary="Sign in",
     * description="Login by email, password",
     * operationId="authLogin",
     * tags={"User Endpoint"},
     * @OA\Parameter(
     *    name="email",
     *    in="query",
     *    description="Email",
     *    required=true,
     *
     *    ),
     * @OA\Parameter(
     *    name="password",
     *    in="query",
     *    description="Password",
     *    required=true,
     *
     *    ),
     *
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *
     *  ),
     *
     * @OA\Response(
     *    response=422,
     *    description="Unprocessable Entity",
     *
     * ),
     * @OA\Response(
     *    response=401,
     *    description="Unprocessable Entity",
     *
     * ),
     *
     * @OA\Response(
     *    response=404,
     *    description="Page not found",
     *
     * ),
     * )
     */

    public function authenticate(Request $request)
    {
        $credentials = $request->only("email", "password");

        //valid credential
        $validator = Validator::make($credentials, [
            "email" => "required|email",
            "password" => "required|string|min:6|max:50",
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json(["error" => $validator->messages()], 200);
        }

        //Request is validated
        //Crean token
        try {
            if (!($token = JWTAuth::attempt($credentials))) {
                return response()->json(
                    [
                        "success" => false,
                        "message" => "Login credentials are invalid.",
                    ],
                    400
                );
            }
        } catch (JWTException $e) {
            return $credentials;
            return response()->json(
                [
                    "success" => false,
                    "message" => "Could not create token.",
                ],
                500
            );
        }

        $user = Auth::user();

        $type = "logged in";

        JwtHistory::dispatch($token, $user, $type);
        //Token created, return with success response and jwt token
        return response()->json([
            "success" => true,
            "token" => $token,
        ]);
    }

    /** @OA\Get(
     * path="/api/v1/user",
     * summary="View a User Account",
     * description="Get profile short information",
     * operationId="user",
     * tags={"User Endpoint"},
     * security={ {"bearerAuth": {} }},
     *@OA\Response(
     *     response=200,
     *     description="Success",
     *
     *  ),
     *
     * @OA\Response(
     *    response=422,
     *    description="Unprocessable Entity",
     *
     * ),
     * @OA\Response(
     *    response=401,
     *    description="Unprocessable Entity",
     *
     * ),
     *
     * @OA\Response(
     *    response=404,
     *    description="Page not found",
     *
     * ),
     * )
     **/

    public function getUser(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        return response()->json(["user" => $user]);
    }

    /** @OA\Delete(
     * path="/api/v1/user",
     * summary="View a User Account",
     * description="Get profile short information",
     * operationId="user delete",
     * tags={"User Endpoint"},
     * security={ {"bearerAuth": {} }},
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *
     *  ),
     *
     * @OA\Response(
     *    response=422,
     *    description="Unprocessable Entity",
     *
     * ),
     * @OA\Response(
     *    response=401,
     *    description="Unprocessable Entity",
     *
     * ),
     *
     * @OA\Response(
     *    response=404,
     *    description="Page not found",
     *
     * ),
     * )
     **/

    public function destroy()
    {
        $user = JWTAuth::parseToken()->authenticate();

        $user->delete();

        return response()->json(
            [
                "success" => true,
                "message" => "User deleted successfully",
            ],
            Response::HTTP_OK
        );
    }

    /** @OA\Get(
     * path="/api/v1/user/logout",
     * summary="Logout",
     * description="Get profile short information",
     * operationId="Logout active user",
     * tags={"User Endpoint"},
     * security={ {"bearerAuth": {} }},
     * @OA\Response(
     *     response=200,
     *     description="Success",
     *
     *  ),
     *
     * @OA\Response(
     *    response=422,
     *    description="Unprocessable Entity",
     *
     * ),
     * @OA\Response(
     *    response=401,
     *    description="Unprocessable Entity",
     *
     * ),
     *
     * @OA\Response(
     *    response=404,
     *    description="Page not found",
     *
     * ),
     * )
     **/

    public function logout()
    {
        $user = JWTAuth::parseToken()->authenticate();

        //Request is validated, do logout
        try {
            JWTAuth::invalidate($user);

            return response()->json([
                "success" => true,
                "message" => "User has been logged out",
            ]);
        } catch (JWTException $exception) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Sorry, user cannot be logged out",
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
