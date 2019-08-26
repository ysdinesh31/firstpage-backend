<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\Token;
use Validator;
use Auth;
use DB;
use App\User;
use App\Jobs\MailJob;

class ForgotPasswordController extends Controller
{

    public function forgot(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email|max:255',
        ]);
        $emailid = $request->email;
        $customClaims = [
            'sub' => $emailid,
            'type' => 'reset'
        ];
        $user = User::where('email', $emailid)->first();
        if ($user) {
            $payload = JWTfactory::customClaims($customClaims)->make();
            // $payload = JWTFactory::make($customClaims);
            $token = JWTAuth::encode($payload, env('JWT_SECRET'));
            Queue::later(5, new MailJob($emailid, $token));
            return response('mail sent')->header('Bearer', $token);
        } else {
            return response()->json(['Entered Email not registered'], 404);
        }
    }

    public function reset(Request $request)
    {

        $this->validate($request, [
            'password' => 'required|min:8',
        ]);
        $token = JWTAuth::getToken();
        try {

            $details = JWTAuth::decode($token, env('JWT_SECRET'));
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['link expired'], 403);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['link invalid'], 403);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['link invalid' => $e->getMessage()], 403);
        }

        $type = $details->get('type');

        if ($type == 'reset') {
            $emailid = $details->get('sub');

            $user = User::where('email', $emailid)->first();
            $user->password = Hash::make($request->input('password'));
            $user->save();
        } else return response('invalid link', 403);
    }
}
