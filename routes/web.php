<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CoaController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/coa', [CoaController::class, 'index']);