<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Carte;
use App\Models\Theme;
use App\Models\Revision;
use App\Models\Categorie;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

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

    /**
     * Met à jour un thème pour un utilisateur connecté
     *
     *  @param Request $request
     *  @param $themeId
     *  @return \Illuminate\Http\JsonResponse
     */
    public function updateTheme(Request $request, $themeId)
    {
        $user = Auth::user();

        // Vérifier la validité des données
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|min:3|max:35',
            'category_id' => 'required|exists:categories,id',
            'public' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Vérifiez si le thème existe et appartient à l'utilisateur
            $theme = Theme::where('id', $themeId)->where('user_id', $user->id)->first();

            if (!$theme) {
                return response()->json(['error' => 'Accès non autorisé ou thème non trouvé'], 403);
            }

            // Récupérer la couleur de la catégorie
            $category = Categorie::find($request->input('category_id'));
            if (!$category) {
                return response()->json(['error' => 'Catégorie non trouvée'], 404);
            }

            // Vérifier si le thème est passé de public à privé
            if ($theme->public && !$request->input('public')) {
                // Récupérer toutes les cartes du thème
                $carteIds = Carte::where('theme_id', $themeId)->pluck('id');

                // Supprimer physiquement les révisions associées à ces cartes pour tous les utilisateurs sauf le propriétaire du thème
                Revision::whereIn('carte_id', $carteIds)
                    ->where('user_id', '!=', $user->id)
                    ->delete();
            }

            // Mettre à jour le thème
            $theme->nom = $request->input('nom');
            $theme->category_id = $request->input('category_id');
            $theme->public = $request->input('public');
            $theme->couleur = $category->couleur;
            $theme->save();

            return response()->json($theme, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la mise à jour du thème'], 500);
        }
    }

    /**
     * Supprime un thème pour un utilisateur connecté
     *
     * @param $themeId
     * @return void
     */
    public function deleteTheme($themeId)
    {
        $user = Auth::user();

        try {
            // Vérifiez si le thème existe et appartient à l'utilisateur
            $theme = Theme::where('id', $themeId)->where('user_id', $user->id)->first();

            if (!$theme) {
                return response()->json(['error' => 'Accès non autorisé ou thème non trouvé'], 403);
            }

            // Récupérer toutes les cartes du thème
            $carteIds = Carte::where('theme_id', $themeId)->pluck('id');

            // Supprimer physiquement toutes les révisions associées à ces cartes
            Revision::whereIn('carte_id', $carteIds)->delete();

            // Supprimer logiquement les cartes du thème (soft delete)
            Carte::where('theme_id', $themeId)->delete();

            // Supprimer le thème
            $theme->delete();

            return response()->json(['message' => 'Thème supprimé avec succès'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la suppression du thème'], 500);
        }
    }
}
