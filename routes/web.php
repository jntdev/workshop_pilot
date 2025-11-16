<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home.dashboard');
})->name('home');

Route::get('/clients', function () {
    return view('clients.index');
})->name('clients.index');

Route::get('/clients/nouveau', function () {
    return view('clients.create');
})->name('clients.create');

Route::get('/clients/{id}', function ($id) {
    return view('clients.show', ['clientId' => $id]);
})->name('clients.show');

Route::get('/atelier', function () {
    return view('atelier.index');
})->name('atelier.index');

Route::get('/location', function () {
    return view('location.index');
})->name('location.index');

Route::get('/counter', function () {
    return view('counter-demo');
})->name('counter');
