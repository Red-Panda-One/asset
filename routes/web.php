<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\KitController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\TagController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
    Route::get('/ui', function () {
        return Inertia::render('Ui/Index');
    })->name('ui.index');
});

// Routes for Assets
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->resource('assets', AssetController::class);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->post('/assets/{asset}', [AssetController::class, 'update'])->name('assets.update.post');

// Routes for Location
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->resource('locations', LocationController::class);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->post('locations/{location}', [LocationController::class, 'update'])->name('locations.update.post');

// Routes for Kit
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->resource('kits', KitController::class);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::post('kits/{kit}', [KitController::class, 'update'])->name('kit.update.post');
    Route::post('kits/{kit}/assets', [KitController::class, 'addAsset'])->name('kits.assets.add');
    Route::delete('kits/{kit}/assets/{asset}', [KitController::class, 'removeAsset'])->name('kits.assets.destroy');
});

// Routes for Tags
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->resource('tags', TagController::class);

// Routes for Categories
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->resource('categories', CategoriesController::class);

// Routes for Scanner
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->get('/scanner', function () {
    return Inertia::render('Scanner/Index');
})->name('scanner.index');

