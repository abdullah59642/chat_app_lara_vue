<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\authController;
use App\Http\Controllers\createConversation;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['middleware' => ['auth:sanctum']], function(){
    Route::get('/getchats', [createConversation::class, 'getConversations']);
    Route::post('/markChatAsRead', [createConversation::class, 'markConversationAsRead']);
    Route::post('/createconversation', [createConversation::class, 'createConversation']);
    Route::get('/getuserid', [createConversation::class, 'getAuthUserId']);
    Route::post('/conversationdata', [createConversation::class, 'getConversationData']);    
    Route::post('/searchuser', [createConversation::class, 'searchUser']);
    Route::post('/sendmessage', [createConversation::class, 'sendMessage']);
    Route::get('/showchat/{id}', [createConversation::class, 'showChat']);
    Route::get('/accountdata', [createConversation::class, 'showAccountData']);
    Route::post('/changepicture', [createConversation::class, 'changePicture']);
    Route::post('/logout', [authController::class, 'logoutUser']);
});

Route::post('/login', [authController::class, 'loginUser']);
Route::post('/register', [authController::class, 'registerUser']);