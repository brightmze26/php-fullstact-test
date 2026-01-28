<?php

use App\Http\Controllers\Api\MyClientController;
use Illuminate\Support\Facades\Route;

Route::get('/my-clients/slug/{slug}', [MyClientController::class, 'bySlug']);

Route::apiResource('my-clients', MyClientController::class);
