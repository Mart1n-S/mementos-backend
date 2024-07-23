<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categorie;

class CategorieController extends Controller
{
    /**
     * Retourne toutes les catégories
     *
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * * @OA\Schema(
     *   schema="Categorie",
     *   type="object",
     *   required={"id", "nom", "pathImage", "couleur"},
     *   title="Categorie",
     *   description="Schéma d'une catégorie",
     *   properties={
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="nom", type="string", example="Science"),
     *     @OA\Property(property="pathImage", type="string", example="image.jpg"),
     *     @OA\Property(property="couleur", type="string", example="#FFFFFF"),
     *     @OA\Property(property="deleted_at", type="string", format="date-time", example="null"),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2021-01-01T00:00:00Z"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2021-01-02T00:00:00Z")
     *   }
     * )
     * @OA\Get(
     *   path="/categories",
     *   summary="Liste toutes les catégories",
     *   description="Récupère une liste de toutes les catégories disponibles (public).",
     *   tags={"Categories"},
     *   operationId="getCategories",
     *   @OA\Response(
     *     response=200,
     *     description="Opération réussie",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/Categorie")
     *     )
     *   ),
     *   @OA\Response(
     *     response=500,
     *     description="Erreur interne du serveur",
     *     @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Une erreur interne est survenue")
     *     )
     *   )
     * )
     */
    public function index()
    {
        $categories = Categorie::all();
        return response()->json($categories);
    }
}
