<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Http\Controllers\ManageUserController;

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

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

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
})->middleware('guest')->name('pending-approval');



require __DIR__.'/auth.php';
