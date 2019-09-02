<?php


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/



use Illuminate\Http\Request;

$router->group(['middleware' => 'cors'], function ($router) {
    $router->post('/login/authBroadcast', function (Request $request) {
        $pusher = new Pusher\Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'), array('cluster' => env('PUSHER_APP_CLUSTER'), 'useTLS' => 'true'));
        return $pusher->socket_auth($request->channel_name, $request->socket_id);
    });
    $router->post('/login/checklogin', 'AuthController@postLogin');
    $router->post('/login/register', 'RegisterController@store');
    $router->post('/login/forgot', 'ForgotPasswordController@forgot');
    $router->post('/login/reset', 'ForgotPasswordController@reset');
    $router->group(['middleware' => 'auth'], function ($router) {
        $router->post('/login/userlist', 'UserListingController@userlist');
        $router->post('/login/delete', 'UserListingController@delete');
        $router->post('/login/changerole', 'UserListingController@changerole');
        $router->post('/login/profile', 'UserListingController@profile');
        $router->post('/login/tasklist', 'TaskListingController@tasklist');
        $router->post('/login/addtask', 'TaskListingController@create');
        $router->post('/login/deletetask', 'TaskListingController@delete');
        $router->post('/login/updatetask', 'TaskListingController@update');
        $router->post('/login/', 'TaskListingController@');
    });
    $router->options('/{route:.*}', function () {
        // $router->options('/login/userlist', function () {
        return response(200);
    });
});

//$router->post('/login/checklogin','LoginController@postLogin');
//$router->get('/login/successlogin','LoginController@successlogin');



//$router->group(['middleware' => 'auth'], function ($router) { });
