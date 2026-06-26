<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\MedicineController;
use App\Http\Controllers\Api\V1\PrescriptionController;
use App\Http\Controllers\Api\V1\SwaggerController;

/*
|--------------------------------------------------------------------------
| Swagger
|--------------------------------------------------------------------------
*/

Route::get('/documentation', [SwaggerController::class, 'index']);
Route::get('/documentation/spec', [SwaggerController::class, 'spec']);

/*
|--------------------------------------------------------------------------
| API Root
|--------------------------------------------------------------------------
*/

Route::get('/v1', function () {
    return response()->json([
        'status'  => 'success',
        'message' => 'API Pharmacy Service',
        'data'    => null,
        'meta'    => [
            'service_name' => 'pharmacy-service',
            'api_version'  => 'v1',
        ]
    ], 200);
});

/*
|--------------------------------------------------------------------------
| API V1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    Route::middleware('check.apikey')->group(function () {

        // Medicines
        Route::get('/medicines', [MedicineController::class, 'index']);
        Route::get('/medicines/{id}', [MedicineController::class, 'show']);

        // Prescriptions
        Route::post('/prescriptions', [PrescriptionController::class, 'store']);
        Route::get('/prescriptions', [PrescriptionController::class, 'index']);
        Route::get('/prescriptions/{id}', [PrescriptionController::class, 'show']);

    });

});