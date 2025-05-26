<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TenderController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/tenders', [TenderController::class, 'index']);
Route::post('/tender', [TenderController::class, 'create']);
//Route::resource('/tenders', 'TenderController');
