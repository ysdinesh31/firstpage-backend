<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\Facades\Token;
use Validator;
use Auth;
use DB;
use App\User;
use Tymon\JWTAuth\Token as TymonToken;

class UserListingController extends Controller
{

    public $pages;


    public function __construct()
    {
        $this->pages = 5;
    }

    public function userlist(Request $request)
    {
        $user = $this->getUser(new TymonToken($request->cookie('token')));
        //$user = JWTAuth::User();
        //$search = $request->input('search');
        $name = $request->input('searchName');
        $email = $request->input('searchEmail');
        $createdBy = $request->input('searchCreatedBy');
        $res = DB::table('users as u3')->join('users as u2', 'u2.id', '=', 'u3.created_by')->leftJoin('users as u1', 'u1.id', '=', 'u3.deleted_by');
        //echo $res->get();
        if ($user->role == 'Admin') {
            $query = $res->select('u3.id', 'u3.name', 'u3.email', 'u3.role', 'u2.name as created_by', 'u1.name as delete_name');
            if ($name != "") {
                $query = $query->where('u3.name', 'LIKE', '%' . $name . '%');
            }
            if ($email != "") {
                $query = $query->where('u3.email', 'LIKE', '%' . $email . '%');
            }
            if ($createdBy != "") {
                $query = $query->where('u2.name', 'LIKE', '%' . $createdBy . '%');
            }
            // else if($type=='email') {
            //     $query = $res->select('u1.name','u1.email','u1.role','u2.name as create_name','u3.name as delete_name')->where('u1.email','LIKE','%'.$search.'%');
            // }else if ($type=='created_by'){
            //     $query = $res->select('u1.name','u1.email','u1.role','u2.name as create_name','u3.name as delete_name')->where('create_name','LIKE','%'.$search.'%');
            // }else if ($type=='deleted_by'){
            //     $query = $res->select('u1.name','u1.email','u1.role','u2.name as create_name','u3.name as delete_name')->where('delete_name','LIKE','%'.$search.'%');
            // }

        } else {

            $query = $res->select('u3.id', 'u3.name', 'u3.email', 'u3.role',  'u1.name as delete_name');
            if ($name != "") {
                $query = $query->where('u3.name', 'LIKE', '%' . $name . '%');
            }
            if ($email != "") {
                $query = $query->where('u3.email', 'LIKE', '%' . $email . '%');
            }

            // else if($type=='email') {
            //     $query = User::select('u1.name','u1.email','u2.name as create_name')->where('u1.email','LIKE','%'.$search.'%');
            // }else{
            //     return response('unauthorized',403);
            // }
        }
        return $query->paginate($this->pages);
    }

    public function delete(Request $request)
    {
        $user = $this->getUser(new TymonToken($request->cookie('token')));
        $this->validate($request, [
            'id' => 'exists:users,id'
        ]);
        if ($user->role == "Admin") {
            $temp = User::where('id', $request->id)->first();
            $temp->deleted_by = $user->id;
            $temp->save();
        } else return response('Unauthorized', 403);
    }

    public function create(Request $request)
    {
        $admin = $this->getUser(new TymonToken($request->cookie('token')));
        if ($admin->role == 'Admin') {
            $this->validate($request, [
                'name' => 'required|max:255',
                'email' => 'required|email',
                'password' => 'required|alphaNum|min:8',
            ]);
            $user = new User;
            $user->name = $request->name;
            $user->password = Hash::make($request->password);
            $user->email = $request->email;
            $user->created_by = $admin->getKey();
            $user->save();
            return response('saved', 200);
        }
    }

    public function changerole(Request $request)
    {
        $this->validate($request, [
            'id' => 'exists:users,id'
        ]);
        $admin = $this->getUser(new TymonToken($request->cookie('token')));
        if ($admin->role == 'Admin') {
            $user = User::where('id', $request->id)->first();
            if ($user->role == 'User') {
                $user->role = 'Admin';
                $user->save();
            } else return response('Unauthorized', 403);
        }
    }

    public function profile(Request $request)
    {
        //dd($request->cookie('token'));

        if ($user = $this->getUser(new TymonToken($request->cookie('token')))) {
            $data = $user->data();
            $adminData = $user->adminData();
            return response()->json(['user' => $user, 'data' => $data, 'adminData' => $adminData], 200);
        } else return response('Token', 403);
    }

    public function getUser(TymonToken $token)
    {
        $id = JWTAuth::decode($token)->get('sub');
        $user = User::where('id', $id)->first();
        return $user;
    }

    // public function sortbyname(){

    //     if($direction_name ==true){
    //         $direction_name=false;
    //         return User::orderby('name','desc')->paginate($this -> $pages)-> get();
    //     }
    //     else
    //     {
    //         $direction_name=true;
    //         return User::orderby('name','asc')->paginate($pages)-> get();

    //     }
    // }

    // public function sortbyemail(){
    //     if($direction_email ==true){
    //         $direction_email=false;
    //         return User::orderby('email',desc)->paginate($pages)-> get();
    //     }
    //     else {
    //         $direction_email=false;
    //         return User::orderby('name',asc)->paginate($pages)-> get();
    //     }
    // }

    public function search(Request $request)
    {
        $search = $request->search;
        return User::where('name', 'LIKE', '%' . $search . '%')->orWhere('email', 'LIKE', '%' . $search . '%')->get();
    }
}
