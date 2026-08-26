<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends ApiController
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            // Generate Laravel Sanctum Token to mimic JWT payload
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse([
                'user' => $user,
                'token' => $token,
                'role' => $user->role_id === 1 ? 'superadmin' : ($user->role_id === 2 ? 'admin' : 'customer')
            ], 'Login successful');
        }

        return $this->errorResponse('Invalid credentials', 401);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'required|string|email|max:150|unique:users,email',
            'mobile' => 'nullable|string|max:20|unique:users,mobile',
            'password' => 'required|string|min:6'
        ], [
            'email.unique' => 'An account with this email already exists.',
            'mobile.unique' => 'This mobile number is already registered to another account.',
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'email' => $validated['email'],
            'mobile' => !empty($validated['mobile']) ? $validated['mobile'] : null,
            'password' => Hash::make($validated['password']),
            'role_id' => 3, // Default customer
            'is_active' => 1
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'user' => $user,
            'token' => $token
        ], 'User registered successfully', 201);
    }

    public function me(Request $request)
    {
        return $this->successResponse($request->user(), 'User retrieved successfully');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->successResponse(null, 'Logged out successfully');
    }
}
