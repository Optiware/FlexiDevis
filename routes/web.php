<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/clients/import', [ClientController::class, 'import'])->name('clients.import');
    Route::resource('clients', ClientController::class);

    Route::resource('documents', DocumentController::class);

    // Routes pour les Produits
    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
    Route::resource('products', ProductController::class);

    Route::post('/documents/{id}/duplicate', [App\Http\Controllers\DocumentController::class, 'duplicate'])->name('documents.duplicate');

    // Routes pour le pdf
    Route::get('/documents/{id}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::resource('documents', DocumentController::class);
});

require __DIR__.'/auth.php';
