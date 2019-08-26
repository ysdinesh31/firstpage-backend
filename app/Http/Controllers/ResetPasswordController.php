<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;
Use Auth;
use DB;
use App\User;

class ResetPasswordController extends Controller{


    public function reset(Request $request){
        $this -> validate($request,[
            'Old_Password' => 'required|min:8',
            'New_Password' => 'required|min:8',
            'Confirm_Password' => 'required|min:8',
        ]);

        $user = JWTAuth::User();
        if(Hash::check($request-> Old_Password,$user->password)){
            if($request-> New_Password == $request-> Confirm_Password){
                $user->password = Hash::make('Confirm_Password');
                $user ->save();
                return response('dikshant',200);
            }
        }else{
            return response([2],400);
        }
        
    }    

}