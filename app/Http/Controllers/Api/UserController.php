<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Create user
     * @param Request $request
     * @return User
     */

    public function createUser(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validateUser->errors()
                ], 200);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
                'token' => $user->createToken('API Token')->plainTextToken,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Internal server error.',
            ], 500);
        }
    }

    /**
     * Login The User
     * @param Request $request
     * @return User
     */

    public function loginUser(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required',
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validateUser->errors()
                ], 200);
            }

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email or password is invalid',
                ], 200);
            }

            $user = Auth::user();

            return response()->json([
                'status' => true,
                'message' => 'User Logged in successfully',
                'token' => $user->createToken('API Token')->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Internal server error.',
            ], 500);
        }
    }

    /**
     * Logout the user and invalidate the token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            $token = $request->user()?->currentAccessToken();

            if ($token) {
                $token->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'User logged out successfully'
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized'
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Internal server error.',
            ], 500);
        }
    }

    /**
     * Check if the provided token is valid
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateToken(Request $request)
    {
        try {
            $user = Auth::guard('sanctum')->user();

            if ($user) {
                return response()->json([
                    'status' => true,
                    'message' => 'Token is valid',
                    'user'=>$user
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Token is invalid'
                ], 401); // Unauthorized
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Internal server error.',
            ], 500);
        }
    }
}
