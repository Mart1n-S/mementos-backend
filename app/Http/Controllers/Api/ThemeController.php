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
use Illuminate\Support\Facades\DB;

/**
 * @OA\Schema(
 *   schema="Theme",
 *   type="object",
 *   required={"id", "user_id", "category_id", "nom", "couleur", "public"},
 *   title="Theme",
 *   description="Schéma d'un thème",
 *   properties={
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="category_id", type="integer", example=1),
 *     @OA\Property(property="nom", type="string", example="Thème de la biologie"),
 *     @OA\Property(property="couleur", type="string", example="#FFFFFF"),
 *     @OA\Property(property="public", type="boolean", example=true),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2021-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2021-01-02T00:00:00Z"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2021-01-02T00:00:00Z")
 *   }
 * )
 */
class ThemeController extends Controller
{
    /**
     * Retourne tous les thèmes publiques d'une catégorie
     *
     * @param $categorie
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Get(
     *   path="/themes/{categorie}",
     *   summary="Récupérer les thèmes par catégorie",
     *   description="Récupère tous les thèmes publics associés à une catégorie spécifique.",
     *   tags={"Themes"},
     *   operationId="getThemesByCategorie",
     *   @OA\Parameter(
     *     name="categorie",
     *     in="path",
     *     required=true,
     *     description="Nom de la catégorie pour laquelle récupérer les thèmes (ex : Histoire)",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Liste des thèmes récupérée avec succès",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/Theme")
     *     )
     *   ),
     * )
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
    /**
     * @OA\Get(
     *   path="/themes/{userId}/user",
     *   summary="Récupérer les thèmes d'un utilisateur spécifique",
     *   description="Retourne tous les thèmes associés à l'ID utilisateur spécifié, accessible uniquement par l'utilisateur connecté correspondant à l'ID.",
     *   tags={"Themes"},
     *   operationId="getThemesByUser",
     *   security={{ "sanctum": {} }},
     *   @OA\Parameter(
     *     name="userId",
     *     in="path",
     *     description="ID de l'utilisateur pour lequel récupérer les thèmes",
     *     required=true,
     *     @OA\Schema(type="integer", example=1)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Thèmes de l'utilisateur récupérés avec succès",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/Theme")
     *     )
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Accès non autorisé si l'utilisateur connecté ne correspond pas à l'ID utilisateur demandé",
     *     @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Non autorisé")
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Erreur serveur",
     *     @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Erreur lors de la récupération des thèmes")
     *     )
     *   )
     * )
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
    /**
     * @OA\Get(
     *   path="/themes/infos/{themeId}",
     *   summary="Récupérer un thème spécifique",
     *   description="Retourne les détails d'un thème spécifique, accessible uniquement par l'utilisateur connecté qui en est le propriétaire.",
     *   tags={"Themes"},
     *   operationId="getThemeById",
     *   security={{ "sanctum": {} }},
     *   @OA\Parameter(
     *     name="themeId",
     *     in="path",
     *     description="ID du thème à récupérer",
     *     required=true,
     *     @OA\Schema(type="integer", example=23)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Thème récupéré avec succès",
     *     @OA\JsonContent(ref="#/components/schemas/Theme")
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Accès non autorisé si l'utilisateur connecté ne correspond pas au propriétaire du thème",
     *     @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Erreur lors de la récupération du thème ou accès non autorisé")
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Erreur serveur",
     *     @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Erreur serveur")
     *     )
     *   )
     * )
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
     * Crée un nouveau thème pour l'utilisateur connecté
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Post(
     *   path="/themes",
     *   summary="Créer un nouveau thème",
     *   description="Permet à l'utilisateur connecté de créer un nouveau thème avec des cartes associées.",
     *   tags={"Themes"},
     *   operationId="createTheme",
     *   security={{ "sanctum": {} }},
     *   @OA\RequestBody(
     *     required=true,
     *     description="Les données nécessaires pour créer un nouveau thème et des cartes associées",
     *     @OA\JsonContent(
     *       required={"nom", "category_id", "public", "cards"},
     *       @OA\Property(property="nom", type="string", example="THEME TEST"),
     *       @OA\Property(property="category_id", type="integer", example=3),
     *       @OA\Property(property="public", type="boolean", example=false),
     *       @OA\Property(
     *         property="cards",
     *         type="array",
     *         @OA\Items(
     *           type="object",
     *           required={"question", "reponse"},
     *           @OA\Property(property="question", type="string", example="Test question"),
     *           @OA\Property(property="reponse", type="string", example="Test réponse")
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Thème créé avec succès",
     *     @OA\JsonContent(
     *       ref="#/components/schemas/Theme"
     *     )
     *   ),
     *  @OA\Response(
     *    response=422,
     *    description="Erreur de validation des données d'entrée",
     *    @OA\JsonContent(
     *      type="object",
     *      @OA\Property(
     *        property="errors",
     *        type="object",
     *        description="Objet contenant les messages d'erreur de validation pour chaque champ",
     *        @OA\Property(
     *          property="nom",
     *          type="array",
     *          @OA\Items(type="string", example="The nom field must be at least 3 characters.")
     *        ),
     *        @OA\Property(
     *          property="category_id",
     *          type="array",
     *          @OA\Items(type="string", example="The selected category_id is invalid.")
     *        ),
     *        @OA\Property(
     *          property="public",
     *          type="array",
     *          @OA\Items(type="string", example="The public field must be true or false.")
     *        ),
     *        @OA\Property(
     *          property="cards",
     *          type="array",
     *          @OA\Items(type="string", example="The cards field is required.")
     *        ),
     *        @OA\Property(
     *          property="cards.*.question",
     *          type="array",
     *          @OA\Items(type="string", example="The cards.*.question field must not be empty.")
     *        ),
     *        @OA\Property(
     *          property="cards.*.reponse",
     *          type="array",
     *          @OA\Items(type="string", example="The cards.*.reponse field must not be empty.")
     *        )
     *      )
     *    )
     *  ),
     *   @OA\Response(
     *     response=500,
     *     description="Erreur serveur",
     *     @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Erreur lors de la création du thème")
     *     )
     *   )
     * )
     */
    public function createTheme(Request $request)
    {
        $user = Auth::user();

        // Validation des données de la requête
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|min:3|max:35',
            'category_id' => 'required|exists:categories,id',
            'public' => 'required|boolean',
            'cards' => 'required|array',
            'cards.*.question' => 'required|string|min:1|max:255',
            'cards.*.reponse' => 'required|string|min:1|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Récupérer la catégorie
            $category = Categorie::find($request->input('category_id'));
            if (!$category) {
                return response()->json(['error' => 'Catégorie non trouvée'], 404);
            }

            // Créer le nouveau thème
            $theme = new Theme();
            $theme->nom = $request->input('nom');
            $theme->category_id = $request->input('category_id');
            $theme->public = $request->input('public');
            $theme->couleur = $category->couleur;
            $theme->user_id = $user->id;
            $theme->save();

            // Créer les cartes associées
            $cards = $request->input('cards');
            foreach ($cards as $card) {
                $newCard = new Carte();
                $newCard->question = $card['question'];
                $newCard->reponse = $card['reponse'];
                $newCard->theme_id = $theme->id;
                $newCard->save();
            }

            return response()->json($theme, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la création du thème'], 500);
        }
    }

