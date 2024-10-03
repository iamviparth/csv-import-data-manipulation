<?php

use App\Http\Controllers\CSVController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/upload', [CSVController::class, 'index']);
Route::post('/upload', [CSVController::class, 'upload'])->name('upload');
Route::post('/create-table', [CSVController::class, 'createTable'])->name('create-table');
Route::get('/filter', [CSVController::class, 'filterData']);
