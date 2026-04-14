<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        $data = $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'user',
            'status' => 'active'
        ]);

        return response()->json([
            'token' => $user->createToken('api-token')->plainTextToken,
            'user' => $user
        ], 201);
    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        if ($user->status !== 'active') {
            return response()->json(['message' => 'Usuario bloqueado o suspendido'], 403);
        }

        return response()->json([
            'token' => $user->createToken('api-token')->plainTextToken,
            'user' => $user
        ]);
    }

    public function logout(Request $request) 
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada con éxito'
        ]);
    }

    public function updateEmail(Request $request) {
        $user = $request->user();
        $request->validate(['email' => 'required|email|unique:users,email,' . $user->id]);
        $request->user()->update(['email' => $request->email]);
        return response()->json(['message' => 'Email actualizado']);
    }

    public function updatePassword(Request $request) {
        $request->validate(['password' => 'required|min:8|confirmed']);
        $request->user()->update(['password' => Hash::make($request->password)]);
        return response()->json(['message' => 'Contraseña actualizada']);
    }

    public function deleteAccount(Request $request) {
        $request->user()->delete();
        return response()->json(['message' => 'Cuenta eliminada con éxito']);
    }
}

