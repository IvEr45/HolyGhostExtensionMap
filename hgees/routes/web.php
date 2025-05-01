<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PolygonController;
use Illuminate\Http\Request;
use App\Models\Polygon;

Route::post('/save-polygon', function (Request $request) {
    return app(PolygonController::class)->store($request);
});

Route::delete('/polygons/{id}', [PolygonController::class, 'destroy']);

// Add new route for updating polygon properties
Route::put('/polygons/{id}', [PolygonController::class, 'update']);

Route::get('/', function () {
    // Get all polygons from the database
    $polygons = Polygon::all();
    return view('map', ['polygons' => $polygons]);
});
Route::put('/polygons/{id}/coordinates', [PolygonController::class, 'updateCoordinates']);
