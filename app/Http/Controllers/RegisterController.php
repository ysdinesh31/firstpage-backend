<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Validator;
use Auth;
use DB;
use App\User;
use Tymon\JWTAuth\JWTAuth;

class RegisterController extends Controller
{

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email',
            'password' => 'required|alphaNum|min:8',
        ]);
        $user = new User;
        $user->name = $request->input('name');
        $user->password = Hash::make($request->input('password'));
        $user->email = $request->input('email');
        $user->save();
        $user->created_by = $user->getKey();
        $user->save();
        return response('saved', 200);
    }
}
