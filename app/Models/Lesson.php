<?php

namespace App\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;
	use DefaultDatetimeFormat;
	
	//we need to put the same keyword $casts, it's not possible to write another name instead of this $casts
	protected $casts = [
		'video'=>'json'
	];
	
	//here we need to named the function as it is like at first we need to write set middle field name and at last Attribute
	public function setVideoAttribute($value){
		
		//data would come as the php associative array format
		//like: 
		/*
		"a"=>"value1", 
		"b"=>"value2",
		.....
		
		after converting associative to json it will be like this
		
		{
		'a':'value1',
		'b':'value2',
		........
		}
		
		*/
		//so we need to convert them in the json format
		
		$this->attributes['video'] = json_encode(array_values($value));
		
	}
	
	//to show in the admin panel of the laravel that is run in the http://127.0.0.1:8000/admin under the Lessons section
	public function getVideoAttribute($value){
		
		$resVideo = json_decode($value,true)?:[];//($value,true)?:[] means if not exists then return [] that means empty
		//dump($resVideo);//to show the output in the browser
		
		if(!empty($resVideo)){
			foreach($resVideo as $k=>$v){//like key:value pair
				$resVideo[$k]["url"]=$v["url"];//value of url will be passed into the key of url
				//some people also do like this
				//$resVideo[$k]["url"]=env("APP_URL")."uploads/".$v["url"];//that means the address will be added in front of the video url, we can use this only when we need the test of our item
				$resVideo[$k]["thumbnail"]=$v["thumbnail"];
			}
		}
		return $resVideo;
	}
}
