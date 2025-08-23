<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileManagerController;

Route::get('/', function () {
    return view('welcome');
});

// Admin routes dengan sidebar
// Route::get('/admin/dashboard', function () {
//     return view('admin.dashboard');
// })->name('admin.dashboard');

// AJAX Demo page
// Route::get('/admin/ajax-demo', function () {
//     return view('admin.ajax-demo');
// })->name('admin.ajax-demo');

// UI Components Demo page
// Route::get('/admin/ui-components', function () {
//     return view('admin.ui-components');
// })->name('admin.ui-components');

// File Manager page
// Route::get('/admin/file-manager', function () {
//     return view('admin.file-manager');
// })->name('admin.file-manager');

// File Manager API routes
// Route::prefix('admin/file-manager')->name('admin.file-manager.')->group(function () {
//     Route::get('/api/files', [FileManagerController::class, 'getFiles'])->name('api.files');
//     Route::post('/api/upload', [FileManagerController::class, 'uploadFile'])->name('api.upload');
//     Route::post('/api/create-folder', [FileManagerController::class, 'createFolder'])->name('api.create-folder');
//     Route::delete('/api/delete', [FileManagerController::class, 'deleteItem'])->name('api.delete');
//     Route::get('/api/download', [FileManagerController::class, 'downloadFile'])->name('api.download');
// });

// Contoh route admin lainnya
// Route::prefix('admin')->name('admin.')->group(function () {
//     Route::get('/users', function () {
//         return view('admin.users');
//     })->name('users');
    
//     Route::get('/products', function () {
//         return view('admin.dashboard'); // Sementara gunakan dashboard view
//     })->name('products');
    
//     Route::get('/orders', function () {
//         return view('admin.dashboard'); // Sementara gunakan dashboard view
//     })->name('orders');
    
//     Route::get('/reports', function () {
//         return view('admin.dashboard'); // Sementara gunakan dashboard view
//     })->name('reports');
    
//     Route::get('/settings', function () {
//         return view('admin.dashboard'); // Sementara gunakan dashboard view
//     })->name('settings');
// });