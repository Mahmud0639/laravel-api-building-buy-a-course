<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
//use App\Http\Controllers\Api\UserController;//no need to use this line because instead of this we have used group routing system

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

//['namespace'=>'Api']: This line specifies that all routes within this group will use the namespace Api. This means that when referencing controllers within this group, Laravel will look for them in the Api namespace. For example, if you reference a controller named UserController within this group, Laravel will look for it in the App\Http\Controllers\Api namespace.
//here the namespace will find the namespace that we already have declared in the RouteServiceProvider class as globally and put the Api namespace after the Controller like this App\Http\Controllers\Api
//we use :: operator to access the static method of any class
//we use -> operator to access any non-static object and use => for inserting key,value pair data in middle of the key and value
Route::group(['namespace'=>'Api'],function(){
	//if we use the global namespace system then below Route system won't work
	//Route::post('/login', [UserController::class, 'login']);
	//go through a middleware and what type of middleware, it is auth:sanctum type of middleware
	//because the login information is available in the sanctum
	//Route::post('/login','UserController@login');
	Route::group(['middleware'=>['auth:sanctum']],function(){
		
		Route::any('/courseList','CourseController@courseList');
		//Route::any('/courseList',[CourseController::class,'courseList']);
		Route::any('/courseDetail','CourseController@courseDetail');
		Route::any('/lessonList','LessonController@lessonList');//@lessonList means the method or function name
		Route::any('/lessonDetail','LessonController@lessonDetail');
		Route::any('/checkout','PaymentController@checkout');
	});
	
	//here we put it outside the middleware because stripe server don't know about our middleware mechanism so we put it here
	Route::any('/webGoHooks','PaymentController@webGoHooks');
});


//Route::post('/auth/login', [UserController::class, 'loginUser']);

?>