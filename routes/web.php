<?php

use Illuminate\Support\Facades\Route;
use Marshmallow\MarketingData\Http\Controllers\StoreMarketingCookiesController;

Route::post('mm-store-marketing-cookies', StoreMarketingCookiesController::class)
    ->middleware('web')
    ->name('marketing-data-tracker.store-marketing-cookies');
