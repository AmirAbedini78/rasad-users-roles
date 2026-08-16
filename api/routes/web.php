<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => config('app.name'),
        'message' => 'This application is API-only. Use the /api prefix.',
        'api' => url('/api'),
    ]);
});
