<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CoaController;
use App\Http\Controllers\SalesOrderPdfController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/coa', [CoaController::class, 'index']);

Route::get('/sales-order/{id}/pdf', [SalesOrderPdfController::class, 'download'])
    ->name('sales-order.pdf');