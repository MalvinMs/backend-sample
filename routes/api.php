<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('cloudflare.auth')->group(function () {
  Route::resource('forms', App\Http\Controllers\Api\FormController::class);
});
