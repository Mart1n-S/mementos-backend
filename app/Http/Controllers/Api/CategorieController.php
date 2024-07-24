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
    public function index()
    {
        $categories = Categorie::all();
        return response()->json($categories);
    }
}
