<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $resp = User::logearUsuario($request->all());

        if ($resp) {
            return response()->json([
                'success' => true,
                'message' => 'Inicio de sesión exitoso',
                'user' => $resp['user'],
                'token' => $resp['token']
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales incorrectas'
            ], 401);
        }
    }

    public function user(Request $request)
    {
        if ($request->user()) {
            return response()->json($request->user());
        } else {
            return response()->json(['message' => 'Token inválido o expirado.'], 401);
        }
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $user = $request->user();

            // Revocar todos los tokens del usuario
            $user->tokens()->delete();

            return response()->json(['message' => 'Logged out successfully'], 200);
        } else {
            return response()->json(['message' => 'Token inválido o expirado.'], 401);
        }
    }
}
