<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

require __DIR__.'/auth.php';

Route::get('/linkstorage', function () {
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    return 'Storage linked!';
});

// Fallback to serve storage files if symlink is missing
Route::get('/storage/{path}', [ProductController::class, 'serveImage'])->where('path', '.*');

Route::get('/fetch-image/{path}', [ProductController::class, 'serveImage'])->where('path', '.*');