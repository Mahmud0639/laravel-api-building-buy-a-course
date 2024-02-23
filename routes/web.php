<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\HomeController;

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

Route::get('/', function () {
    return view('welcome');
});


//it is not defined in the api.php file because we don't need to make a 
//secure communication. It can be accessed by any third party api or can
//redirect this route who wants.This route will be called by stripe itself
//while it sends us a success url
Route::get('/success',[HomeController::class, 'success']);
Route::get('/cancel',[HomeController::class, 'cancel']);
