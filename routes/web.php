<?php

use Illuminate\Support\Facades\Route;
use Marshmallow\MarketingData\Http\Controllers\StoreMarketingCookiesController;

Route::post('mm-store-marketing-cookies', function (\Illuminate\Http\Request $request) {
    return app(StoreMarketingCookiesController::class)($request);
})
    ->middleware('web')
    ->name('mm-store-marketing-cookies');
