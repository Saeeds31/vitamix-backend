<?php

use Illuminate\Support\Facades\Route;
use Modules\English\Http\Controllers\EnglishController;

Route::middleware(['auth:sanctum'])->prefix('v1/admin')->group(function () {
    // article category get and set
    Route::get('/article-categories/{id}/en', [EnglishController::class, 'getArticleCategory']);
    Route::post('/article-categories/{id}/en', [EnglishController::class, 'setArticleCategory']);
    // article get and set

    Route::get('/articles/{id}/en', [EnglishController::class, 'getArticle']);
    Route::post('/articles/{id}/en', [EnglishController::class, 'setArticle']);
    // menu get and set
    Route::get('/menus/{id}/en', [EnglishController::class, 'getMenu']);
    Route::post('/menus/{id}/en', [EnglishController::class, 'setMenu']);
    // sliders get and set
    Route::get('/sliders/{id}/en', [EnglishController::class, 'getSlider']);
    Route::post('/sliders/{id}/en', [EnglishController::class, 'setSlider']);
    // banners get and set
    Route::get('/banners/{id}/en', [EnglishController::class, 'getBanners']);
    Route::post('/banners/{id}/en', [EnglishController::class, 'setBanners']);

    // categories get and set
    Route::get('/categories/{id}/en', [EnglishController::class, 'getCategories']);
    Route::post('/categories/{id}/en', [EnglishController::class, 'setCategories']);

    // products get and set
    Route::get('/products/{id}/en', [EnglishController::class, 'getProducts']);
    Route::post('/products/{id}/en', [EnglishController::class, 'setProducts']);

    // settings get and set
    Route::get('/settings-groups/{group}/en', [EnglishController::class, 'getSettingGroup']);
    Route::post('/settings-save-group/{group}/en', [EnglishController::class, 'setSettingGroup']);
});
