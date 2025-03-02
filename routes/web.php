<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('chargpt.chat');
});

use App\Http\Controllers\ChatController;

Route::post('/chat', [ChatController::class, 'streamChat']);

Route::get('/gethistory', [ChatController::class, 'getHistory']);