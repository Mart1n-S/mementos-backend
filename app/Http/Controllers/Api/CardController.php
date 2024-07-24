<?php

namespace App\Http\Controllers\Api;

use App\Models\Carte;
use App\Models\Theme;
use App\Models\Revision;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CardController extends Controller
{
    /**
     * Retourne toutes les cartes d'un thème public spécifique avec les informations sur le thème et l'utilisateur
     *
     * @param $themeId
     * @return \Illuminate\Http\JsonResponse
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
