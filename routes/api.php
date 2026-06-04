<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FimAnalysisController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/fim-analysis', [FimAnalysisController::class, 'store']);
Route::get('/fim-analysis', [FimAnalysisController::class, 'index']);
Route::get('/fim-analysis/summary', [FimAnalysisController::class, 'summary']);