<?php

declare(strict_types=1);

use App\Http\Controllers\IndexController;
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

Route::get('/', [IndexController::class, 'index'])->name('index');
Route::post('/locate', [IndexController::class, 'locate'])->name('locate');
Route::post('/step1', [IndexController::class, 'step1'])->name('step_1');
Route::post('/step2', [IndexController::class, 'step2'])->name('step_2');
Route::post('/step3', [IndexController::class, 'step3'])->name('step_3');
Route::get('/step4', [IndexController::class, 'step4'])->name('step_4');
