<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class UserController extends Controller
{

    public const Accepted = 202;
    public const NoContent = 204;
    public const Created = 201;
    public const BadRequest = 400;
    public const Success = 200;

    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('testBackendDev')->accessToken;
            return response()->json(
                [
                    'responCode' => Self::Accepted,
                    'success' => $success,
                ], 
            Self::Success);
        }
        else{
            return response()->json(
                [
                    'error'=>'Unauthorised',
                    'responCode' => Self::BadRequest
                ], 
            Self::BadRequest);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'conf_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], Self::BadRequest);            
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('testBackendDev')->accessToken;
        $success['name'] =  $user->name;

        return response()->json(['success'=>$success,'responCode' => Self::Created], Self::Success);
    }
} 