<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    //our success route method
	public function success(){
		//return ['hello'=>'hi'];
		//when the success method is called then we need to show a ui
		return view("success");
		
	}
	
	public function cancel(){
		
		return [""];
	}
}
