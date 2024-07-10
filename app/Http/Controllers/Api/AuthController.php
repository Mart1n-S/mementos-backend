<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Créer un utilisateur
     * @param Request $request
     * @return User 
     */
    public function createUser(Request $request)
    {
        try {
            $request->validate([
                'pseudo' => ['required', 'string', 'max:20', 'min:2'],
                'niveauRevision' => ['required', 'integer', 'min:1', 'max:7'],
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
                'pseudo' => $request->pseudo,
                'email' => $request->email,
                'niveauRevision' => $request->niveauRevision,
                'password' => Hash::make($request->input('password')),
            ]);

            event(new Registered($user));

            // Connecter l'utilisateur
            Auth::login($user);

            // Créer un jeton d'API pour l'utilisateur 
            $token = $user->createToken('access_token')->plainTextToken;

            return response()->json([
                'message' => 'Inscription réussie',
                'user' => $user,
                'token' => $token
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Retourner les erreurs de validation
            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422); // Impossible de traiter l'entité
        } catch (\Illuminate\Database\QueryException $e) {
            // Retourner une erreur si l'email est déjà utilisé
            return response()->json([
                'status' => false,
                'message' => 'Cet email déjà utilisé'
            ], 409);
        } catch (\Throwable $th) {
            // Retourner une erreur si une erreur interne du serveur s'est produite
            return response()->json([
                'status' => false,
                'message' => 'Une erreur interne au serveur s\'est produite'
            ], 500);
        }
    }

    /**
     * Connecter un utilisateur
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
                    'message' => 'Erreur de validation',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email ou mot de passe incorrect',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'Utilisateur connecté avec succès',
                'token' => $user->createToken("access_token")->plainTextToken
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

    /**
     * Demander un nouveau mot de passe
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgot(Request $request)
    {
        // Valide que le champ 'email' est requis et doit être une adresse email valide
        $request->validate(['email' => 'required|email']);

        // Tente de récupérer un utilisateur correspondant à l'email fourni
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Pour ne pas révéler si l'email existe ou non
            return response()->json([], 200);
        }

        // Génère un token aléatoire de 60 caractères
        $token = Str::random(60);

        // Insère ou met à jour le token dans la table 'password_reset_tokens'
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $token,
                'created_at' => Carbon::now()
            ]
        );

        // Construit le lien de réinitialisation avec le token généré
        $resetLink = env('FRONTEND_URL') . '/change-password?token=' . $token;

        // Envoie un email avec le lien de réinitialisation
        Mail::send('emails.password_reset', ['resetLink' => $resetLink], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Réinitialisation de mot de passe');
        });

        return response()->json([], 200);
    }

    /**
     * Réinitialiser le mot de passe d'un utilisateur
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        // Valide les entrées requises : token, email, et les règles pour le nouveau mot de passe
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
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

        // Si la validation échoue, renvoie une réponse JSON avec les erreurs
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Recherche le token de réinitialisation et l'email dans la base de données
        $passwordReset = DB::table('password_reset_tokens')->where([
            ['token', $request->token],
            ['email', $request->email],
        ])->first();

        // Si aucun token correspondant n'est trouvé, renvoie un message d'erreur
        if (!$passwordReset) {
            return response()->json(['message' => 'Token invalide'], 404);
        }

        // Tente de récupérer l'utilisateur à partir de l'email
        $user = User::where('email', $request->email)->first();

        // Si aucun utilisateur n'est trouvé, renvoie un message d'erreur
        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé'], 404);
        }

        // Met à jour le mot de passe de l'utilisateur avec une version hashée du nouveau mot de passe
        $user->password = Hash::make($request->password);
        $user->save();

        // Supprime le token de réinitialisation de la base de données
        DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();

        return response()->json(['message' => 'Mot de passe réinitialisé avec succès'], 200);
    }
}
