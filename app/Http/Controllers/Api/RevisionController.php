<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Carte;
use App\Models\Theme;
use App\Models\Revision;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *   schema="RevisionCarte",
 *   type="object",
 *   title="Révision",
 *   description="Détails d'une carte spécifique dans le cadre de sa révision",
 *   required={"id", "user_id", "carte_id", "niveau"},
 *   @OA\Property(property="id", type="integer", format="int64", example=66),
 *   @OA\Property(property="user_id", type="integer", format="int64", example=1),
 *   @OA\Property(property="carte_id", type="integer", format="int64", example=16),
 *   @OA\Property(property="niveau", type="integer", example=7),
 *   @OA\Property(property="created_at", type="string", format="date-time", example="2024-07-22T17:57:30.000000Z"),
 *   @OA\Property(property="updated_at", type="string", format="date-time", example="2024-07-22T17:57:30.000000Z"),
 * )
 */
class RevisionController extends Controller
{


    /**
     * Ajoute le thème spécifié aux révisions de l'utilisateur connecté.
     *
     * @param $themeId
     */
    /**
     * @OA\Post(
     *   path="/revision/{themeId}",
     *   summary="Créer une révision pour un thème",
     *   description="Ajoute toutes les cartes d'un thème spécifique aux révisions de l'utilisateur connecté, à condition que le thème soit public ou appartienne à l'utilisateur.",
     *   operationId="createRevision",
     *   tags={"Revision"},
     *   security={{"sanctum": {}}},
     *   @OA\Parameter(
     *     name="themeId",
     *     in="path",
     *     required=true,
     *     description="L'identifiant du thème pour lequel créer une révision",
     *     @OA\Schema(type="integer", format="int64")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Révision créée avec succès, renvoie les détails du thème révisé.",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/Theme")
     *     )
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Accès refusé si le thème est privé et n'appartient pas à l'utilisateur",
     *     @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Accès refusé. Le thème et privé.")
     *     )
     *   ),
     *   @OA\Response(
     *     response=409,
     *     description="Thème déjà révisé par l'utilisateur",
     *     @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Thème déjà révisé.")
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Erreur serveur lors de la tentative d'ajout du thème aux révisions",
     *     @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Erreur lors de l'ajout du thème à vos révisions: message d'erreur")
     *     )
     *   )
     * )
     */

