<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Carte;
use App\Models\Theme;
use Illuminate\Support\Facades\Auth;

class CardController extends Controller
{
    /**
     * Retourne toutes les cartes d'un thème spécifique avec les informations sur le thème et l'utilisateur
     *
     * @param $themeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCardsByTheme($themeId)
    {
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
            $cartes = $theme->cartes; // Assurez-vous d'avoir défini la relation dans votre modèle Theme

            // Retourner les cartes
            return response()->json($cartes, 200);
        } catch (\Exception $e) {
            // Gestion des erreurs
            return response()->json(['error' => 'Erreur lors de la récupération des cartes'], 500);
        }
    }

    /**
     * Supprimer une carte
     *
     * @param $carteId
     * @return \Illuminate\Http\JsonResponse
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

            // Retourner une réponse réussie
            return response()->json(['message' => 'Carte supprimée avec succès'], 200);
        } catch (\Exception $e) {
            // Gestion des erreurs
            return response()->json(['error' => 'Erreur lors de la suppression de la carte ou accès non autorisé'], 403);
        }
    }
}
