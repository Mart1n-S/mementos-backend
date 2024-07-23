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

/**
 *  @OA\Schema(
 *   schema="User",
 *   type="object",
 *   title="User",
 *   description="Schema d'un user",
 *   required={"id", "pseudo", "email", "niveauRevision", "subscribedNotifications", "password"},
 *   @OA\Property(property="id", type="integer", format="int64", example="1"),
 *   @OA\Property(property="pseudo", type="string", example="johndoe"),
 *   @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *   @OA\Property(property="email_verified_at", type="string", format="date-time", example="2021-01-01T00:00:00Z"),
 *   @OA\Property(property="niveauRevision", type="integer", example=3),
 *   @OA\Property(property="subscribedNotifications", type="boolean", example=true),
 *   @OA\Property(property="created_at", type="string", format="date-time", example="2021-01-01T00:00:00Z"),
 *   @OA\Property(property="updated_at", type="string", format="date-time", example="2021-01-02T00:00:00Z"),
 *   @OA\Property(property="password", type="string", format="password", example="YourSecurePassword")
 * )
 */
class AuthController extends Controller
{
    /**
     * Créer un utilisateur
     * @param Request $request
     * @return User 
     */
    /**  @OA\Post(
     *   path="/create-user",
     *   summary="Créer un utilisateur",
     *   description="Enregistre un nouvel utilisateur dans la base de données et retourne les informations de l'utilisateur avec un token d'accès.",
     *   operationId="createUser",
     *   tags={"Auth"},
     *   @OA\RequestBody(
     *       required=true,
     *       description="Informations nécessaires pour créer un nouvel utilisateur",
     *       @OA\JsonContent(
     *           required={"pseudo", "email", "password", "niveauRevision", "password_confirmation"},
     *           @OA\Property(property="pseudo", type="string", example="John"),
     *           @OA\Property(property="niveauRevision", type="integer", example=7),
     *           @OA\Property(property="email", type="string", format="email", example="john@exemple.com"),
     *           @OA\Property(property="password", type="string", format="password", example="Pa$$w0rd!"),
     *           @OA\Property(property="password_confirmation", type="string", format="password", example="Pa$$w0rd!")
     *       )
     *   ),
     *   @OA\Response(
     *       response=201,
     *       description="Inscription réussie",
     *       @OA\JsonContent(
     *           @OA\Property(property="message", type="string", example="Inscription réussie"),
     *           @OA\Property(property="user", ref="#/components/schemas/User"),
     *           @OA\Property(property="token", type="string", example="2|RI1TW2GCppDXR09fE0qyVufn8PBZIYeUk6ohC9ruf180affb"),
     *       )
     *   ),
     *   @OA\Response(
     *       response=422,
     *       description="Erreur de validation",
     *       @OA\JsonContent(
     *           @OA\Property(property="status", type="boolean", example=false),
     *           @OA\Property(
     *               property="errors",
     *               type="object",
     *               @OA\Property(
     *                   property="pseudo",
     *                   type="array",
     *                   @OA\Items(type="string", example="The pseudo field must be at least 2 characters.")
     *               ),
     *               @OA\Property(
     *                   property="niveauRevision",
     *                   type="array",
     *                   @OA\Items(type="string", example="The niveau revision field must not be greater than 7.")
     *               ),
     *               @OA\Property(
     *                   property="email",
     *                   type="array",
     *                   @OA\Items(type="string", example="The email has already been taken.")
     *               ),
     *               @OA\Property(
     *                   property="password",
     *                   type="array",
     *                   @OA\Items(
     *                       type="string",
     *                       example="The password field must be at least 8 characters."
     *                   ),
     *                   @OA\Items(
     *                       type="string",
     *                       example="The password field format is invalid."
     *                   )
     *               )
     *           )
     *       )
     *   ),
     *   @OA\Response(
     *       response=500,
     *       description="Erreur interne du serveur",
     *       @OA\JsonContent(
     *           @OA\Property(property="status", type="boolean", example=false),
     *           @OA\Property(property="message", type="string", example="Une erreur interne au serveur s'est produite")
     *       )
     *   )
     * )
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

    /**
     * @OA\Post(
     *   path="/login",
     *   summary="Connecter un utilisateur",
     *   description="Permet à un utilisateur de se connecter en utilisant ses credentials et retourne un token d'accès en cas de succès.",
     *   operationId="loginUser",
     *   tags={"Auth"},
     *   @OA\RequestBody(
     *       required=true,
     *       description="Les identifiants de connexion nécessaires",
     *       @OA\JsonContent(
     *           required={"email", "password"},
     *           @OA\Property(property="email", type="string", format="email", example="john@exemple.com"),
     *           @OA\Property(property="password", type="string", format="password", example="Pa$$w0rd!")
     *       )
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="Connexion réussie",
     *       @OA\JsonContent(
     *           @OA\Property(property="status", type="string", example="true"),
     *           @OA\Property(property="message", type="string", example="Utilisateur connecté avec succès"),
     *           @OA\Property(property="token", type="string", example="6|pnlfhRkVBhmFiJKV1Qi...")
     *       )
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Erreur d'authentification",
     *       @OA\JsonContent(
     *           @OA\Property(property="status", type="boolean", example=false),
     *           @OA\Property(property="message", type="string", example="Email ou mot de passe incorrect")
     *       )
     *   ),
     *   @OA\Response(
     *       response=500,
     *       description="Erreur interne du serveur",
     *       @OA\JsonContent(
     *           @OA\Property(property="status", type="boolean", example=false),
     *           @OA\Property(property="message", type="string", example="Une erreur interne au serveur s'est produite")
     *       )
     *   )
     * )
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

    /**
     * @OA\Post(
     *   path="/logout",
     *   summary="Déconnecter l'utilisateur",
     *   description="Déconnecte l'utilisateur en révoquant son token d'accès actuel. Nécessite un token d'accès valide pour l'authentification.",
     *   operationId="logoutUser",
     *   tags={"Auth"},
     *   security={{"sanctum": {}}},
     *   @OA\Response(
     *       response=200,
     *       description="Déconnexion réussie",
     *       @OA\JsonContent(
     *           @OA\Property(property="message", type="string", example="Déconnexion réussie")
     *       )
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="Non autorisé",
     *       @OA\JsonContent(
     *           @OA\Property(property="message", type="string", example="Unauthenticated.")
     *       )
     *   )
     * )
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
    /**
     * @OA\Post(
     *   path="/forgot-password",
     *   summary="Demande de réinitialisation de mot de passe",
     *   description="Envoie un lien de réinitialisation de mot de passe à l'email fourni si l'utilisateur est enregistré. </br> Ne révèle pas si l'email est enregistré pour des raisons de sécurité.",
     *   operationId="forgotPassword",
     *   tags={"Auth"},
     *   @OA\RequestBody(
     *       required=true,
     *       description="Email de l'utilisateur pour lequel la réinitialisation du mot de passe est demandée",
     *       @OA\JsonContent(
     *           required={"email"},
     *           @OA\Property(property="email", type="string", format="email", example="john@exemple.com")
     *       )
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="Email de réinitialisation envoyé si l'utilisateur est trouvé, réponse vide sinon. La réponse ne révèle pas si l'email est enregistré. </br> <strong>Uniquement pour la présentation</strong>",
     *       @OA\JsonContent(
     *           @OA\Property(property="token", type="string", example="kuk2dUqMYNs3dA...")
     *       )
     *   ),
     *   @OA\Response(
     *       response=422,
     *       description="Erreur de validation des données de la requête",
     *       @OA\JsonContent(
     *           @OA\Property(property="message", type="string", example="The email field must be a valid email address."),
     *           @OA\Property(
     *               property="errors",
     *               type="object",
     *               @OA\Property(
     *                   property="email",
     *                   type="array",
     *                   @OA\Items(type="string", example="The email field must be a valid email address.")
     *               )
     *           )
     *       )
     *   ),
     *   @OA\Response(
     *       response=500,
     *       description="Erreur interne du serveur",
     *       @OA\JsonContent(
     *           @OA\Property(property="message", type="string", example="Une erreur interne est survenue.")
     *       )
     *   )
     * )
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

        // return response()->json([], 200);

        // Unique pour la présentation
        return response()->json(['token' => $token], 200);
    }

    /**
     * Réinitialiser le mot de passe d'un utilisateur
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Post(
     *   path="/reset-password",
     *   summary="Réinitialiser le mot de passe d'un utilisateur",
     *   description="Permet à un utilisateur de réinitialiser son mot de passe en utilisant un token de réinitialisation. Le token doit être valide et l'email doit correspondre à un utilisateur existant. </br> <strong>Remplacez le token par celui récupéré avec 'forgot-password'.</strong>",
     *   operationId="resetPassword",
     *   tags={"Auth"},
     *   @OA\RequestBody(
     *       required=true,
     *       description="Données nécessaires pour réinitialiser le mot de passe.",
     *       @OA\JsonContent(
     *           required={"token", "email", "password", "password_confirmation"},
     *           @OA\Property(property="token", type="string", example="naANvEQXt4zB8...(Remplacez le token par celui récupéré avec l'option 'forgot-password')"),
     *           @OA\Property(property="email", type="string", format="email", example="john@exemple.com"),
     *           @OA\Property(property="password", type="string", format="password", example="J0hnEx@ample13"),
     *           @OA\Property(property="password_confirmation", type="string", format="password", example="J0hnEx@ample13")
     *       )
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="Mot de passe réinitialisé avec succès",
     *       @OA\JsonContent(
     *           @OA\Property(property="message", type="string", example="Mot de passe réinitialisé avec succès")
     *       )
     *   ),
     *   @OA\Response(
     *       response=422,
     *       description="Erreur de validation des données de la requête",
     *       @OA\JsonContent(
     *           type="object",
     *           @OA\Property(property="errors", type="object", description="Détails des erreurs de validation")
     *       )
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Token invalide ou Utilisateur non trouvé",
     *       @OA\JsonContent(
     *           @OA\Property(property="message", type="string", example="Token invalide ou Utilisateur non trouvé")
     *       )
     *   ),
     *   @OA\Response(
     *       response=500,
     *       description="Erreur interne du serveur",
     *       @OA\JsonContent(
     *           @OA\Property(property="message", type="string", example="Une erreur interne est survenue")
     *       )
     *   )
     * )
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
