<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/chat-proxy', function (Request $request) {
    $message = $request->input('message');

    try {
        // Tambahkan withoutVerifying() untuk menghindari masalah SSL pada IP lokal
        $response = Http::timeout(180)
            ->withoutVerifying() 
            ->post('http://10.252.242.28:3232/chat', [
                'message' => $message,
            ]);

        // Cek apakah response sukses
        if ($response->successful()) {
            return response()->json($response->json(), 200);
        }

        return response()->json([
            'response' => 'FastAPI memberikan respon error.',
            'detail' => $response->body()
        ], $response->status());

    } catch (\Exception $e) {
        return response()->json([
            'response' => 'Waduh, koneksi dari Laravel ke LLM putus nih, Bre.',
            'error' => $e->getMessage()
        ], 500);
    }
});
