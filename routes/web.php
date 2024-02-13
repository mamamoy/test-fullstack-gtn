<?php

use App\Http\Controllers\Admin\SalesReportController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Sales\SalesController;
use App\Models\SalesReports;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});



// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/export', [SalesReportController::class, 'exportPdf'])->name('export.pdf');
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [SalesReportController::class, 'index'])->name('dashboard');
    Route::post('/sales/store', [SalesReportController::class, 'store'])->name('sales.store');
    Route::put('/sales/update/{id}', [SalesReportController::class, 'update'])->name('sales.update');
    Route::delete('/sales/destroy/{id}', [SalesReportController::class, 'destroy'])->name('sales.destroy');
    Route::put('/approved/{id}', [SalesReportController::class, 'approved'])->name('salesReport.approved');
    Route::delete('/sales-report/destroy/{id}', [SalesReportController::class, 'destroyReport'])->name('salesReport.destroy');
});

// Route::group(['prefix' => 'admin', 'middleware' => 'redirectAdmin'], function () {
//     Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
//     Route::post('login', [AdminAuthController::class, 'login'])->name('admin.login.post');
// });

// end


// Sales routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', [SalesController::class, 'index'])->name('dashboard');
    Route::post('/customer/store', [SalesController::class, 'store'])->name('customer.store');
    Route::put('/customer/update/{id}', [SalesController::class, 'update'])->name('customer.update');
    Route::delete('/customer/destroy/{id}', [SalesController::class, 'destroy'])->name('customer.destroy');
    Route::post('/sales-report/store', [SalesController::class, 'storeReport'])->name('salesReport.store');
    Route::delete('/sales-report/id-image/{id}', [SalesController::class, 'deleteIdImage'])->name('IDPicture.delete');
    Route::delete('/sales-report/home-image/{id}', [SalesController::class, 'deleteHomeImage'])->name('HomePicture.delete');
    Route::delete('/sales-report/update/{id}', [SalesController::class, 'updateReport'])->name('salesReport.update');
    Route::delete('/sales-report/destroy/{id}', [SalesController::class, 'destroyReport'])->name('salesReport.destroy');
});

// end
require __DIR__ . '/auth.php';
