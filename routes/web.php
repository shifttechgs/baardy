<?php

use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\InsightController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web routes
|--------------------------------------------------------------------------
|
| The public marketing site. The homepage is static, so it is served with
| Route::view rather than an empty controller; give it a controller when it
| needs data that does not belong in config.
|
| Clean, descriptive URLs -- no .php extensions, no query strings for
| navigation. See docs/SEO.md for the planned URL structure.
|
*/

Route::view('/', 'pages.home')->name('home');

// Five enquiries a minute per visitor is plenty for a person and a nuisance
// for a script; the honeypot in the controller catches the rest.
Route::post('/enquiries', [EnquiryController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('enquiries.store');

// Legal pages: static copy, listed in config/company.php -> legal.
Route::view('/privacy', 'pages.legal.privacy')->name('legal.privacy');
Route::view('/terms', 'pages.legal.terms')->name('legal.terms');
Route::view('/responsible-lending', 'pages.legal.responsible-lending')->name('legal.responsible-lending');
Route::view('/complaints', 'pages.legal.complaints')->name('legal.complaints');

Route::get('/insights', [InsightController::class, 'index'])->name('insights.index');
Route::get('/insights/{slug}', [InsightController::class, 'show'])
    ->where('slug', '[a-z0-9-]+')
    ->name('insights.show');
