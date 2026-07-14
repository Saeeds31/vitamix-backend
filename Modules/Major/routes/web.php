<?php

use Illuminate\Support\Facades\Route;
use Modules\Major\Http\Controllers\MajorController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('majors', MajorController::class)->names('major');
});
