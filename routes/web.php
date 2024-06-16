<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivityRegisterController;
use App\Http\Controllers\CompanyActivityController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyGuideController;
use App\Http\Controllers\CompanyUserController;
use App\Http\Controllers\GuideActivityController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyActivityController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


Route::middleware('auth')->group(function () {

    Route::resource('/companies', CompanyController::class)->middleware('isAdmin');

    Route::resource('/companies.users', CompanyUserController::class)->except('show');

    Route::resource('/companies.guides', CompanyGuideController::class)->except('show');

    Route::resource('/companies.activities', CompanyActivityController::class);
});

Route::get('/', HomeController::class)->name('home');

Route::get('/activities/{activity}', [ActivityController::class, 'show'])->name('activity.show');
Route::post('/activities/{activity}/register', [ActivityRegisterController::class, 'store'])->name('activities.register');

Route::middleware('auth')->group(function () {
    Route::get('/activities', [MyActivityController::class, 'show'])->name('my-activity.show');
    Route::delete('/activities/{activity}', [MyActivityController::class, 'cancel'])->name('my-activity.cancel');
    Route::get('/guides/activities/{activity}/pdf', [GuideActivityController::class, 'export'])->name('guide-activity.export');

    Route::get('/guides/activities', [GuideActivityController::class, 'show'])->name('guide-activity.show');
});


