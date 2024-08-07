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

// メール認証用機能
// Route::middleware(['auth'])->group(function () {
//     Route::get('/addresscheck', [AttendanceController::class, 'index']);
// });

Route::middleware('auth')->group(function () {
    Route::get('/', [AttendanceController::class, 'index']);

    // 出退勤打刻
    Route::post('/workin', [AttendanceController::class, 'workin']);
    Route::patch('/workout', [AttendanceController::class, 'workout']);

    // 休憩打刻
    Route::patch('/breakin', [AttendanceController::class, 'breakin']);
    Route::patch('/breakout', [AttendanceController::class, 'breakout']);
    
    // 日付別勤怠一覧
    Route::get('/attendance', [AttendanceController::class, 'attend']);

    // ユーザー別勤怠一覧
    Route::get('/userattend', [AttendanceController::class, 'userattend']);

    // ユーザ一覧
    Route::get('/userlist', [AttendanceController::class, 'userlist']);
});
    
        
        
 








