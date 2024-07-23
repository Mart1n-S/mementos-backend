<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

/**
 * @OA\Get(
 *   path="/user",
 *   summary="Récupérer les informations de l'utilisateur connecté",
 *   description="Permet de récupérer les informations de l'utilisateur connecté en utilisant le token d'accès fourni dans les headers. Pensez à créer l'utilisateur et à le connecter pour récupérer le token, puis à le configurer dans Authorize",
 *   operationId="getUserInfo",
 *   tags={"User"},
 *   security={{"sanctum": {}}},
 *   @OA\Response(
 *       response=200,
 *       description="Informations de l'utilisateur récupérées avec succès",
 *       @OA\JsonContent(
 *           @OA\Property(property="id", type="integer", example=1),
 *           @OA\Property(property="pseudo", type="string", example="Martin"),
 *           @OA\Property(property="email", type="string", example="martin@gmail.com"),
 *           @OA\Property(property="email_verified_at", type="string", format="date-time", example="2024-07-22T17:57:29.000000Z"),
 *           @OA\Property(property="niveauRevision", type="integer", example=7),
 *           @OA\Property(property="subscribedNotifications", type="boolean", example=false),
 *           @OA\Property(property="created_at", type="string", format="date-time", example="2024-07-22T17:57:29.000000Z"),
 *           @OA\Property(property="updated_at", type="string", format="date-time", example="2024-07-22T17:57:29.000000Z"),
 *           @OA\Property(property="deleted_at", type="string", nullable=true, example=null)
 *       )
 *   ),
 *   @OA\Response(
 *       response=401,
 *       description="Non autorisé",
 *       @OA\JsonContent(
 *           @OA\Property(property="message", type="string", example="Non autorisé")
 *       )
 *   ),
 *   @OA\Response(
 *       response=500,
 *       description="Erreur interne du serveur",
 *       @OA\JsonContent(
 *           @OA\Property(property="message", type="string", example="Une erreur interne au serveur s'est produite")
 *       )
 *   )
 * )
 */
class UserController extends Controller
{
    /**
     * Mettre à jour un utilisateur
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Put(
     *   path="/user/{id}",
     *   summary="Mettre à jour les informations de l'utilisateur",
     *   description="Met à jour les informations d'un utilisateur spécifique par ID. L'utilisateur doit être authentifié et correspondre à l'ID spécifié.",
     *   tags={"User"},
     *   operationId="updateUser",
     *   security={{ "sanctum": {} }},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="ID de l'utilisateur à mettre à jour",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     description="Données de l'utilisateur à mettre à jour",
     *     @OA\JsonContent(
     *       required={"pseudo", "niveauRevision"},
     *       @OA\Property(property="pseudo", type="string", example="Martin"),
     *       @OA\Property(property="niveauRevision", type="integer", example=7)
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Utilisateur mis à jour avec succès",
     *     @OA\JsonContent(
     *       @OA\Property(property="status", type="boolean", example=true),
     *       @OA\Property(property="message", type="string", example="Utilisateur mis à jour avec succès")
     *     )
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Accès refusé pour modifier les informations d'un autre utilisateur",
     *     @OA\JsonContent(
     *       @OA\Property(property="status", type="boolean", example=false),
     *       @OA\Property(property="message", type="string", example="Vous ne pouvez pas modifier les informations d'un autre utilisateur.")
     *     )
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Erreur de validation des données",
     *     @OA\JsonContent(
     *       @OA\Property(property="status", type="boolean", example=false),
     *       @OA\Property(property="message", type="string", example="Veuillez vérifier les erreurs de validation"),
     *       @OA\Property(property="errors", type="object")
     *     )
     *   )
     * )
     */
    public function update(Request $request, $id)
    {
        /** @var User $user */
        $user = Auth::user();

        // Vérifiez que l'utilisateur connecté correspond à l'ID dans l'URL
        if ($user->id != $id) {
            return response()->json([
                'status' => false,
                'message' => 'Vous ne pouvez pas modifier les informations d\'un autre utilisateur.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'pseudo' => 'required|string|min:2|max:20',
            'niveauRevision' => 'required|integer|min:1|max:7',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Veuillez vérifier les erreurs de validation',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user->pseudo = $request->pseudo;
        $user->niveauRevision = $request->niveauRevision;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Utilisateur mis à jour avec succès',
        ]);
    }

    /**
     * Mettre à jour l'état de l'abonnement aux notifications
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Post(
     *   path="/user/subscription",
     *   summary="Mettre à jour l'état de l'abonnement de notifications de l'utilisateur",
     *   description="Met à jour l'abonnement aux notifications push de l'utilisateur authentifié.",
     *   tags={"User"},
     *   operationId="updateSubscription",
     *   security={{ "sanctum": {} }},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Statut de l'abonnement à mettre à jour",
     *     @OA\JsonContent(
     *       required={"isSubscribed"},
     *       @OA\Property(property="isSubscribed", type="boolean", example=true)
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Abonnement mis à jour avec succès",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=true)
     *     )
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Erreur de validation des données",
     *     @OA\JsonContent(
     *       @OA\Property(property="status", type="boolean", example=false),
     *       @OA\Property(property="errors", type="object")
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Erreur interne du serveur",
     *     @OA\JsonContent(
     *       @OA\Property(property="status", type="boolean", example=false),
     *       @OA\Property(property="message", type="string", example="Erreur de la base de données lors de la mise à jour de l'abonnement.")
     *     )
     *   )
     * )
     */
    public function updateSubscription(Request $request)
    {
        try {
            // Valider la requête
            $request->validate([
                'isSubscribed' => 'required|boolean',
            ]);

            // Récupérer l'utilisateur authentifié
            $user = $request->user();

            // Mettre à jour le statut de l'abonnement aux notifications
            $user->subscribedNotifications = $request->isSubscribed;
            $user->save();

            return response()->json(['success' => true]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Retourner les erreurs de validation
            return response()->json([
                'status' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            // Retourner une erreur de requête de base de données
            return response()->json([
                'status' => false,
                'message' => 'Erreur de la base de données lors de la mise à jour de l\'abonnement.'
            ], 500);
        } catch (\Exception $e) {
            // Retourner une erreur générique
            return response()->json([
                'status' => false,
                'message' => 'Une erreur interne est survenue.'
            ], 500);
        }
    }
}
