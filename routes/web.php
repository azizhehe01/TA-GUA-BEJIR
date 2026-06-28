<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Http\Controllers\ManageUserController;
use App\Http\Controllers\DashboardController;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/manage-user', [ManageUserController::class, 'index'])
        ->name('manage-user');

    Route::post('/manage-user/{user}/toggle-status', [ManageUserController::class, 'updateStatus'])
        ->name('manage-user.toggle-status');

    Route::post('/manage-user/{user}/update-role', [ManageUserController::class, 'updateRole'])
        ->name('manage-user.update-role');

    Route::delete('/manage-user/{user}', [ManageUserController::class, 'delete'])
        ->name('manage-user.delete');
});


Route::get('/', function () {
    return view('welcome');
})->name('home');



Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/fim-dashboard', [DashboardController::class, 'fim'])
    ->middleware(['auth', 'verified'])
    ->name('fim.dashboard');

Route::view('AskAI', 'AskAI')
    ->middleware(['auth', 'verified'])
    ->name('AskAI');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['auth.password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

Route::get('/pending-approval', function () {
    return view('pending-approval');
})
->middleware('pending.approval')
->name('pending-approval');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/chat-proxy', function (Illuminate\Http\Request $request) {
        $user = Auth::user();
        $messageText = $request->input('message');
        $convId = $request->input('conversation_id');

        // 1. Logic Conversation
        if ($convId) {
            $conversation = Conversation::where('id', $convId)->where('user_id', $user->id)->firstOrFail();
        } else {
            $title = strlen($messageText) > 30 ? substr($messageText, 0, 30) . '...' : $messageText;
            $conversation = $user->conversations()->create(['title' => $title]);
        }

        // 2. Simpan Pesan User
        $conversation->messages()->create([
            'role' => 'user',
            'content' => $messageText
        ]);

        try {
            $response = Http::timeout(300)->withoutVerifying()->post('http://10.252.242.28:3232/chat', [
                'message' => $messageText,
            ]);

            $botResponse = $response->json()['response'] ?? 'Hadeh.. Botnya error nih, coba lagi nanti ya.🤣';

            // 3. Simpan Pesan Bot
            $conversation->messages()->create([
                'role' => 'assistant',
                'content' => $botResponse
            ]);

            return response()->json([
                'response' => $botResponse,
                'conversation_id' => $conversation->id
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'response' => 'Koneksi API Gagal, tapi pesan lo udah tersimpan di DB.',
                'conversation_id' => $conversation->id
            ]);
        }
    })->name('chat.proxy');
});

Route::get('/chat/messages/{id}', function ($id) {
    $conversation = App\Models\Conversation::where('id', $id)
        ->where('user_id', auth()->id())
        ->with('messages') // Ambil sekalian pesan-pesannya
        ->firstOrFail();

    return response()->json([
        'messages' => $conversation->messages
    ]);
})->name('chat.messages');

Route::delete('/chat/{conversation}', function (App\Models\Conversation $conversation) {
    if ($conversation->user_id !== auth()->id()) {
        abort(403);
    }

    $conversation->delete();

    // Jangan pakai back(), tapi arahkan ke halaman chat kosong
    return redirect()->route('AskAI'); 
})->name('chat.destroy');

Route::patch('/chat/{conversation}', function (Illuminate\Http\Request $request, App\Models\Conversation $conversation) {
    if ($conversation->user_id !== auth()->id()) {
        abort(403);
    }

    $request->validate([
        'title' => 'required|string|max:255',
    ]);

    $conversation->update([
        'title' => $request->input('title'),
    ]);

    return back();
})->name('chat.update');





require __DIR__.'/auth.php';
