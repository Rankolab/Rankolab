<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Content Generation routes
Route::prefix('content')->group(function () {
    Route::post('/generate', 'App\Http\Controllers\API\ContentGenerationController@generate');
    Route::post('/check-plagiarism', 'App\Http\Controllers\API\ContentGenerationController@checkPlagiarism');
    Route::post('/check-readability', 'App\Http\Controllers\API\ContentGenerationController@checkReadability');
});

// Domain Analysis routes
Route::prefix('domain')->group(function () {
    Route::post('/analyze', 'App\Http\Controllers\API\DomainAnalysisController@analyze');
    Route::get('/keywords/{domain}', 'App\Http\Controllers\API\DomainAnalysisController@getKeywords');
    Route::get('/backlinks/{domain}', 'App\Http\Controllers\API\DomainAnalysisController@getBacklinks');
});

// License routes
Route::prefix('license')->group(function () {
    Route::post('/validate', 'App\Http\Controllers\API\LicenseValidationController@validate');
    Route::post('/activate', 'App\Http\Controllers\API\LicenseValidationController@activate');
    Route::post('/deactivate', 'App\Http\Controllers\API\LicenseValidationController@deactivate');
});