<?php

declare(strict_types=1);

use App\Http\Controllers\FlowController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\Landing\HomeController;
use App\Http\Controllers\Landing\TimelineController;
use App\Http\Controllers\Auth\DigidMockController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', IndexController::class)->name('index');
Route::get('/flow', [FlowController::class, 'index'])->name('flow');
Route::get('/flow/consent', [FlowController::class, 'editConsent'])->name('flow-consent');
Route::post('/flow/consent', [FlowController::class, 'saveConsent'])->name('flow-consent');

if (config('auth.digid_mock_enabled')) {
    Route::get('oidc/login', [DigidMockController::class, 'login'])->name('oidc.login');
}

Route::middleware(['auth'])
    ->prefix('landing')
    ->name('landing.')
    ->group(function () {
        Route::get('home', [HomeController::class, 'home'])->name('home');
        Route::get('logout', [HomeController::class, 'logout'])->name('logout');
    });

Route::middleware(['auth'])
    ->prefix('timeline')
    ->name('timeline.')
    ->group(function () {
        Route::get('home', [TimelineController::class, 'home'])->name('home');
        Route::post('fetch', [TimelineController::class, 'fetch'])->name('fetch');
        Route::get('org/{ref}', [TimelineController::class, 'orgInfo'])->name('org_info');
    });
