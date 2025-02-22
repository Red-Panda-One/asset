<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/version', function () {
    $version = trim(File::get(base_path('version.md')));
    return response()->json(['version' => $version]);
});
