<?php

use App\Http\Controllers\Api\V1\PrescriptionController;
use App\Http\Controllers\Api\V1\MedicineController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/medicines', [MedicineController::class, 'index']);
    Route::get('/medicines/{id}', [MedicineController::class, 'show']);

    Route::post('/prescriptions', [PrescriptionController::class, 'store']);
    Route::get('/prescriptions', [PrescriptionController::class, 'index']);
    Route::get('/prescriptions/{id}', [PrescriptionController::class, 'show']);
});