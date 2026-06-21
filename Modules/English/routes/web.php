<?php

use Illuminate\Support\Facades\Route;
use Modules\English\Http\Controllers\EnglishController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('englishes', EnglishController::class)->names('english');
});
