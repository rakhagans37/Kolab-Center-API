<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // REGISTER API
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'data' => [
                'user' => $user,
                'token' => $user->createToken('API Token')->plainTextToken,
            ]

        ], 201);
    }

    // LOGIN API
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json([
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $user->createToken('API Token')->plainTextToken,
            ]
        ]);
    }

    // LOGOUT API
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout successful'
        ]);
    }

    public function getUser(Request $request)
    {
        return response()->json(
            [
                'message' => 'User data',
                'data' => [
                    'user' => $request->user()->load('attendance')
                ]
            ]
        );
    }
}
