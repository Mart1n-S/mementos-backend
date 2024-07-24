<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Carte;
use App\Models\Theme;
use App\Models\Revision;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RevisionController extends Controller
{


    /**
     * Ajoute le thème spécifié aux révisions de l'utilisateur connecté.
     *
     * @param $themeId
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