    /**
     * Met à jour un thème pour un utilisateur connecté
     *
     *  @param Request $request
     *  @param $themeId
     *  @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Put(
     *   path="/themes/{themeId}",
     *   summary="Mettre à jour un thème",
     *   description="Permet à l'utilisateur connecté de mettre à jour les détails d'un thème spécifique dont il est propriétaire.",
     *   tags={"Themes"},
     *   operationId="updateTheme",
     *   security={{ "sanctum": {} }},
     *   @OA\Parameter(
     *     name="themeId",
     *     in="path",
     *     description="ID du thème à mettre à jour",
     *     required=true,
     *     @OA\Schema(type="integer", example=23)
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     description="Les données nécessaires pour mettre à jour le thème",
     *     @OA\JsonContent(
     *       required={"nom", "category_id", "public"},
     *       @OA\Property(property="nom", type="string", example="Nutrition et bien-être"),
     *       @OA\Property(property="category_id", type="integer", example=12),
     *       @OA\Property(property="public", type="boolean", example=true)
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Thème mis à jour avec succès",
     *     @OA\JsonContent(ref="#/components/schemas/Theme")
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Accès non autorisé si l'utilisateur connecté ne correspond pas au propriétaire du thème ou le thème est introuvable",
     *     @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Accès non autorisé ou thème non trouvé")
     *     )
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Erreur de validation des données d'entrée",
     *     @OA\JsonContent(
     *       @OA\Property(property="errors", type="object")
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Erreur serveur",
     *     @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Erreur lors de la mise à jour du thème")
     *     )
     *   )
     * )
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
    /**
     * @OA\Delete(
     *   path="/themes/{themeId}",
     *   summary="Supprimer un thème",
     *   description="Supprime un thème spécifique si l'utilisateur connecté est le propriétaire du thème.",
     *   tags={"Themes"},
     *   operationId="deleteTheme",
     *   security={{ "sanctum": {} }},
     *   @OA\Parameter(
     *     name="themeId",
     *     in="path",
     *     required=true,
     *     description="Identifiant unique du thème à supprimer",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Thème supprimé avec succès",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Thème supprimé avec succès")
     *     )
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Accès non autorisé ou thème non trouvé",
     *     @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Accès non autorisé ou thème non trouvé")
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Erreur serveur lors de la suppression du thème",
     *     @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Erreur lors de la suppression du thème")
     *     )
     *   )
     * )
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

    /**
     * Duplique un thème public
     *
     * @param $themeId
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Post(
     *   path="/themes/duplicate/{themeId}",
     *   summary="Dupliquer un thème public pour les utilisateurs connectés",
     *   description="Crée une copie d'un thème public pour l'utilisateur connecté, le nouveau thème est marqué comme privé.",
     *   tags={"Themes"},
     *   operationId="duplicateTheme",
     *   security={{ "sanctum": {} }},
     *   @OA\Parameter(
     *     name="themeId",
     *     in="path",
     *     required=true,
     *     description="Identifiant unique du thème à dupliquer",
     *     @OA\Schema(
     *       type="integer"
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Thème dupliqué avec succès",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="user_id", type="integer", example=1),
     *       @OA\Property(property="category_id", type="integer", example=5),
     *       @OA\Property(property="nom", type="string", example="Innovations en technologie"),
     *       @OA\Property(property="couleur", type="string", example="#636363"),
     *       @OA\Property(property="public", type="boolean", example=false),
     *       @OA\Property(property="created_at", type="string", format="date-time", example="2024-07-23T09:21:40.000000Z"),
     *       @OA\Property(property="updated_at", type="string", format="date-time", example="2024-07-23T09:21:40.000000Z"),
     *       @OA\Property(property="deleted_at", type="string", nullable=true),
     *       @OA\Property(property="id", type="integer", example=47)
     *     )
     *   ),
     *   @OA\Response(
     *     response=403,
     *     description="Accès non autorisé, tentative de dupliquer son propre thème",
     *     @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Vous ne pouvez pas dupliquer votre propre thème")
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Erreur serveur lors de la duplication du thème",
     *     @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Erreur lors de la duplication du thème: [message d'erreur]")
     *     )
     *   )
     * )
     */
    public function duplicateTheme($themeId)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $originalTheme = Theme::where('id', $themeId)->where('public', true)->firstOrFail();
            // Vérifier que le thème n'appartient pas déjà à l'utilisateur connecté
            if ($originalTheme->user_id === $user->id) {
                return response()->json(['error' => 'Vous ne pouvez pas dupliquer votre propre thème'], 403);
            }