    public function createRevision($themeId)
    {
        try {
            $user = Auth::user();

            // Vérifier si le thème existe
            $theme = Theme::findOrFail($themeId);

            // Vérifier si le thème est public ou appartient à l'utilisateur connecté
            if (!$theme->public && $theme->user_id !== $user->id) {
                return response()->json(['error' => 'Accès refusé. Le thème et privé.'], 403);
            }

            // Vérifier si le thème est déjà révisé par l'utilisateur
            $alreadyRevised = Revision::where('user_id', $user->id)
                ->whereHas('carte', function ($query) use ($themeId) {
                    $query->where('theme_id', $themeId);
                })
                ->exists();

            if ($alreadyRevised) {
                return response()->json(['error' => 'Thème déjà révisé.'], 409);
            }

            // Récupérer toutes les cartes du thème
            $cartes = Carte::where('theme_id', $themeId)->get();

            // Ajouter des cartes du thème aux révisions
            foreach ($cartes as $carte) {
                Revision::create([
                    'user_id' => $user->id,
                    'carte_id' => $carte->id,
                    'niveau' => 1,
                    'dateRevision' => Carbon::today(),
                    'dateDerniereRevision' => null
                ]);
            }

            return response()->json([$theme]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de l\'ajout du thème à vos révisions: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Récupère les thèmes révisés par l'utilisateur connecté.
     *
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Get(
     *   path="/revision/{userId}",
     *   summary="Récupérer les révisions d'un utilisateur",
     *   description="Récupère toutes les révisions associées à un utilisateur spécifique, incluant des détails sur les thèmes et les cartes révisées.",
     *   operationId="fetchUserRevision",
     *   tags={"Revision"},
     *   security={{"sanctum": {}}},
     *   @OA\Parameter(
     *     name="userId",
     *     in="path",
     *     required=true,
     *     description="Identifiant de l'utilisateur pour lequel récupérer les révisions",
     *     @OA\Schema(type="integer", format="int64")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Révisions de l'utilisateur récupérées avec succès",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="themes",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/Theme")
     *       ),
     *       @OA\Property(property="nextRevisionInDays", type="integer", nullable=true, description="Jours jusqu'à la prochaine révision, null si des révisions sont dues aujourd'hui"),
     *       @OA\Property(property="cardRevisionDisponible", type="integer", example=5, description="Nombre de cartes disponibles pour la révision aujourd'hui"),
     *       @OA\Property(
     *         property="detailCards",
     *         type="array",
     *         @OA\Items(
     *           type="object",
     *           @OA\Property(property="id", type="integer", format="int64", example=33),
     *           @OA\Property(property="user_id", type="integer", format="int64", example=21),
     *           @OA\Property(property="carte_id", type="integer", format="int64", example=1),
     *           @OA\Property(property="niveau", type="integer", example=1),
     *           @OA\Property(property="dateRevision", type="string", format="date", example="2024-07-23"),
     *           @OA\Property(property="dateDerniereRevision", type="string", format="date", nullable=true),
     *           @OA\Property(property="created_at", type="string", format="date-time", example="2024-07-23T10:51:47.000000Z"),
     *           @OA\Property(property="updated_at", type="string", format="date-time", example="2024-07-23T10:51:47.000000Z"),
     *           @OA\Property(
     *             property="carte",
     *             type="object",
     *             @OA\Property(property="id", type="integer", format="int64", example=1),
     *             @OA\Property(property="question", type="string", example="Quelle invention a révolutionné l'Europe pendant la Renaissance ?"),
     *             @OA\Property(property="reponse", type="string", example="L'imprimerie"),
     *             @OA\Property(property="theme_id", type="integer", format="int64", example=1),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2024-07-22T17:57:30.000000Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2024-07-22T17:57:30.000000Z"),
     *             @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true),
     *             @OA\Property(
     *               property="theme",
     *               type="object",
     *               ref="#/components/schemas/Theme"
     *             )
     *           )
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Accès non autorisé",
     *     @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Unauthorized")
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Erreur serveur lors de la récupération des révisions",
     *     @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Erreur lors de la récupération des thèmes")
     *     )
     *   )
     * )
     */
    public function fetchUserRevision($userId)
    {
        try {
            $user = Auth::user();

            // S'assurer que l'utilisateur connecté correspond à l'utilisateur demandé
            if ($user->id !== (int)$userId) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Récupérer les thèmes révisés par l'utilisateur
            $themes = Theme::whereHas('cartes.revisions', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->distinct()->get();

            // Compter les révisions pour aujourd'hui
            $revisionsTodayCount = Revision::where('user_id', $userId)
                ->where('dateRevision', '=', Carbon::today())
                ->count();

            $detailCards = Revision::where('user_id', $userId)
                ->with(['carte.theme'])
                ->get();

            if ($revisionsTodayCount > 0) {
                return response()->json([
                    'themes' => $themes,
                    'nextRevisionInDays' => null, // Indique qu'il y a des révisions à faire aujourd'hui
                    'cardRevisionDisponible' => $revisionsTodayCount,
                    'detailCards' => $detailCards
                ]);
            }

            // Calculer la date de la prochaine révision
            $nextRevision = Revision::where('user_id', $userId)
                ->where('dateRevision', '>', Carbon::today())
                ->orderBy('dateRevision', 'asc')
                ->first();

            $nextRevisionInDays = null;
            if ($nextRevision) {
                $nextRevisionInDays = Carbon::today()->diffInDays(Carbon::parse($nextRevision->dateRevision));
            }

            return response()->json([
                'themes' => $themes,
                'nextRevisionInDays' => $nextRevisionInDays,
                'cardRevisionDisponible' => 0
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération des thèmes: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Récupérer le nombre de cartes à réviser pour aujourd'hui choisi par l'utilisateur connecté.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Post(
     *   path="/revision/nombre-cartes",
     *   summary="Sélectionner le nombre de cartes à réviser pour aujourd'hui",
     *   description="Permet à l'utilisateur connecté de récupérer un nombre spécifique de cartes à réviser pour la journée, mélange les cartes disponibles et les renvoie.",
     *   operationId="getCardsForToday",
     *   tags={"Revision"},
     *   security={{"sanctum": {}}},
     *   @OA\RequestBody(
     *       required=true,
     *       description="Nombre de cartes à réviser pour la journée",
     *       @OA\JsonContent(
     *           required={"number_of_cards"},
     *           @OA\Property(property="number_of_cards", type="integer", example=5, description="Le nombre de cartes que l'utilisateur souhaite réviser aujourd'hui")
     *       )
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="Retourne les cartes à réviser pour la journée",
     *       @OA\JsonContent(
     *           type="array",
     *           @OA\Items(
     *               type="object",
     *               @OA\Property(property="id", type="integer", example=1),
     *               @OA\Property(property="question", type="string", example="Quelle est la définition de la dérivée d'une fonction ?"),
     *               @OA\Property(property="reponse", type="string", example="Le taux de variation instantané"),
     *               @OA\Property(
     *                   property="theme",
     *                   type="object",
     *                   ref="#/components/schemas/Theme"
     *               )
     *           )
     *       )
     *   ),
     *   @OA\Response(
     *       response=400,
     *       description="Erreur de validation: le nombre de cartes demandé est supérieur au nombre de cartes disponibles.",
     *       @OA\JsonContent(
     *           @OA\Property(property="error", type="string", example="Le nombre de cartes demandé est supérieur au nombre de cartes disponibles.")
     *       )
     *   ),
     *   @OA\Response(
     *       response=500,
     *       description="Erreur interne du serveur",
     *       @OA\JsonContent(
     *           @OA\Property(property="error", type="string", example="Erreur lors de la récupération des cartes à réviser.")
     *       )
     *   )
     * )
     */
    public function getCardsForToday(Request $request)
    {
        $request->validate([
            'number_of_cards' => 'required|integer|min:1',
        ]);

        try {
            $user = Auth::user();
            $numberOfCards = $request->number_of_cards;

            // Récupérer les révisions pour aujourd'hui
            $revisionsToday = Revision::where('user_id', $user->id)
                ->where('dateRevision', '=', Carbon::today())
                ->get();

            $availableCardsCount = $revisionsToday->count();

            if ($numberOfCards > $availableCardsCount) {
                return response()->json(['error' => 'Le nombre de cartes demandé est supérieur au nombre de cartes disponibles.'], 400);
            }

            // Mélanger les révisions et sélectionner le nombre de cartes demandé
            $revisionsToday = $revisionsToday->shuffle()->take($numberOfCards);

            $cards = $revisionsToday->map(function ($revision) {
                return [
                    'id' => $revision->carte->id,
                    'question' => $revision->carte->question,
                    'reponse' => $revision->carte->reponse,
                    'theme' => $revision->carte->theme,
                ];
            });

            return response()->json($cards);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération des cartes à réviser: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mettre à jour le niveau d'une carte qui est révisée.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Put(
     *   path="/revision",
     *   summary="Mettre à jour le niveau d'une carte révisée",
     *   operationId="updateRevision",
     *   tags={"Revision"},
     *   security={{"sanctum": {}}},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Données nécessaires pour mettre à jour la révision",
     *     @OA\JsonContent(
     *       required={"id", "is_correct"},
     *       @OA\Property(property="id", type="integer", description="Identifiant de la carte à réviser", example=111),
     *       @OA\Property(property="is_correct", type="boolean", description="Indique si la réponse était correcte", example=true)
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Révision mise à jour avec succès",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=true)
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Erreur lors de la mise à jour de la révision",
     *     @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Erreur lors de la mise à jour de la révision: détail de l'erreur")
     *     )
     *   )
     * )
     */
    public function updateRevision(Request $request)
    {
        try {


            $request->validate([
                'id' => 'required|integer|exists:revisions,carte_id',
                'is_correct' => 'required|boolean',
            ]);

            $user = Auth::user();
            $carteId = $request->id;
            $isCorrect = $request->is_correct;

            $revision = Revision::where('user_id', $user->id)
                ->where('carte_id', $carteId)
                ->firstOrFail();

            // Niveau maximum en fonction du niveau de révision de l'utilisateur
            $maxNiveau = min($user->niveauRevision, 7);

            // Mise à jour du niveau en fonction de la réponse
            if ($isCorrect) {
                $newNiveau = min($revision->niveau + 1, $maxNiveau);
            } else {
                $newNiveau = 1;
            }

            // Calcul de la nouvelle date de révision en fonction du niveau
            $newDateRevision = Carbon::today()->addDays(2 ** ($newNiveau - 1));

            // Mise à jour de la révision
            $revision->niveau = $newNiveau;
            $revision->dateRevision = $newDateRevision;
            $revision->dateDerniereRevision = Carbon::today();
            $revision->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la mise à jour de la révision: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Supprimer un thème des révisions.
     *
     * @param int $themeId
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Delete(
     *   path="/revision/{themeId}",
     *   summary="Supprimer un thème des révisions de l'utilisateur connecté",
     *   description="Supprime toutes les révisions associées à un thème spécifique pour l'utilisateur connecté.",
     *   operationId="deleteThemeFromRevision",
     *   tags={"Revision"},
     *   security={{"sanctum": {}}},
     *   @OA\Parameter(
     *     name="themeId",
     *     description="Identifiant du thème à supprimer des révisions",
     *     required=true,
     *     in="path",
     *     @OA\Schema(type="integer", format="int64")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Thème supprimé des révisions avec succès",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Thème supprimé des révisions avec succès")
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Erreur serveur lors de la suppression du thème des révisions",
     *     @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Erreur lors de la suppression du thème des révisions: <error message>")
     *     )
     *   )
     * )
     */
    public function deleteThemeFromRevision($themeId)
    {
        try {
            $user = Auth::user();
            $revisions = Revision::whereHas('carte', function ($query) use ($themeId) {
                $query->where('theme_id', $themeId);
            })->where('user_id', $user->id)->delete();

            return response()->json(['message' => 'Thème supprimé des révisions avec succès'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la suppression du thème des révisions: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Supprimer toutes les révisions de l'utilisateur connecté.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Delete(
     *   path="/revision/deleteAll",
     *   summary="Supprimer toutes les révisions de l'utilisateur connecté",
     *   description="Effectue une suppression physique de toutes les révisions associées à l'utilisateur connecté.",
     *   operationId="deleteAllRevision",
     *   tags={"Revision"},
     *   security={{"sanctum": {}}},
     *   @OA\Response(
     *     response=200,
     *     description="Toutes les révisions ont été supprimées avec succès",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Toutes les révisions ont été supprimées avec succès")
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Erreur serveur lors de la suppression des révisions",
     *     @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Erreur lors de la suppression des révisions: <error message>")
     *     )
     *   )
     * )
     */
    public function deleteAllRevision()
    {
        try {
            $user = Auth::user();

            // Suppression physique des révisions de l'utilisateur
            Revision::where('user_id', $user->id)->delete();

            return response()->json(['message' => 'Toutes les révisions ont été supprimées avec succès'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la suppression des révisions: ' . $e->getMessage()], 500);
        }
    }
}
