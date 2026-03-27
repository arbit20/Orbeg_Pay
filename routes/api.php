<?php

use App\Http\Controllers\Api\ClientApiController;
use Illuminate\Support\Facades\Route;

Route::apiResource('clients', ClientApiController::class);

