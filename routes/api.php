<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\PassportAuthController;
use App\Http\Controllers\API\DetailProfileController;
use App\Http\Controllers\API\PostingController;


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
    Route::get('profile-user-detail', [DetailProfileController::class, 'DetailProfile']);
    Route::post('profile-user-add', [DetailProfileController::class, 'AddDetailProfile']);
    Route::post('profile-user-addfollower', [DetailProfileController::class, 'AddFollower']);

    Route::get('posting-get', [PostingController::class, 'GetPosting']);
    Route::post('posting-add', [PostingController::class, 'AddPostingImage']);
    Route::post('posting-like', [PostingController::class, 'AddLike']);

    Route::get('detail-user', [PassportAuthController::class, 'UserInfo']);
});
