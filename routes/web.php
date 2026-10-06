<?php

use App\Http\Controllers\CareerController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\InsightController;
use App\Http\Controllers\JobApplicationCvController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\SitemapController;
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

Route::get('/promotions', [PromotionController::class, 'index'])->name('promotions.index');
Route::get('/promotions/{promotion:slug}', [PromotionController::class, 'show'])->name('promotions.show');

// One page per loan, so each can rank for what it is.
Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
Route::get('/loans/{slug}', [LoanController::class, 'show'])->name('loans.show');

Route::get('/sitemap.xml', [SitemapController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');
Route::get('/llms.txt', [SitemapController::class, 'llms'])->name('llms');

Route::view('/about', 'pages.about')->name('about');

// Answers to the questions people ask before borrowing: how to apply, the FAQs, the terms.
Route::view('/how-to-apply', 'pages.how-to-apply')->name('how-to-apply');
Route::view('/faq', 'pages.faq')->name('faq');
Route::view('/glossary', 'pages.glossary')->name('glossary');

Route::view('/contact', 'pages.contact')->name('contact');

Route::view('/partners', 'pages.partners')->name('partners');

Route::get('/careers', [CareerController::class, 'index'])->name('careers');
Route::post('/careers/apply', [CareerController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('careers.apply');

// A CV for staff to read in the browser (PDF) or download (anything else).
// Admin users only; see JobApplicationCvController.
Route::get('/staff/applications/{application}/cv', [JobApplicationCvController::class, 'show'])
    ->name('applications.cv');

Route::get('/insights', [InsightController::class, 'index'])->name('insights.index');
Route::get('/insights/{slug}', [InsightController::class, 'show'])
    ->where('slug', '[a-z0-9-]+')
    ->name('insights.show');
