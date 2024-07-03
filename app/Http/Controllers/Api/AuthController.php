<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create User
     * @param Request $request
     * @return User 
     */
    public function createUser(Request $request)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
                'password' => [
                    'required',
                    'confirmed',
                    'min:8',
                    'regex:/[a-z]/',
                    'regex:/[A-Z]/',
                    'regex:/[0-9]/',
                    'regex:/[@$!%*#?&]/',
                ],
            ]);
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->input('password')),
            ]);

            event(new Registered($user));

            // Connecter l'utilisateur
            Auth::login($user);

            // Créer un jeton d'API pour l'utilisateur (nécessite Laravel Sanctum ou Passport)
            $token = $user->createToken('access_token')->plainTextToken;

            return response()->json([
                'message' => 'Inscription réussie',
                'user' => $user,
                'token' => $token
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422); // Unprocessable Entity for validation errors
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database errors like unique constraint
            return response()->json([
                'status' => false,
                'message' => 'Cet email déjà utilisé'
            ], 409); // Conflict for email already used
        } catch (\Throwable $th) {
            // General error handling
            return response()->json([
                'status' => false,
                'message' => 'Une erreur interne au serveur s\'est produite'
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
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Déconnecter l'utilisateur en révoquant le token actuel.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Révoquer le token actuel
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnexion réussie'], 200);
    }
}