            // Créer le nouveau thème
            $newTheme = $originalTheme->replicate();
            $newTheme->user_id = $user->id;
            $newTheme->nom = $originalTheme->nom;
            $newTheme->public = false;
            $newTheme->save();

            // Dupliquer les cartes associées
            $originalCards = Carte::where('theme_id', $themeId)->get();
            foreach ($originalCards as $originalCard) {
                $newCard = $originalCard->replicate();
                $newCard->theme_id = $newTheme->id;
                $newCard->save();
            }

            DB::commit();

            return response()->json($newTheme, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Erreur lors de la duplication du thème: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Duplique un thème public pour un utilisateur non connecté (invité)
     *
     * @param $themeId
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @OA\Get(
     *   path="/themes/duplicate/guest/{themeId}",
     *   summary="Dupliquer un thème pour un invité",
     *   description="Récupère un thème public spécifié par son ID et le duplique pour un usage par un utilisateur non connecté, renvoyant les données de base du thème ainsi que les cartes associées.",
     *   tags={"Themes"},
     *   operationId="duplicateThemeForGuest",
     *   @OA\Parameter(
     *     name="themeId",
     *     in="path",
     *     description="ID du thème public à dupliquer",
     *     required=true,
     *     @OA\Schema(type="integer", example=1)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Thème dupliqué avec succès pour un invité",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="theme",
     *         type="object",
     *         @OA\Property(property="nom", type="string", example="La Renaissance"),
     *         @OA\Property(property="category_nom", type="string", example="Histoire"),
     *         @OA\Property(property="couleur", type="string", example="#A88DFF")
     *       ),
     *       @OA\Property(
     *         property="cards",
     *         type="array",
     *         @OA\Items(
     *           type="object",
     *           @OA\Property(property="question", type="string", example="Quelle invention a révolutionné l'Europe pendant la Renaissance ?"),
     *           @OA\Property(property="reponse", type="string", example="L'imprimerie")
     *         )
     *       )
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Erreur serveur",
     *     @OA\JsonContent(
     *       @OA\Property(property="error", type="string", example="Erreur lors de la récupération des thèmes")
     *     )
     *   )
     * )
     */
    public function duplicateForGuest($themeId)
    {
        try {
            $theme = Theme::with('cartes', 'categorie')->where('public', true)->findOrFail($themeId);

            $themeData = [
                'nom' => $theme->nom,
                'category_nom' => $theme->categorie->nom,
                'couleur' => $theme->couleur
            ];

            $cardsData = $theme->cartes->map(function ($carte) {
                return [
                    'question' => $carte->question,
                    'reponse' => $carte->reponse
                ];
            });

            return response()->json([
                'theme' => $themeData,
                'cards' => $cardsData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération des thèmes'], 500);
        }
    }
}
