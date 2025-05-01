<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PolygonController;
use Illuminate\Http\Request; // Properly import the Request class

Route::post('/save-polygon', function (Request $request) {
    $coordinates = $request->input('coordinates');

    // Let's use the controller for this logic instead of doing it directly in routes
    return app(PolygonController::class)->store($request);
});

Route::get('/', function () {
    return view('map');
});