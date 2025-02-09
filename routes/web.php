<?php

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
    Route::get('/assets', function () {
        return Inertia::render('Assets/Index');
    })->name('assets.index');
    Route::get('/ui', function () {
        return Inertia::render('Ui/Index');
    })->name('ui.index');


    Route::get('/tags/create', function () {
        return Inertia::render('Tags/Create');
    })->name('tags.create');

    Route::get('/tags/{tag}/edit', function () {
        return Inertia::render('Tags/Edit');
    })->name('tags.edit');

    Route::resource('tags', TagController::class);
});
