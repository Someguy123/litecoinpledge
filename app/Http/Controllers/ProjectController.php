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
        $project->save();
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
    function verify(Project $project) {
        $this->authorize('verify', $project);
        $project->verified = 1;
        $project->save();
        return redirect()->back()->with('status', 'Project has been verified.');
    }
}
