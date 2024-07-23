<?php

namespace App\Http\Controllers\Api;

use App\Models\Carte;
use App\Models\Theme;
use App\Models\Revision;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Schema(
 *   schema="Carte",
 *   type="object",
 *   title="Carte",
 *   description="Schéma d'une carte associée à un thème",
 *   required={"id", "question", "reponse", "theme_id"},
 *   @OA\Property(property="id", type="integer", format="int64", example=66),
 *   @OA\Property(property="question", type="string", example="Quelle est la définition de la dérivée d'une fonction ?"),
 *   @OA\Property(property="reponse", type="string", example="Le taux de variation instantané"),
 *   @OA\Property(property="theme_id", type="integer", format="int64", example=14),
 *   @OA\Property(property="created_at", type="string", format="date-time", example="2024-07-22T17:57:30.000000Z"),
 *   @OA\Property(property="updated_at", type="string", format="date-time", example="2024-07-22T17:57:30.000000Z"),
 *   @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null),
 * )
 */
class CardController extends Controller
{
    /**
     * Retourne toutes les cartes d'un thème public spécifique avec les informations sur le thème et l'utilisateur
     *
     * @param $themeId
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Get(
     *   path="/cartes/{themeId}",
     *   summary="Récupère toutes les cartes d'un thème public spécifique",
     *   description="Retourne toutes les cartes d'un thème public spécifique avec les informations sur le thème et l'utilisateur associé.",
     *   operationId="getCardsByTheme",
     *   tags={"Cartes"},
     *   @OA\Parameter(
     *     name="themeId",
     *     in="path",
     *     required=true,
     *     description="Identifiant du thème pour lequel récupérer les cartes",
     *     @OA\Schema(type="integer", format="int64")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Cartes récupérées avec succès",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(
     *         type="object",
     *         required={"id", "question", "reponse", "theme_id", "created_at", "updated_at", "theme"},
     *         @OA\Property(property="id", type="integer", example=66),
     *         @OA\Property(property="question", type="string", example="Quelle est la définition de la dérivée d'une fonction ?"),
     *         @OA\Property(property="reponse", type="string", example="Le taux de variation instantané"),
     *         @OA\Property(property="theme_id", type="integer", example=14),
     *         @OA\Property(property="created_at", type="string", format="date-time", example="2024-07-22T17:57:30.000000Z"),
     *         @OA\Property(property="updated_at", type="string", format="date-time", example="2024-07-22T17:57:30.000000Z"),
     *         @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true),
     *         @OA\Property(
     *           property="theme",
     *           type="object",
     *           required={"id", "user_id", "category_id", "nom", "couleur", "public", "created_at", "updated_at"},
     *           @OA\Property(property="id", type="integer", example=14),
     *           @OA\Property(property="user_id", type="integer", example=1),
     *           @OA\Property(property="category_id", type="integer", example=7),
     *           @OA\Property(property="nom", type="string", example="Calcul différentiel"),
     *           @OA\Property(property="couleur", type="string", example="#FF4500"),
     *           @OA\Property(property="public", type="boolean", example=true),
     *           @OA\Property(property="created_at", type="string", format="date-time", example="2024-07-22T17:57:29.000000Z"),
     *           @OA\Property(property="updated_at", type="string", format="date-time", example="2024-07-22T17:57:29.000000Z"),
     *           @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true),
     *           @OA\Property(
     *             property="user",
     *             type="object",
     *             required={"id", "pseudo", "email", "email_verified_at", "niveauRevision", "subscribedNotifications", "created_at", "updated_at"},
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="pseudo", type="string", example="Martin"),
     *             @OA\Property(property="email", type="string", format="email", example="martin@gmail.com"),
     *             @OA\Property(property="email_verified_at", type="string", format="date-time", example="2024-07-22T17:57:29.000000Z"),
     *             @OA\Property(property="niveauRevision", type="integer", example=7),
     *             @OA\Property(property="subscribedNotifications", type="integer", example=1),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2024-07-22T17:57:29.000000Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2024-07-23T07:55:24.000000Z"),
     *             @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true)
     *           )
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Thème non trouvé ou n'est pas public",
     *     @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Thème non trouvé ou n'est pas public")
     *     )
     *   )
     * )
     */

    public function getCardsByTheme($themeId)
    {
        // Vérifier d'abord si le thème est public
        $theme = Theme::where('id', $themeId)->where('public', true)->first();

        if (!$theme) {
            return response()->json(['error' => 'Thème non trouvé ou n\'est pas public'], 404);
        }

        // Récupérer les cartes associées au thème public
        $cards = Carte::where('theme_id', $themeId)
            ->with(['theme', 'theme.user'])
            ->get();

        return response()->json($cards);
    }

