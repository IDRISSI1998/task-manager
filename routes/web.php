<?php

use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return view('welcome');
});


Route::resource('tasks', TaskController::class);

Route::get('/csrf-token', function () {
    return csrf_token();
});


/*
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
*/