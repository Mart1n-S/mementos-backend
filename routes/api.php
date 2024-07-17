<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PushController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CardController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ThemeController;
use App\Http\Controllers\Api\RevisionController;
use App\Http\Controllers\Api\CategorieController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/**
 * Route pour récupérer les informations de l'utilisateur connecté
 
 */
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * Route pour créer un utilisateur
 */
Route::post('/create-user', [AuthController::class, 'createUser']);

/**
 * Route pour connecter un utilisateur
 */
Route::post('/login', [AuthController::class, 'loginUser']);

/**
 * Route pour déconnecter un utilisateur
 */
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

/**
 * Route pour envoyer un email de réinitialisation de mot de passe
 */
Route::post('/forgot-password', [AuthController::class, 'forgot']);

/**
 * Route pour réinitialiser le mot de passe
 */
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

/**
 * Route pour mettre à jour un utilisateur
 */
Route::middleware('auth:sanctum')->put('/user/{id}', [UserController::class, 'update']);

/**
 * Route pour mettre à jour l'état de l'abonnement d'un utilisateur
 */
Route::post('/user/subscription', [UserController::class, 'updateSubscription'])->middleware('auth:sanctum');


/**
 * Route pour récupérer les catégories
 */
Route::get('/categories', [CategorieController::class, 'index']);

/**
 * Route pour créer un thème pour un utilisateur connecté
 */
Route::post('/themes', [ThemeController::class, 'createTheme'])->middleware('auth:sanctum');

/**
 * Route pour récupérer les thèmes pour une catégorie donnée
 */
Route::get('/themes/{categorie}', [ThemeController::class, 'getThemesByCategorie']);

/**
 * Route pour récupérer les thèmes d'un utilisateur
 */
Route::get('/user/{userId}/themes', [ThemeController::class, 'getThemesByUser'])->middleware('auth:sanctum');

/**
 * Route pour récupérer un thème spécifique
 */
Route::get('/themes/infos/{themeId}', [ThemeController::class, 'getThemeById'])->middleware('auth:sanctum');

/**
 * Route pour modifier un thème de l'utilisateur connecté
 */
Route::put('/themes/{themeId}', [ThemeController::class, 'updateTheme'])->middleware('auth:sanctum');

/**
 * Route pour supprimer un thème de l'utilisateur connecté
 */
Route::delete('/themes/{themeId}', [ThemeController::class, 'deleteTheme'])->middleware('auth:sanctum');

/**
 * Route pour dupliquer un thème public
 */
Route::post('/duplicate/{themeId}/themes', [ThemeController::class, 'duplicateTheme'])->middleware('auth:sanctum');

/**
 * Route pour récupérer les cartes d'un thème public
 */
Route::get('/themes/{theme}/cards', [CardController::class, 'getCardsByTheme']);

/**
 * Route pour récupérer les cartes d'un thème spécifique pour un utilisateur connecté
 */
Route::get('/cartes/{themeId}', [CardController::class, 'getCardsByThemeForUser'])->middleware('auth:sanctum');

/**
 * Route pour supprimer une carte d'un utilisateur connecté

 */
Route::delete('/cartes/{carteId}', [CardController::class, 'deleteCard'])->middleware('auth:sanctum');

/**
 * Route pour mettre à jour une carte d'un utilisateur connecté
 */
Route::put('/cartes/{id}', [CardController::class, 'updateCard'])->middleware('auth:sanctum');

/**
 * Route pour créer une carte pour un utilisateur connecté
 */
Route::post('/cartes', [CardController::class, 'createCard'])->middleware('auth:sanctum');

/**
 * Route pour récupérer les révisions d'un utilisateur connecté
 */
Route::get('/revision/{userId}', [RevisionController::class, 'fetchUserRevision'])->middleware('auth:sanctum');

/**
 * Route pour supprimer un thème de la révision de l'utilisateur connecté
 */
Route::delete('/revision/{themeId}', [RevisionController::class, 'deleteThemeFromRevision'])->middleware('auth:sanctum');

/**
 * Route pour supprimer toutes les cartes de la révision de l'utilisateur connecté
 */
Route::delete('/deleteAll/revision', [RevisionController::class, 'deleteAllRevision'])->middleware('auth:sanctum');

/**
 * Route pour abonner un utilisateur aux notifications push
 */
Route::post('/subscribe', [PushController::class, 'subscribe'])->middleware('auth:sanctum');


/**
 * Route de test pour envoyer une notification push
 */
Route::post('/sendNotification', [PushController::class, 'sendNotification']);

/**
 * Route de test pour envoyer une notification push au premier user trouvé dans la table PushSubscription, donc a utiliser 
 * si c'est le user que j'ai créé qui est le premier dans la table sinon utiliser sendNotification
 */
Route::post('/test-notification', [PushController::class, 'testNotification']);
