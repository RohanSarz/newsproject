<?php

use App\Http\Controllers\QuoteController;
use Illuminate\Support\Facades\Route;

// Public API routes
Route::get('/quotes/random', [QuoteController::class, 'random']);
