<?php

declare(strict_types=1);

use App\Http\Controllers\AddressBookController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\FlowController;
use App\Http\Controllers\IndexController;
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
Route::post('/flow', [FlowController::class, 'retrieveTimeline'])->name('flow.retrieve-timeline');
Route::get('/flow/consent', [FlowController::class, 'editConsent'])->name('flow-consent');
Route::post('/flow/consent', [FlowController::class, 'storeConsent'])->name('flow-consent.store');
Route::get('/flow/authorization', [FlowController::class, 'editAuthorization'])->name('flow-authorization');
Route::post('/flow/authorization', [FlowController::class, 'storeAuthorization'])->name('flow-authorization.store');

Route::get('/address-book', [AddressBookController::class, 'index'])->name('address-book');
Route::get('/address-book/org/{ref}', [AddressBookController::class, 'orgInfo'])->name('address-book.org-info');

if (config('auth.digid_mock_enabled')) {
    Route::get('oidc/login', [DigidMockController::class, 'login'])->name('oidc.login');
}

Route::middleware(['auth'])
    ->group(function () {
        Route::post('logout', LogoutController::class)->name('logout');
    });

Route::middleware(['auth'])
    ->prefix('timeline')
    ->name('timeline.')
    ->group(function () {
        Route::get('fetch', [TimelineController::class, 'fetch'])->name('fetch');
    });
