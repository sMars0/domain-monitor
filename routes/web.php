<?php

use App\Http\Controllers\DomainController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $user = request()->user();
    $domains = $user->domains()->get(['id', 'name', 'url', 'last_status', 'last_checked_at', 'is_active']);

    $stats = [
        'total'   => $domains->count(),
        'up'      => $domains->where('last_status', 'up')->count(),
        'down'    => $domains->where('last_status', 'down')->count(),
        'unknown' => $domains->where('last_status', 'unknown')->count(),
    ];

    $downDomains = $domains->where('last_status', 'down')->take(5);

    return view('dashboard', compact('stats', 'downDomains'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/domains/{domain}/check', [DomainController::class, 'check'])->name('domains.check');
    Route::resource('domains', DomainController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
