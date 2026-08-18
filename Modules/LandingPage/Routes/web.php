<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
use Illuminate\Support\Facades\Route;
use Modules\LandingPage\Http\Controllers\CustomPageController;
use Modules\LandingPage\Http\Controllers\DiscoverController;
use Modules\LandingPage\Http\Controllers\FaqController;
use Modules\LandingPage\Http\Controllers\FeaturesController;
use Modules\LandingPage\Http\Controllers\HomeController;
use Modules\LandingPage\Http\Controllers\JoinUsController;
use Modules\LandingPage\Http\Controllers\LandingPageController;
use Modules\LandingPage\Http\Controllers\PricingPlanController;
use Modules\LandingPage\Http\Controllers\ScreenshotsController;
use Modules\LandingPage\Http\Controllers\TestimonialsController;

// Routes without authentication
Route::middleware('XSS')->group(function () {
    Route::get('pages/{slug}', [CustomPageController::class, 'customPage'])->name('custom.page');
});

// Routes with authentication and XSS middleware
Route::middleware(['auth', 'XSS'])->group(function () {

    Route::resource('landingpage', LandingPageController::class);

    Route::resource('custom_page', CustomPageController::class);
    Route::post('custom_store', [CustomPageController::class, 'customStore'])->name('custom_store');
    Route::delete('custom_store/delete/{slug}/{key}', [CustomPageController::class, 'destroy'])->name('custom.page.delete');

    Route::resource('homesection', HomeController::class);

    Route::resource('features', FeaturesController::class);
    Route::controller(FeaturesController::class)->group(function () {
        Route::get('feature/create', 'feature_create')->name('feature_create');
        Route::post('feature/store', 'feature_store')->name('feature_store');
        Route::get('feature/edit/{key}', 'feature_edit')->name('feature_edit');
        Route::post('feature/update/{key}', 'feature_update')->name('feature_update');
        Route::get('feature/delete/{key}', 'feature_delete')->name('feature_delete');

        Route::post('feature_highlight_create', 'feature_highlight_create')->name('feature_highlight_create');

        Route::get('features/create', 'features_create')->name('features_create');
        Route::post('features/store', 'features_store')->name('features_store');
        Route::get('features/edit/{key}', 'features_edit')->name('features_edit');
        Route::post('features/update/{key}', 'features_update')->name('features_update');
        Route::get('features/delete/{key}', 'features_delete')->name('features_delete');
    });

    Route::resource('discover', DiscoverController::class);
    Route::controller(DiscoverController::class)->group(function () {
        Route::get('discover/create', 'discover_create')->name('discover_create');
        Route::post('discover/store', 'discover_store')->name('discover_store');
        Route::get('discover/edit/{key}', 'discover_edit')->name('discover_edit');
        Route::post('discover/update/{key}', 'discover_update')->name('discover_update');
        Route::get('discover/delete/{key}', 'discover_delete')->name('discover_delete');
    });

    Route::resource('screenshots', ScreenshotsController::class);
    Route::controller(ScreenshotsController::class)->group(function () {
        Route::get('screenshots/create', 'screenshots_create')->name('screenshots_create');
        Route::post('screenshots/store', 'screenshots_store')->name('screenshots_store');
        Route::get('screenshots/edit/{key}', 'screenshots_edit')->name('screenshots_edit');
        Route::post('screenshots/update/{key}', 'screenshots_update')->name('screenshots_update');
        Route::get('screenshots/delete/{key}', 'screenshots_delete')->name('screenshots_delete');
    });

    Route::resource('pricing_plan', PricingPlanController::class);

    Route::resource('faq', FaqController::class);
    Route::controller(FaqController::class)->group(function () {
        Route::get('faq/create', 'faq_create')->name('faq_create');
        Route::post('faq/store', 'faq_store')->name('faq_store');
        Route::get('faq/edit/{key}', 'faq_edit')->name('faq_edit');
        Route::post('faq/update/{key}', 'faq_update')->name('faq_update');
        Route::get('faq/delete/{key}', 'faq_delete')->name('faq_delete');
    });

    Route::resource('testimonials', TestimonialsController::class);
    Route::controller(TestimonialsController::class)->group(function () {
        Route::get('testimonials/create', 'testimonials_create')->name('testimonials_create');
        Route::post('testimonials/store', 'testimonials_store')->name('testimonials_store');
        Route::get('testimonials/edit/{key}', 'testimonials_edit')->name('testimonials_edit');
        Route::post('testimonials/update/{key}', 'testimonials_update')->name('testimonials_update');
        Route::get('testimonials/delete/{key}', 'testimonials_delete')->name('testimonials_delete');
    });

    Route::resource('join_us', JoinUsController::class);
});

// Separate join us store route
Route::post('join_us/store', [JoinUsController::class, 'joinUsUserStore'])->name('join_us_store');
