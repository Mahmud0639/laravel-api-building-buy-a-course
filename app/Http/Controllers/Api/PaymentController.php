<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Stripe\Webhook;
use Stripe\Customer;
use Stripe\Price;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Exception\UnexpectedValueException;
use Stripe\Exception\SignatureVerificationException;
use App\Models\Course;
use App\Models\Order;

class PaymentController extends Controller
{
    //
	public function checkout(Request $request){
		try{
				//after hitting the submit button in the postman with the bearer token it will give us user info based on the token of the specific user
		//this is for users who buys course
		$courseId = $request->id;//it could be any random id now we pass it randomly from postman to get any course info(it can be 2,3,4 etc.)
		$user = $request->user();
		$token = $user->token;
		
		
		//stripe key
		Stripe::setApiKey(
			"---your-stripe-key-here---"
		);
		
		$searchCourse = Course::where('id','=',$courseId)->first();
		//as we want to get with the specific id so it is not an error becasue response come
		if(empty($searchCourse)){
			return response()->json([
				'code'=>204,//response code 204 means resource not found
				'message'=>"No course found",
				'data'=>$searchCourse
			
			],200);//to prevent the crash we give 200 as response code here
		}
		
		$orderMap = [];
		$orderMap["course_id"] = $courseId;
		$orderMap["user_token"] = $token;
		$orderMap["status"] = 1;//success: 1 means order has placed by a user
		
		//now we do a query based on the $orderMap list
		$orderRes = Order::where($orderMap)->first();
		
		//if the same user try to buy the same course then it will make a query to know if is is ordered or not,
		//if already ordered then show the message Order already exist. Then the else block will be called and insert
		//data to the database and at first make the status rows data as 0, because after successfully stripe payment 
		//completion the status result would be set to 1 instead of 0
		if(!empty($orderRes)){
			return response()->json([
				'code'=>409,//409 means conflicting, order already exits but again trying to do order with the same
				'message'=>'The order already exist!',
				'data'=>''
			],200);//to prevent the crash we give 200 as response code here
		}
		
		//if the order does not exist then only below codes execute for the specific course id
		
		$your_domain = env('APP_URL');
		$map = [];
		$map["user_token"] = $token;
		$map["course_id"] = $courseId;
		$map["total_amount"] = $searchCourse->price;//which course user has bought, we need to put that course price
		$map["status"] = 0;
		$map["created_at"] = Carbon::now();
		
		$orderNum = Order::insertGetId($map);//it would insert data and give us corresponding id of the inserted row and update the status : 1 to 0
		
		
		$checkOutSession = Session::create([
		
		/*
		//output will be like this:
		{
    "line_items": [
        {
            "price_data": {
                "currency": "USD",
                "product_data": {
                    "name": "Sample Course",
                    "description": "Sample Course Description"
                },
                "unit_amount": 1230
            }
        }
    ]
}

		*/
		
			'line_items'=>[[
				
				'price_data'=>[
					'currency'=>'USD',
					'product_data'=>[
						'name'=>$searchCourse->name,
						'description'=>$searchCourse->description
					],
					//it would do like this: 12.30 to 1230 cents
					'unit_amount'=>intval(($searchCourse->price)*100);
				
				],
				'quantity'=>1,
			
			
			]],
			
			'payment_intent_data'=>[
			
				'metadata'=>['order_num'=>$orderNum,'user_token'=>$token],
			],
			
			'metadata'=>['order_num'=>$orderNum,'user_token'=>$token],
			'mode'=>'payment',
			'success_url'=>$your_domain.'success',
			'cancel_url'=>$your_domain.'cancel'
		
		
		]);
		
		return response()->json([
			'code'=>200,
			'message'=>'Order has been placed successfully.',
			'data'=>$checkOutSession->url//this url will come from stripe api and the url is the payment page url through stripe
		
		],200);
		
		
		//using try-catch we can get better way to know what is the exact error
		}catch(\Throwable $th){
			return response()->json([
			
				'status'=>false,
				'message'=>$th->getMessage()
				
				
			],500);
		
		
		}
	}
	//for successful payment info from stripe
	public function webGoHooks(){
		Log::info("starts here...");
		Stripe::setApiKey('---your-stripe-key-here---');
		$endPointSecret = '---your-end-point-secret-here---';
		//This variable is assigned the value of the raw POST data retrieved from the HTTP request body. It allows you to work with the raw data directly in your PHP script.
		$payload = @file_get_contents('php://input');//for a special memory saving for the next time fast checking
		//$sigHeader = $_SERVER('HTTP_STRIPE_SIGNATURE');//it would be third braces not first braces
		$sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'];
		$event = null;
		
		Log::info('Set up buffer and handshake done.');
		
		try{
			$event = \Stripe\Webhook::constructEvent(
				$payload,
				$sigHeader,
				$endPointSecret
			)
		}catch(\UnexpectedValueException $e){//this exception is coming from php own
			Log::info('UnexpectedValueException '.$e);
			http_response_code(400);
			exit();
		}catch(\Stripe\Exception\SignatureVerificationException $e){//this comes from Stripe server
			Log::info('SignatureVerificationException'.$e);
			http_response_code(400);
			exit();
		}
		
		if($event->type=="charge.succeeded"){
			$session = $event->data->object;
			$metadata = $session["metadata"];
			//this order_num and user_token are the same as we sent to the server with the help of metadata
			$orderNum = $metadata->order_num;
			$userToken = $metadata->user_token;
			
			Log::info("Order id ".$orderNum);
			
			$map = [];
			$map["status"] = 1;
			$map["updated_at"] = Carbon::now();
			//for finding out the specific rows to update with the new data
			$whereMap = [];
			$whereMap["user_token"] = $userToken;
			$whereMap["id"] = $orderNum;
			
			Order::where($whereMap)->update($map);
		}
		//after successful payment submission and saving data in the database
		http_response_code(200);
		
	}
}
