<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Cookie;

class AuthController extends Controller
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */


    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email|max:255',
            'password' => 'required|min:8',
        ]);

        try {

            if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
                return response()->json(['User not found'], 404);
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], 500);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], 500);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent' => $e->getMessage()], 500);
        }
        $user = JWTAuth::User();
        $response = new \Illuminate\Http\Response($user);
        if ($user->deleted_by != -1) return response()->json(['User was deleted'], 404);

        $response->withCookie(new cookie('token', $token, strtotime('now+60 minutes'), '/', null, false, false, false, 'lax'));

        return $response;

        //return response()->header('Set-Cookie',cookie("Sandy","Dinesh",60));
    }
}
