<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminProviderAccountController;
use App\Http\Controllers\BusinessAccountStatusController;
use App\Http\Controllers\BusinessAuthController;
use App\Http\Controllers\BusinessDashboardController;
use App\Http\Controllers\BusinessProfileController;
use App\Http\Controllers\ProviderReviewController;
use App\Http\Controllers\PublicServiceProviderController;
use App\Http\Controllers\SeekerAuthController;
use App\Http\Controllers\SeekerProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/business/register', [BusinessAuthController::class, 'showRegistrationForm'])->name('business.register');
    Route::post('/business/register', [BusinessAuthController::class, 'register'])->name('business.register.store');
    Route::get('/business/login', [BusinessAuthController::class, 'showLoginForm'])->name('business.login');
    Route::post('/business/login', [BusinessAuthController::class, 'login'])->name('business.login.store');
});

Route::middleware(['auth', 'service-provider:any'])->group(function (): void {
    Route::get('/business/account-status', BusinessAccountStatusController::class)->name('business.account.status');
    Route::post('/business/logout', [BusinessAuthController::class, 'logout'])->name('business.logout');
});

Route::middleware(['auth', 'service-provider'])->group(function (): void {
    Route::get('/business/dashboard', BusinessDashboardController::class)->name('business.dashboard');
    Route::get('/business/profile', [BusinessProfileController::class, 'show'])->name('business.profile.show');
    Route::get('/business/profile/edit', [BusinessProfileController::class, 'edit'])->name('business.profile.edit');
    Route::put('/business/profile', [BusinessProfileController::class, 'update'])->name('business.profile.update');
    Route::patch('/business/profile/hiring-availability', [BusinessProfileController::class, 'updateHiringAvailability'])->name('business.profile.hiring-availability');
    Route::post('/business/profile/profile-photo', [BusinessProfileController::class, 'updateProfilePhoto'])->name('business.profile.profile-photo.store');
    Route::post('/business/profile/brand-photo', [BusinessProfileController::class, 'updateBrandPhoto'])->name('business.profile.brand-photo.store');
    Route::post('/business/profile/work-samples', [BusinessProfileController::class, 'addWorkSample'])->name('business.profile.work-samples.store');
    Route::delete('/business/profile/profile-photo', [BusinessProfileController::class, 'deleteProfilePhoto'])->name('business.profile.profile-photo.destroy');
    Route::delete('/business/profile/brand-photo', [BusinessProfileController::class, 'deleteBrandPhoto'])->name('business.profile.brand-photo.destroy');
    Route::delete('/business/profile/work-samples/{index}', [BusinessProfileController::class, 'deleteWorkSample'])->whereNumber('index')->name('business.profile.work-samples.destroy');
});

Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.store');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
    Route::patch('/providers/{provider}/approve', [AdminProviderAccountController::class, 'approve'])->name('providers.approve');
    Route::patch('/providers/{provider}/reject', [AdminProviderAccountController::class, 'reject'])->name('providers.reject');
    Route::patch('/providers/{provider}/suspend', [AdminProviderAccountController::class, 'suspend'])->name('providers.suspend');
    Route::patch('/providers/{provider}/activate', [AdminProviderAccountController::class, 'activate'])->name('providers.activate');
});

Route::get('/providers/{provider}', [PublicServiceProviderController::class, 'show'])->name('providers.show');
Route::get('/providers/{provider}/reviews', [ProviderReviewController::class, 'index'])->middleware('public-provider')->name('providers.reviews.index');

Route::middleware('auth')->group(function (): void {
    Route::post('/providers/{provider}/reviews', [ProviderReviewController::class, 'store'])->middleware('public-provider')->name('providers.reviews.store');
    Route::put('/providers/{provider}/reviews/{review}', [ProviderReviewController::class, 'update'])->middleware('public-provider')->name('providers.reviews.update');
    Route::delete('/providers/{provider}/reviews/{review}', [ProviderReviewController::class, 'destroy'])->middleware('public-provider')->name('providers.reviews.destroy');
});

Route::middleware('guest')->prefix('seeker')->name('seeker.')->group(function (): void {
    Route::get('/register', [SeekerAuthController::class, 'registrationForm'])->name('register');
    Route::post('/register', [SeekerAuthController::class, 'register'])->middleware('throttle:10,1')->name('register.store');
    Route::get('/login', [SeekerAuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [SeekerAuthController::class, 'login'])->middleware('throttle:10,1')->name('login.store');
});
Route::middleware(['auth', 'seeker'])->prefix('seeker')->name('seeker.')->group(function (): void {
    Route::get('/dashboard', [SeekerAuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [SeekerAuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [SeekerProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [SeekerProfileController::class, 'update'])->name('profile.update');
});
