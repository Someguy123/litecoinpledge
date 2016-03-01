<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Project;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ProjectController extends Controller
{
    function __construct() {
        $this->middleware('auth', ['except' => ['show', 'index']]);
    }

    function index() {
        $projects = Project::orderBy('total_pledged', 'desc')->paginate(12);
        return view('projects.index', compact('projects'));
    }

    function create() {
        return view('projects.create');
    }
    function store(ProjectRequest $r) {
        $project = new Project($r->only('name', 'description'));
        $project->user_id = $r->user()->id;
        $project->save();
        if(!WalletController::generateAddress($project, true)) {
            return redirect()->back()->withErrors("Sorry. The address pool is low, so we were unable to create your project at this time. Try again in an hour");
        }
        return redirect('/projects/'.$project->id);
    }
    function show(Project $project) {
        return view('projects.show', compact('project'));
    }
    function destroy(Project $project) {
        $this->authorize('destroy', $project);
        $project->delete();
        return redirect('/projects')->with('status', 'Project deleted');
    }
    function withdraw(Request $r, Project $project) {
        $this->authorize('withdraw', $project);
        app('db')->transaction(function() use($project, $r, &$response) {
            // for safety, we lock the balance
            $p = app('db')->table('projects')->where('id', $project->id)->lockForUpdate();
            $p = $p->first();
            $balance = $p->project_balance;
            if ($balance > 0) {
                // clear the project balance
                app('db')->table('projects')->where('id', $project->id)->update(['project_balance' => '0']);
                app('db')->table('users')->where('id', $r->user()->id)->increment('balance', $balance);
                return $response = redirect()->back()->with('status', 'Successfully transferred project balance to your balance');
            }
            return $response = redirect()->back()->withErrors('Empty project balance');
        });
        return $response;

    }
    function verify(Project $project) {
        $this->authorize('verify', $project);
        $project->verified = 1;
        $project->save();
        return redirect()->back()->with('status', 'Project has been verified.');
    }
}
