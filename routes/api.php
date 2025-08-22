<?php

use App\Http\Middleware\ApiKeyAuth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailSendController;

Route::middleware([
    ApiKeyAuth::class,
    'throttle:client'
])->group(function () {
    Route::post('/send', [EmailSendController::class, 'send']);
    Route::post('/send-now', [EmailSendController::class, 'sendNow']);
    Route::get('/messages/{id}', [EmailSendController::class, 'show']);
});
