<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DataController;
use App\Http\Controllers\Api\SeitonController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/user', [AuthController::class, 'getUser'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->post('/time-in', [AttendanceController::class, 'timeIn']);
Route::middleware('auth:sanctum')->post('/time-out', [AttendanceController::class, 'timeOut']);
Route::middleware('auth:sanctum')->post('/seiton', [SeitonController::class, 'scoreSeiton']);


Route::get('/ranking', [DataController::class, 'getRanking']);
Route::get('/seiton/rank', [DataController::class, 'getSeitonRanking']);
