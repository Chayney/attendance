<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

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

Route::middleware('auth')->group(function () {
    Route::get('/', [AttendanceController::class, 'index']);

    // 出退勤打刻
    Route::post('/workin', [AttendanceController::class, 'store']);
    Route::patch('/workout', [AttendanceController::class, 'workout']);

    // 休憩打刻
    Route::patch('/breakin', [AttendanceController::class, 'breakin']);
    Route::patch('/breakout', [AttendanceController::class, 'breakout']);
    
    Route::get('/attendance', [AttendanceController::class, 'attend']);
    
});


