<?php

use Atom\Theme\Http\Controllers\Api\BadgeController;
use Atom\Theme\Http\Controllers\Api\FurnitureController;
use Atom\Theme\Http\Controllers\Api\HomeCategoryController;
use Atom\Theme\Http\Controllers\Api\HomeItemController;
use Atom\Theme\Http\Controllers\Api\IndexController;
use Atom\Theme\Http\Controllers\Api\OnlineCountController;
use Atom\Theme\Http\Controllers\Api\OnlineUserController;
use Atom\Theme\Http\Controllers\Api\TradeLogController;
use Atom\Theme\Http\Controllers\Api\UserController;
use Atom\Theme\Http\Controllers\Api\WebsiteArticleController;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->prefix('api')->group(function () {
    Route::get('users/{user:username}', UserController::class)
        ->name('api.users');

    // Atom specific routes.
    Route::middleware(Authenticate::using('sanctum'))->group(function () {
        Route::get('users/home/categories', HomeCategoryController::class)
            ->name('api.users.home.categories');

        Route::get('users/{user:username}/home/items', [HomeItemController::class, 'index'])
            ->name('api.users.home.items');

        Route::post('users/home/items', [HomeItemController::class, 'store'])
            ->name('api.users.home.items.store');

        Route::put('users/home/items', [HomeItemController::class, 'update'])
            ->name('api.users.home.items.update');
    });

    // Public api routes
    Route::prefix('public')->group(function () {
        Route::get('/', IndexController::class)
            ->name('api.public.index');

        Route::get('users/{user:username}', UserController::class)
            ->name('api.public.users');

        Route::get('online/count', OnlineCountController::class)
            ->name('api.public.online.count');

        Route::get('online/users', OnlineUserController::class)
            ->name('api.public.online.users');

        Route::get('furniture', FurnitureController::class)
            ->name('api.public.furniture');

        Route::get('trade-logs', TradeLogController::class)
            ->name('api.public.trade-logs');

        Route::apiResource('website-articles', WebsiteArticleController::class)
            ->names('api.public.website-articles')
            ->only(['index', 'show']);

        Route::apiResource('badges', BadgeController::class)
            ->names('api.public.badges')
            ->parameters(['badges' => 'badge:code'])
            ->only(['index', 'show']);
    });
});
