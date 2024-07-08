<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Carte;

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
}
