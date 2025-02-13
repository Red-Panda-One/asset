<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\CategoriesController;
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

// Routes for Location
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->resource('locations', LocationController::class);

// Routes for Assets
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->get('/scanner', function () {
    return Inertia::render('Scanner/Index');
})->name('scanner.index');

// Routes for Kit
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->get('/kit', function () {
    return Inertia::render('Kits/Index');
})->name('kit.index');
