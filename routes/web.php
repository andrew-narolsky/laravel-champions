<?php

use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false,
]);

Route::group(['prefix' => 'admin'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin');

    Route::resource('/countries', CountryController::class)->except(['show']);
});
