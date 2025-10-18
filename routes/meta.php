<?php

use App\Http\Controllers\MetaController;
use Illuminate\Support\Facades\Route;

Route::get('/facebook/callback', [MetaController::class, 'callback']);
Route::get('/meta', [MetaController::class, 'index']);
