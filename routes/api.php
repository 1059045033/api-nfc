<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NfcController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/nfc', [NfcController::class, 'index']); // 通用查询接口

// 实际用的接口
Route::post('/nfc/write', [NfcController::class, 'store']);          // 接口2: NFC 写入
Route::get('/nfc/{nfcId}', [NfcController::class, 'showByNfcId']);  // 接口3: NFC ID 查询
Route::get('/wechat/{wechatId}/data', [NfcController::class, 'showByWechatId']); // 接口4: 微信号查询

// 认证接口（添加到 Sanctum 默认路由之前）
//Route::post('/login', [AuthController::class, 'login']);
//Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
//
//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});



// 添加一个公开的测试接口
Route::get('/hello', function () {
    return response()->json(['message' => 'Hello, API!']);
});
