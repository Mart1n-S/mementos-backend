<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Mettre à jour un utilisateur
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        /** @var User $user */
        $user = Auth::user();

        // Vérifiez que l'utilisateur connecté correspond à l'ID dans l'URL
        if ($user->id != $id) {
            return response()->json([
                'status' => false,
                'message' => 'Vous ne pouvez pas modifier les informations d\'un autre utilisateur.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'pseudo' => 'required|string|min:2|max:20',
            'niveauRevision' => 'required|integer|min:1|max:7',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Veuillez vérifier les erreurs de validation',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user->pseudo = $request->pseudo;
        $user->niveauRevision = $request->niveauRevision;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Utilisateur mis à jour avec succès',
        ]);
    }
}
