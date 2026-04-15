<?php

use App\Http\Controllers\Admin\AttachmentController;
use App\Http\Controllers\Admin\ClubController;
use App\Http\Controllers\Admin\CompetitionController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SeasonController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\CountryController as FrontCountryController;
use App\Http\Controllers\Front\CompetitionController as FrontCompetitionController;
use App\Http\Controllers\Front\ClubController as FrontClubController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false,
]);

Route::get('country/{country:slug}', [FrontCountryController::class, 'index'])->name('country.show');
Route::get('competition/{competition:slug}', [FrontCompetitionController::class, 'index'])->name('competition.show');
Route::get('club/{club:slug}', [FrontClubController::class, 'index'])->name('club.show');

Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin');

    Route::post('attachments/upload', [AttachmentController::class, 'upload'])->name('attachments.upload');
    Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');

    Route::resource('countries', CountryController::class)->except(['show']);
    Route::resource('clubs', ClubController::class)->except(['show']);
    Route::resource('competitions', CompetitionController::class)->except(['show']);
    Route::resource('competitions.seasons', SeasonController::class)->except(['index', 'show']);
});
