<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Create User
     * @param Request $request
     * @return User
     */
    public function login(Request $request)
    {
			//this is only for debugging purpose when only we need to see the output
		        // return response()->json([
                // 'status' => true,
                // 'message' => 'User logged in Successfully',
                // 'token' => "random token"
            // ], 200);
		
        try {
            //Validated
            $validateUser = Validator::make($request->all(),
            [
                'avatar'=>'required',
                'type'=>'required',
			//here only we need to login there is no need of registration because here only we
			// want to save login user in the database with the help of laravel framework of registered user that is already registered with the help of google itself
                'name' => 'required',
                'email' => 'required',
                'open_id' => 'required',
                //"password"=>'required|min:6'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            $validated = $validateUser->validated();//here validated() is return an array and so $validated variable is an array variable there is no need to define array 
            $map = [];
            $map['type']=$validated['type'];
            $map['open_id'] = $validated['open_id'];
			
            $user = User::where($map)->first();

            if(empty($user->id)){
                $validated['token'] = md5(uniqid().rand(10000, 99999));
                $validated['created_at']=Carbon::now();
                //$validated['password'] = Hash::make($validated['password']);
                $userID = User::insertGetId($validated);
                $userInfo = User::where('id', '=', $userID)->first();
                $accessToken = $userInfo->createToken(uniqid())->plainTextToken;
                $userInfo->access_token = $accessToken;
                User::where('id', '=', $userID)->update(['access_token'=>$accessToken]);

                return response()->json([
                    //'status' => true,
					//we need to match with our projects key
					'code' => 200,
                    //'message' => 'User Created Successfully',
                    'msg' => 'User Created Successfully',
                    'data' => $userInfo
                ], 200);

            }
			//user previously logged in
			//next time when user would try to log in then everytime will save the new generated access token in the database with the help of below codes
            $accessToken = $user->createToken(uniqid())->plainTextToken;
            $user->access_token = $accessToken;
            User::where('open_id', '=', $validated['open_id'])->update(['access_token'=>$accessToken]);
            return response()->json([
                //'status' => true,
                'code' => 200,
                //'message' => 'User logged in Successfully',
                'msg' => 'User logged in Successfully',
                //'token' => $user
                'data' => $user
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Login The User
     * @param Request $request
     * @return User
     */
    // public function loginUser(Request $request)
    // {
        // try {
            // $validateUser = Validator::make($request->all(),
            // [
                // 'email' => 'required|email',
                // 'password' => 'required'
            // ]);

            // if($validateUser->fails()){
                // return response()->json([
                    // 'status' => false,
                    // 'message' => 'validation error',
                    // 'errors' => $validateUser->errors()
                // ], 401);
            // }

            // if(!Auth::attempt($request->only(['email', 'password']))){
                // return response()->json([
                    // 'status' => false,
                    // 'message' => 'Email & Password does not match with our record.',
                // ], 401);
            // }

            // $user = User::where('email', $request->email)->first();

            // return response()->json([
                // 'status' => true,
                // 'message' => 'User Logged In Successfully',
                // 'token' => $user->createToken("API TOKEN")->plainTextToken
            // ], 200);

        // } catch (\Throwable $th) {
            // return response()->json([
                // 'status' => false,
                // 'message' => $th->getMessage()
            // ], 500);
        // }
    // }
}
