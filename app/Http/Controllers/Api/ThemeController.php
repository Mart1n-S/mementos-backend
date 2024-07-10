<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Theme;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ThemeController extends Controller
{
    /**
     * Retourne tous les thèmes publiques d'une catégorie
     *
     * @param $categorie
     * @return \Illuminate\Http\JsonResponse
     */
    public function getThemesByCategorie($categorie)
    {
        $themes = Theme::whereHas('categorie', function ($query) use ($categorie) {
            $query->where('nom', $categorie);
        })->where('public', true)->get();

        return response()->json($themes);
    }

    /**
     * Retourne tous les thèmes d'un utilisateur
     *
     * @param $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getThemesByUser($userId)
    {
        try {
            // Vérifier si l'utilisateur connecté correspond à l'utilisateur demandé
            if (auth()->id() != $userId) {
                return response()->json(['error' => 'Non autorisé'], 403);
            }

            // Vérifier si l'utilisateur existe
            $user = User::findOrFail($userId);

            // Récupérer les thèmes de l'utilisateur
            $themes = Theme::where('user_id', $userId)->get();

            // Retourner les thèmes
            return response()->json($themes, 200);
        } catch (\Exception $e) {
            // Gestion des erreurs
            return response()->json(['error' => 'Erreur lors de la récupération des thèmes'], 500);
        }
    }

    /**
     * Retourne un thème spécifique
     *
     * @param $themeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getThemeById($themeId)
    {
        try {
            // Récupérer l'utilisateur connecté
            $user = Auth::user();

            // Vérifier si le thème existe et appartient à l'utilisateur connecté
            $theme = Theme::where('id', $themeId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            // Retourner le thème
            return response()->json($theme, 200);
        } catch (\Exception $e) {
            // Gestion des erreurs
            return response()->json(['error' => 'Erreur lors de la récupération du thème ou accès non autorisé'], 403);
        }
    }
}