    /**
     * Retourne toutes les cartes d'un thème spécifique pour l'utilisateur connecté
     *
     * @param $themeId
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Get(
     *   path="/cartes/{themeId}/user",
     *   summary="Récupérer les cartes d'un thème pour l'utilisateur connecté",
     *   description="Retourne toutes les cartes associées à un thème spécifique appartenant à l'utilisateur connecté.",
     *   operationId="getCardsByThemeForUser",
     *   tags={"Cartes"},
     *   security={{"sanctum": {}}},
     *   @OA\Parameter(
     *     name="themeId",
     *     in="path",
     *     required=true,
     *     description="Identifiant du thème pour lequel récupérer les cartes",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Opération réussie",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/Carte")
     *     )
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Accès non autorisé",
     *     @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Accès non autorisé")
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Erreur serveur",
     *     @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Erreur lors de la récupération des cartes")
     *     )
     *   )
     * )
     */
    public function getCardsByThemeForUser($themeId)
    {
        try {
            // Vérifier si le thème existe
            $theme = Theme::findOrFail($themeId);

            // Vérifier si l'utilisateur connecté est bien l'utilisateur associé au thème
            if ($theme->user_id !== Auth::id()) {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }

            // Récupérer les cartes associées au thème
            $cartes = $theme->cartes;

            // Retourner les cartes
            return response()->json($cartes, 200);
        } catch (\Exception $e) {
            // Gestion des erreurs
            return response()->json(['error' => 'Erreur lors de la récupération des cartes'], 500);
        }
    }

