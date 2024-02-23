<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Course;
class CourseController extends Controller
{
    //
	public function courseList(){
		//if we want to get everything from the database at a time not using specific fields
		//$result = Course::get();
		$result = Course::select('name','thumbnail','lesson_num','price','id')->get();
		//we can write also like this to get the same output of the json response from server
		//$result = Course::get(['name','thumbnail','lesson_num','price','id']);
		return response()->json([
			'code'=>200,
			'Msg'=>'My course list here',
			'data'=>$result
		],200);
	}
	
	//here we use Request to query any field data like as id
	public function courseDetail(Request $request){
		$id = $request->id;
		
		try{
			$result = Course::where('id','=',$id)->select('id','name','user_token','description','thumbnail','lesson_num','video_length','price')->first();//only get the first item because we just want one item from the query
			return response()->json([
				'code'=>200,
				'Message'=>'My course details is here',
				'data'=>$result
			],200);
		}catch(\Throwable $e){
			return response()->json([
				'code'=>500,
				'Message'=>'Server internal error.',
				'data'=>$e->getMessage()
			],500);
		}
		
	}
}
