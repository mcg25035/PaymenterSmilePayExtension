<?php

use Illuminate\Support\Facades\Route;


Route::post('/smilepay/webhook', [App\Extensions\Gateways\SmilePay\SmilePay::class, 'webhook'])->name('smilepay.webhook');
