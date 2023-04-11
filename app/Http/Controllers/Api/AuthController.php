<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"auth"},
     *     summary="Login",
     *     description="-",
     *     operationId="login",
     *     @OA\RequestBody(
     *          required=true,
     *          description="Auth form login",
     *          @OA\JsonContent(
     *              required={"email", "password",},
     *              @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *              @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *          ),
     *      ),
     *     @OA\Response(
     *         response="default",
     *         description=""
     *     )
     * )
     */
    public function login(Request $request)
    {
        if (! Auth::attempt($request->only('email', 'password')))
        {
            return response()->json([
                'status' => 'error',
                'data' => 'Unauthorized',
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        $token = $user->createToken('be_st2023')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'access_token' => $token, 
                'token_type' => 'Bearer', 
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"auth"},
     *     summary="Register",
     *     description="-",
     *     operationId="register",
     *     @OA\RequestBody(
     *          required=true,
     *          description="Auth form register",
     *          @OA\JsonContent(
     *              required={"email", "password", "name", "password_confirmation"},
     *              @OA\Property(property="name", type="string", example="your name"),
     *              @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *              @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *              @OA\Property(property="password_confirmation", type="string", example="PassWord12345"),
     *          ),
     *      ),
     *     @OA\Response(
     *         response="default",
     *         description=""
     *     )
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'required|min:6|same:password',
        ]);

        if($validator->fails())
        {
            return response()->json([
                'status' => 'error',
                'data' => $validator->errors()
            ]);       
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
         ]);

        $token = $user->createToken('be_st2023')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'access_token' => $token, 
                'token_type' => 'Bearer', 
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"auth"},
     *     summary="Logout",
     *     description="-",
     *     operationId="logout",
     *     @OA\Parameter(
     *          name="Bearer Token",
     *          description="",
     *          required=true,
     *          in="header",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description=""
     *     )
     * )
     */
    public function logout(){
        Auth::user()->tokens()->delete();

        return [
            'message' => 'Logout success'
        ];
    }
}
