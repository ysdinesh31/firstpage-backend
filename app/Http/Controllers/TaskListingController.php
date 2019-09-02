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
use App\Task;
use Tymon\JWTAuth\Token as jwtToken;
use App\Events\TaskCreateEvent;

class TaskListingController extends Controller
{

    public function __construct()
    {
        $this->pages = 5;
    }

    public function tasklist(Request $request)
    {
        $user = $this->getUser(new jwtToken($request->cookie('token')));
        $title = $request->searchTitle;
        $assignedby = $request->searchAssignedBy;
        //echo $search;
        // $res = DB::table('tasks as u3')
        //     ->join('users as u2', 'u2.id', '=', 'u3.created_by')
        //     ->join('users as u4', 'u4.id', '=', 'u3.assignedTo')
        //     ->leftJoin('users as u1', 'u1.id', '=', 'u3.deleted_by')
        //     ->select('u3.id', 'u3.description', 'u3.status', 'u3.due_date', 'u3.title', 'u3.assignedTo', 'u4.name as assignee_name', 'u2.name as create_name', 'u1.name as delete_name', 'u1.id as delete_id', 'u2.id as create_id', 'u4.id as assignee_id');
        $res = Task::with('assignedto', 'createdby', 'deletedby');
        // echo (json_encode($res->first()->toArray()["assignedto"]));




        $query = $res;
        if ($title != "")
            $query = $res->where('title', 'LIKE', '%' . $title . '%');
        if ($assignedby != "")
            $query =  $query->whereHas('createdby', function ($query) use ($assignedby) {
                return $query->where('name', 'LIKE', '%' . $assignedby . '%');
            });

        if ($user->role == 'Admin') {

            return $query->paginate($this->pages);
        } else {
            return $query->where('assignedTo', $user->id)->paginate($this->pages);
        }
    }

    public function create(Request $request)
    {
        $user = $this->getUser(new jwtToken($request->cookie('token')));
        $this->validate($request, [
            'title' => 'required|max:100',
            'description' => 'required|max:255',
            'due_date' => 'required|date',
            'assignedTo' => 'exists:users,id'

        ]);
        $task = new Task;
        $task->title = $request->title;
        $task->description = $request->description;
        if ($user->role == 'Admin') {
            $task->assignedTo = $request->assignedTo;
        } else {
            $task->assignedTo = $user->id;
        }
        $task->due_date = $request->due_date;
        $task->created_by = $user->id;
        $task->save();
        event(new TaskCreateEvent($task));
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'exists:users,id'

        ]);
        $user = $this->getUser(new jwtToken($request->cookie('token')));
        $task = Task::find($request->id);
        $task->deleted_by = $user->id;
        $task->save();
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:100',
            'description' => 'required|max:255',
            'due_date' => 'required|date',
            'status' => Rule::in(['Assigned', 'In Progress', 'Completed'])
        ]);
        $task = Task::find($request->id);
        $task->title = $request->title;
        $task->description = $request->description;
        $task->due_date = $request->due_date;
        $task->status = $request->status;
        $task->save();
    }


    public function getUser(jwtToken $token)
    {

        $id = JWTAuth::decode($token)->get('sub');
        $user = User::where('id', $id)->first();
        return $user;
    }
}
