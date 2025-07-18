<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\MaterialManagementController;

// Security: Disable route model binding debugging in production
if (app()->environment('production')) {
    Route::pattern('id', '[0-9]+');
}

// Main routes
Route::get('/', [MaterialController::class, 'index']);
Route::get('/catalog', [CatalogController::class, 'index']);
Route::get('/management', [MaterialManagementController::class, 'index']);

// API routes with rate limiting
Route::middleware(['throttle:60,1'])->group(function () {
    Route::get('/api/materials', [MaterialController::class, 'getMaterials']);
    Route::get('/api/stats', [MaterialController::class, 'getStats']);
    Route::get('/api/materials/stats', [MaterialController::class, 'getStats']);
    Route::get('/api/management/materials', [MaterialManagementController::class, 'getMaterials']);
    Route::put('/api/management/materials/{id}', [MaterialManagementController::class, 'updateMaterial']);
});

// File operations with security middleware
Route::middleware(['file.upload.security', 'throttle:5,1'])->group(function () {
    Route::get('/management/download-template', [MaterialManagementController::class, 'downloadTemplate']);
    Route::post('/management/upload-data', [MaterialManagementController::class, 'uploadData']);
    Route::post('/management/export-data', [MaterialManagementController::class, 'exportData']);
});
