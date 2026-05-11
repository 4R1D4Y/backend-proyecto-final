<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * Controlador de Autenticación y Perfil (AuthController).
 * 
 * Gestiona el ciclo de vida del usuario: registro, acceso mediante tokens,
 * seguridad de la cuenta y políticas de moderación (bloqueos y suspensiones).
 */

class AuthController extends Controller
{
    /**
     * Registro de nuevos usuarios.
     * 
     * Implementa validaciones de unicidad y fortaleza de contraseña, 
     * cifrado mediante Hash y generación de token Bearer inmediato para Auto-Login.
     */
    public function register(Request $request) {
        $data = $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']), // Cifrado Bcrypt por seguridad
            'role' => 'user',
            'status' => 'active'
        ]);

        return response()->json([
            'token' => $user->createToken('api-token')->plainTextToken,
            'user' => $user
        ], 201);
    }

    /**
     * Inicio de sesión y Control de Acceso.
     * 
     * Además de validar credenciales, este método actúa como un middleware de 
     * seguridad que gestiona automáticamente el levantamiento de suspensiones
     * temporales basándose en la fecha actual (now()).
     */
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // Verificación de existencia y coincidencia de Hash de contraseña
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        /**
         * Lógica de Levantamiento Automático de Suspensión:
         * Si el usuario estaba suspendido pero el tiempo de sanción ha expirado,
         * el sistema lo reactiva automáticamente durante el proceso de login.
         */
        if ($user->status === 'suspended' && $user->suspension_time && now()->greaterThan($user->suspension_time)) {
            $user->update([
                'status' => 'active',
                'suspension_time' => null
            ]);
        }

        /**
         * Barrera de Moderación:
         * Deniega el acceso (403 Forbidden) si la cuenta sigue suspendida o bloqueada,
         * devolviendo información detallada sobre la fecha de disponibilidad.
         */
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

        // Generación de Token de sesión única (Sanctum)
        return response()->json([
            'token' => $user->createToken('api-token')->plainTextToken,
            'user' => $user
        ]);
    }

    /**
     * Cierre de sesión (Revocación de Token).
     * Elimina el token de acceso actual de la base de datos para invalidar la sesión.
     */
    public function logout(Request $request) 
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada con éxito']);
    }

    /**
     * Actualización de identidad (Email).
     * Garantiza la integridad de datos evitando correos duplicados, excluyendo el ID actual.
     */
    public function updateEmail(Request $request) {
        $user = $request->user();
        $request->validate(['email' => 'required|email|unique:users,email,' . $user->id]);
        $request->user()->update(['email' => $request->email]);
        return response()->json(['message' => 'Email actualizado']);
    }

    /**
     * Cambio de Contraseña con Doble Validación.
     * Verifica la contraseña actual mediante Hash::check para prevenir cambios
     * no autorizados antes de establecer la nueva clave cifrada.
     */
    public function updatePassword(Request $request) {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'La contraseña actual no es correcta.'], 401);
        }

        $request->user()->update(['password' => Hash::make($request->password)]);
        return response()->json(['message' => 'Contraseña actualizada']);
    }

    /**
     * Eliminación de cuenta (Derecho al olvido).
     * Borrado físico del registro de usuario de la base de datos.
     */
    public function deleteAccount(Request $request) {
        $request->user()->delete();
        return response()->json(['message' => 'Cuenta eliminada con éxito']);
    }
}
