<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\MedicineController;
use App\Http\Controllers\Api\V1\PrescriptionController;
use App\Http\Controllers\Api\V1\SwaggerController;

Route::get('/documentation', [SwaggerController::class, 'index']);
Route::get('/documentation/spec', [SwaggerController::class, 'spec']);

Route::prefix('v1')->group(function () {

    Route::middleware('check.apikey')->group(function () {
        Route::get('/medicines', [MedicineController::class, 'index']);
        Route::get('/medicines/{id}', [MedicineController::class, 'show']);
                
        Route::post('/prescriptions', [PrescriptionController::class, 'store']);
        Route::get('/prescriptions', [PrescriptionController::class, 'index']);
        Route::get('/prescriptions/{id}', [PrescriptionController::class, 'show']);
    });

});