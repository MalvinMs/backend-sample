<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::resource('forms', App\Http\Controllers\Api\FormController::class);
