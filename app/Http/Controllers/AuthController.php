<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Log;
use App\Traits\TokenGenerationTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    use TokenGenerationTrait;

    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['password'] = bcrypt($validatedData['password']);
        $user = User::create($validatedData);
        if ($user) {
            return response()->json([
                'message' => 'Registration was Successful',
                'success' => true,
                ], 201);
        } else {
            return response()->json([
                'message' => 'Registration failed',
                'success' => false,
                'error' => 'validation failed'
                ], 400);
        }
    }


    public function login(LoginRequest $request)
    {
        $fields = $request->validated();
        // Check email
        $user = User::where('email', $fields['email'])->first();

        if ($user && Auth::attempt(['email' => $fields['email'], 'password' => $fields['password']])) {
            $user = Auth::user();
            $token = $this->generateToken($user);

            return response()->json([
                'succeed' => true,
                'message' => 'Authentication successful',
                'user' => $user,
                'token' => $token,
            ], 200);
        }

        return response()->json([
            'message' => 'Invalid credentials',
            'error' => true,
            'success' => false,
            ], 401);
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}
