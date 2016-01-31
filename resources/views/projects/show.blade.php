@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h1 class="text-center">{{ $project->name }}</h1>
                @include('partials.status')
                <div class="row">
                        <div class="col-md-4">
                            <div class="well">
                                <div class="text-center">
                                    <img src="{{ $project->project_img }}" alt="Project Image">
                                    <br/>
                                    @if($project->verified == 1)
                                        <span class="label label-success">Verified</span>
                                    @else
                                        <span class="label label-danger">! Unverified !</span>
                                    @endif

                                    <p>Total Pledged: <strong>{{ number_format($project->total_pledged, 2) }}</strong> LTC</p>
                                    <p>Recurring LTC per month: {{ $project->monthly_pledged }}</p>
                                    <p>Total Monthly Pledges: {{ $project->monthly_users }} users</p>
                                </div>
                            </div>
                        </div>
                    <div class="col-md-8">
                        <div class="well">
                            <h4>Project Description</h4>
                            <p>{!! \Michelf\Markdown::defaultTransform($project->description)  !!}</p>
                            <h4>Make a pledge</h4>
                            <p>
                                You can make an anonymous pledge without an account, however we strongly recommend a
                                monthly pledge to ensure the project owner can pay for bills even after completion, and
                                for future development costs.
                            </p>
                            <a href="{{ url('/projects/'.$project->id.'/pledge?type=monthly') }}" class="btn btn-default">Recurring Pledge (Monthly)</a>
                            <a href="{{ url('/projects/'.$project->id.'/pledge?type=once') }}" class="btn btn-primary">One-Time Pledge</a>
                            <a href="{{ url('/projects/'.$project->id.'/pledge?type=anon') }}" class="btn btn-danger">Anonymous Pledge</a>
                            <hr/>

                        @if($project->verified == 0)
                                @can('verify', $project)
                                <form action="{{ url('/projects/'.$project->id.'/verify') }}" class="actually-inline" method="POST">
                                    {!! csrf_field() !!}
                                    <button class="btn btn-success">Verify Project as Legitimate</button>
                                </form>
                                @endcan
                            @endif
                            @can('destroy', $project)
                                <form action="{{ url('/projects/'.$project->id) }}" class="actually-inline" method="POST">
                                    {!! csrf_field() !!}
                                    {!! method_field('DELETE') !!}

                                    <button class="btn btn-danger">Delete Project</button>
                                </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop