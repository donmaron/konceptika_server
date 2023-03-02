<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Create new user
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);

        // Generate JWT token
        $token = $user->createToken('authToken')->accessToken;

        // Return response with JWT token
        return response()->json(['token' => $token], 201);
    }

    /**
     * Log in a user and generate JWT token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // Validate request data
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Attempt authentication
        if (!Auth::attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
            return response()->json(['error' => 'Invalid email or password'], 401);
        }

        // Generate JWT token
        $user = Auth::user();
        $token = JWTAuth::fromUser($user);

        // Return response with JWT token
        return response()->json(['token' => $token, 'user' => $user->only(['email', 'name'])], 200);
    }

    /**
     * Log out a user and revoke JWT token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out'], 200);
    }

    /**
     * Refresh JWT token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function refresh(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        $newToken = $request->user()->createToken('authToken')->accessToken;
        return response()->json(['token' => $newToken], 200);
    }

    /**
     * Get authenticated user information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function me(Request $request)
    {
        // Get authenticated user
        $user = $request->user();

        // Return user information
        return response()->json(['user' => $user->only(['email', 'name'])], 200);
    }

}