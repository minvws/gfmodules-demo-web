<?php

declare(strict_types=1);

use App\Http\Controllers\IndexController;
use App\Http\Controllers\Landing\HomeController;
use App\Http\Controllers\Landing\OidcLoginController;
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

Route::get('/', IndexController::class)->name('home');
Route::get('oidc/login', OidcLoginController::class)->name('oidc.login');

Route::middleware(['auth'])
    ->prefix('landing')
    ->name('landing.')
    ->group(function () {
        Route::get('home', [HomeController::class, 'home'])->name('home');
        Route::get('logout', [HomeController::class, 'logout'])->name('logout');
    });
