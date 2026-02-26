<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('logs', [\Opcodes\LogViewer\Http\Controllers\LogViewerController::class, 'index']);