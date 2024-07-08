<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Theme;

class ThemeController extends Controller
{
    /**
     * Retourne tous les thèmes public par catégorie
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
}
