<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['role:quan_tri'])
    ->prefix('quan-tri')
    ->group(function () {
        Route::get('/kiem-tra', function () {
            return 'Xin chao quan tri vien';
        });
    });

Route::middleware(['role:nha_cung_cap,quan_tri'])
    ->prefix('nha-cung-cap')
    ->group(function () {
        Route::get('/kiem-tra', function () {
            return 'Xin chao nha cung cap';
        });
    });