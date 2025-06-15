<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;

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

Route::get('/', [ProductController::class, 'index']);
Route::get('/login', [UserController::class, 'showLogin'])->name('login');
Route::post('/login', [UserController::class, 'login']);
Route::get('/register', [UserController::class, 'showRegister']);
Route::post('/register', [UserController::class, 'register']);
Route::get('/products/{product_id}', [ProductController::class, 'showDetail']);
Route::get('/buy/{product_id}', [ProductController::class, 'handleBuyAccess']);


Route::middleware('auth')->group(function () {
    Route::post('/products/{product}/comment', [ProductController::class, 'comment'])->name('products.comment');
    Route::post('/products/{product_id}/like', [ProductController::class, 'like']);
    Route::post('/products/{product_id}/unlike', [ProductController::class, 'unlike']);
    Route::get('/purchase/{product_id}', [ProductController::class, 'showPurchase']);
    Route::post('/buy', [ProductController::class, 'confirmPurchase']);
    Route::get('/mypage', [UserController::class, 'showMypage']);
    Route::get('/purchase/address/{item_id}', [UserController::class, 'showAddress']);
    Route::post('/purchase/address/{item_id}', [UserController::class, 'postAddress']);
    Route::get('/mypage/profile', [UserController::class, 'showProfile']);
    Route::post('mypage/profile', [UserController::class, 'postProfile']);
    Route::get('/sell', [ProductController::class, 'showSell']);
    Route::post('/sell', [ProductController::class, 'postSell']);
});