<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;
use DB;

class User extends Model implements JWTSubject, AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'name', 'email', 'role', 'created_by', 'deleted_by'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return ['type' => 'user'];
    }
    public function isAdmin()
    {
        if ($this->role == 1) return true;
        else return false;
    }

    public function data()
    {
        $data = [];
        $data[0] = DB::table('tasks')->where('assignedTo', $this->id)
            ->where('status', 'Assigned')
            ->count();
        $data[1] = DB::table('tasks')->where('assignedTo', $this->id)
            ->where('status', 'In Progress')
            ->count();
        $data[2] = DB::table('tasks')->where('assignedTo', $this->id)
            ->where('status', 'Completed')
            ->count();
        return $data;
    }

    public function Admindata()
    {
        $data = [];
        $data[0] = DB::table('tasks')
            ->where('status', 'Assigned')
            ->count();
        $data[1] = DB::table('tasks')
            ->where('status', 'In Progress')
            ->count();
        $data[2] = DB::table('tasks')
            ->where('status', 'Completed')
            ->count();
        return $data;
    }


    // public function assignedto()
    // {
    //     return $this->hasMany('App\Task', 'assignedTo', 'id');
    // }
    // public function createdby()
    // {
    //     return $this->hasMany('App\Task', 'created_by', 'id');
    // }
    // public function deletedby()
    // {
    //     return $this->hasMany('App\Task', 'deleted_by', 'id');
    // }
}
