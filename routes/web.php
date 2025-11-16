<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home.dashboard');
})->name('home');

Route::get('/clients', function () {
    return view('clients.index');
})->name('clients.index');

Route::get('/atelier', function () {
    return view('atelier.index');
})->name('atelier.index');

Route::get('/location', function () {
    return view('location.index');
})->name('location.index');

Route::get('/counter', function () {
    return view('counter-demo');
})->name('counter');
