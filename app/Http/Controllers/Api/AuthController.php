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

        if ($user->status === 'suspended' && $user->suspension_time && now()->greaterThan($user->suspension_time)) {
            $user->update([
                'status' => 'active',
                'suspension_time' => null
            ]);
        }

        if ($user->status !== 'active') {
            $data = [
                'status' => $user->status,
                'message' => $user->status === 'suspended' 
                    ? 'Tu cuenta está suspendida temporalmente.' 
                    : 'Tu cuenta ha sido bloqueada permanentemente.'
            ];

            if ($user->status === 'suspended' && $user->suspension_time) {
                $data['until'] = $user->suspension_time->format('d/m/Y H:i');
            }

            return response()->json($data, 403);
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
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        // --- ESTA ES LA VERIFICACIÓN QUE TE FALTA ---
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'La contraseña actual no es correcta.'
            ], 401); // Devolvemos 401 para que React lo capture como error
        }
        $request->user()->update(['password' => Hash::make($request->password)]);
        return response()->json(['message' => 'Contraseña actualizada']);
    }

    public function deleteAccount(Request $request) {
        $request->user()->delete();
        return response()->json(['message' => 'Cuenta eliminada con éxito']);
    }
}

