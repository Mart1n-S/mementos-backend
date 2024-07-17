<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Theme;
use App\Models\Revision;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RevisionController extends Controller
{
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

            // Vérifier s'il y a des révisions pour aujourd'hui
            $revisionsToday = Revision::where('user_id', $userId)
                ->where('dateRevision', '=', Carbon::today())
                ->exists();

            if ($revisionsToday) {
                return response()->json([
                    'themes' => $themes,
                    'nextRevisionInDays' => null // Indique qu'il y a des révisions à faire aujourd'hui
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
                'nextRevisionInDays' => $nextRevisionInDays
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération des thèmes: ' . $e->getMessage()], 500);
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
