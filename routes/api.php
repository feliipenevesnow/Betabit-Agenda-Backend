<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\ContactController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('contacts', ContactController::class);
    Route::post('contacts/{contact}/toggle-favorite', [ContactController::class, 'toggleFavorite']);
    Route::post('contacts/update-order', [ContactController::class, 'updateSortOrder']);
});

Route::get('/ai-photo', function () {
    try {
        $response = Http::withOptions(['verify' => false])
            ->get('https://thispersondoesnotexist.com/image');

        if ($response->failed()) {
            return response()->json(['error' => 'Falha ao buscar imagem da IA'], 500);
        }

        return response($response->body(), 200)
            ->header('Content-Type', 'image/jpeg')
            ->header('Access-Control-Allow-Origin', '*')
            ->header('Access-Control-Allow-Methods', 'GET, OPTIONS')
            ->header('Access-Control-Allow-Headers', 'Origin, Content-Type, Accept, Authorization');
    } catch (\Exception $e) {
        return response()->json(['error' => 'Erro ao gerar imagem: ' . $e->getMessage()], 500);
    }
});