    /**
     * Crée une nouvelle carte et l'ajoute aux révisions des utilisateurs révisant une carte existante du thème
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Post(
     *   path="/cartes",
     *   summary="Crée une nouvelle carte pour un thème spécifique",
     *   description="Ajoute une nouvelle carte au thème spécifié, appartenant à l'utilisateur connecté.",
     *   operationId="createCard",
     *   tags={"Cartes"},
     *   security={{"sanctum": {}}},
     *   @OA\RequestBody(
     *       required=true,
     *       description="Données de la nouvelle carte",
     *       @OA\JsonContent(
     *           required={"question", "reponse", "theme_id"},
     *           @OA\Property(property="question", type="string", description="La question de la carte", example="Test question"),
     *           @OA\Property(property="reponse", type="string", description="La réponse de la carte", example="Test réponse"),
     *           @OA\Property(property="theme_id", type="integer", format="int64", description="Identifiant du thème auquel la carte sera ajoutée", example=14)
     *       )
     *   ),
     *   @OA\Response(
     *       response=201,
     *       description="Carte créée avec succès",
     *       @OA\JsonContent(
     *           ref="#/components/schemas/Carte"
     *       )
     *   ),
     *   @OA\Response(
     *       response=422,
     *       description="Erreur de validation des données de la requête",
     *       @OA\JsonContent(
     *           @OA\Property(property="errors", type="object", description="Détail des erreurs de validation")
     *       )
     *   ),
     *   @OA\Response(
     *       response=500,
     *       description="Erreur interne du serveur",
     *       @OA\JsonContent(
     *           @OA\Property(property="error", type="string", example="Erreur lors de la création de la carte")
     *       )
     *   )
     * )
     */
    public function createCard(Request $request)
    {
        $user = Auth::user();

        // Vérifier la validité des données
        $validator = Validator::make($request->all(), [
            'question' => 'required|string|min:1|max:255',
            'reponse' => 'required|string|min:1|max:255',
            'theme_id' => 'required|exists:themes,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Vérifiez si l'utilisateur est propriétaire du thème
            $theme = Theme::where('id', $request->theme_id)->where('user_id', $user->id)->first();
            if (!$theme) {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }

            // Récupérer une carte existante du thème
            $existingCardId = Carte::where('theme_id', $request->theme_id)->value('id');

            // Créer la carte
            $carte = new Carte();
            $carte->question = $request->question;
            $carte->reponse = $request->reponse;
            $carte->theme_id = $request->theme_id;
            $carte->save();

            if ($existingCardId) {
                // Récupérer les utilisateurs révisant cette carte
                $userIds = Revision::where('carte_id', $existingCardId)->pluck('user_id')->unique();

                // Ajouter la nouvelle carte aux révisions des utilisateurs
                foreach ($userIds as $userId) {
                    Revision::create([
                        'user_id' => $userId,
                        'carte_id' => $carte->id,
                        'niveau' => 1,
                        'dateRevision' => now(),
                    ]);
                }
            }

            return response()->json($carte, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la création de la carte'], 500);
        }
    }

    /**
     * Supprimer une carte
     *
     * @param $carteId
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Delete(
     *   path="/cartes/{carteId}",
     *   summary="Supprime une carte spécifique",
     *   description="Supprime une carte appartenant à l'utilisateur connecté, en vérifiant que la carte appartient bien à l'utilisateur.",
     *   operationId="deleteCard",
     *   tags={"Cartes"},
     *   security={{"sanctum": {}}},
     *   @OA\Parameter(
     *     name="carteId",
     *     in="path",
     *     required=true,
     *     description="Identifiant de la carte à supprimer",
     *     @OA\Schema(type="integer", format="int64")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Carte supprimée avec succès",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Carte supprimée avec succès")
     *     )
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Erreur lors de la suppression de la carte ou accès non autorisé",
     *     @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Erreur lors de la suppression de la carte ou accès non autorisé")
     *     )
     *   )
     * )
     */
    public function deleteCard($carteId)
    {
        try {
            // Récupérer l'utilisateur connecté
            $user = Auth::user();

            // Vérifier si la carte existe et appartient à l'utilisateur connecté
            $carte = Carte::where('id', $carteId)
                ->whereHas('theme', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->firstOrFail();

            // Supprimer la carte
            $carte->delete();

            return response()->json(['message' => 'Carte supprimée avec succès'], 200);
        } catch (\Exception $e) {
            // Gestion des erreurs
            return response()->json(['error' => 'Erreur lors de la suppression de la carte ou accès non autorisé'], 403);
        }
    }

    /**
     * Met à jour une carte
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Put(
     *   path="/cartes/{id}",
     *   summary="Mise à jour d'une carte spécifique",
     *   description="Permet à l'utilisateur connecté de mettre à jour une carte spécifique, si celui-ci est le propriétaire du thème auquel la carte appartient.",
     *   operationId="updateCard",
     *   tags={"Cartes"},
     *   security={{"sanctum": {}}},
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="Identifiant de la carte à mettre à jour",
     *     @OA\Schema(type="integer", format="int64")
     *   ),
     *   @OA\RequestBody(
     *       required=true,
     *       description="Données nécessaires pour la mise à jour de la carte",
     *       @OA\JsonContent(
     *           required={"question", "reponse"},
     *           @OA\Property(property="question", type="string", description="La nouvelle question de la carte", example="Test question UPDATED"),
     *           @OA\Property(property="reponse", type="string", description="La nouvelle réponse de la carte", example="Test réponse UPDATED")
     *       )
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="Carte mise à jour avec succès",
     *       @OA\JsonContent(ref="#/components/schemas/Carte")
     *   ),
     *   @OA\Response(
     *       response=403,
     *       description="Accès non autorisé si l'utilisateur n'est pas propriétaire du thème de la carte",
     *       @OA\JsonContent(
     *           @OA\Property(property="error", type="string", example="Accès non autorisé")
     *       )
     *   ),
     *   @OA\Response(
     *       response=404,
     *       description="Carte non trouvée",
     *       @OA\JsonContent(
     *           @OA\Property(property="error", type="string", example="Carte non trouvée")
     *       )
     *   ),
     *   @OA\Response(
     *       response=422,
     *       description="Erreur de validation des données de la requête",
     *       @OA\JsonContent(
     *           @OA\Property(property="messages", type="object", description="Détails des erreurs de validation")
     *       )
     *   ),
     *   @OA\Response(
     *       response=500,
     *       description="Erreur interne du serveur",
     *       @OA\JsonContent(
     *           @OA\Property(property="error", type="string", example="Erreur lors de la mise à jour de la carte ou accès non autorisé")
     *       )
     *   )
     * )
     */
    public function updateCard(Request $request, $id)
    {
        $user = Auth::user();

        // Vérifier la validité des données
        $validator = Validator::make($request->all(), [
            'question' => 'required|string|min:1|max:255',
            'reponse' => 'required|string|min:1|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['messages' => $validator->errors()], 422);
        }

        try {
            // Vérifiez si la carte existe
            $carte = Carte::find($id);
            if (!$carte) {
                return response()->json(['error' => 'Carte non trouvée'], 404);
            }

            // Vérifiez si l'utilisateur est propriétaire du thème auquel appartient la carte
            $theme = Theme::where('id', $carte->theme_id)->where('user_id', $user->id)->first();
            if (!$theme) {
                return response()->json(['error' => 'Accès non autorisé'], 403);
            }

            // Mis à jour de la carte
            $carte->question = $request->input('question');
            $carte->reponse = $request->input('reponse');
            $carte->save();

            return response()->json($carte, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la mise à jour de la carte ou accès non autorisé'], 403);
        }
    }
}
