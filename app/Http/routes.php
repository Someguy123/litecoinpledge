<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use App\Pledge;
use App\UserPledge;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

function rand_str()
{
    return hash('sha256', substr(str_shuffle(sha1(rand(0, 999999999))), 0, rand(20, 45)));
}

Route::group(['middleware' => ['web']], function () {
    Route::get('/', function () {
        $top_projects = App\Project::limit(6)->orderBy('total_pledged', 'desc')->get();
        return view('welcome', compact('top_projects'));
    });

    Route::group(['path' => '/account'], function() {
        Route::get('account', function(Request $r) {
            $user_pledges = $r->user()->u_pledges()->with('project')->get();
            $user = auth()->user();
            return view('account', compact('user_pledges', 'user'));
        });
    });

    Route::get('projects/{project}/pledge', function(Request $r, \App\Project $project) {
        $type = $r->input('type', '');
        if($type !== "monthly" && $type !== "once" && $type !== "anon") {
            return redirect()->back();
        }
        if(Auth::guest() && ($type == "monthly" || $type == "once")) {
            return redirect()->guest(Route::current());
        }
        return view('projects.pledge', compact('project', 'type'));
    });

    Route::post('projects/{project}/pledge', 'PledgeController@createPledge');

    Route::post('projects/{project}/verify', 'ProjectController@verify');
    Route::get('projects/{project}', 'ProjectController@show')->where('project', '[0-9]+');
    Route::delete('projects/{project}', 'ProjectController@destroy')->where('project', '[0-9]+');
    Route::resource('projects', 'ProjectController');

});

Route::group(['middleware' => 'web'], function () {
    Route::auth();

    Route::get('/home', 'HomeController@index');
});
