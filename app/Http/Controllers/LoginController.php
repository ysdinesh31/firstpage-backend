<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;

use Validator;
use Auth;
use DB;

class LoginController extends Controller
{

    public function checklogin(Request $request)
    {
        $email = $request->email;
        $password = $request->password;


        $result = DB::table('users')
            ->where('email', $email)
            ->first();
        if (!is_null($result)) {
            if (Hash::check($password, $result->password)) {
                return response([1], 200);
            } else {
                return response([2], 400);
            }
        } else return response("New User? Sign Up!", 401);
    }

    public function successlogin()
    {
        return response(['welcome'], 200);
    }
    //
}
