<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\PassportAuthController;
use App\Http\Controllers\API\DetailProfileUsers;


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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });


//App-Sio

Route::get('login', function () {
    return response()->json([
                    "status"=> "success",
                    "message"=> "Gagal Login",
                    "data" => "",
                ], 200);
})->name('login');

Route::post('register', [PassportAuthController::class, 'Register']);
Route::post('login-user', [PassportAuthController::class, 'loginUser']);



Route::middleware('auth:api')->group(function () {
    Route::get('profile-user-detail', [DetailProfileUsers::class, 'DetailProfile']);
    Route::post('profile-user-add', [DetailProfileUsers::class, 'AddDetailProfile']);
    Route::post('profile-user-addfollower', [DetailProfileUsers::class, 'AddFollower']);
    Route::get('detail-user', [PassportAuthController::class, 'UserInfo']);
});