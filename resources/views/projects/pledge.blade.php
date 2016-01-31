@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h1 class="text-center">Making a pledge to <span
                            style="text-decoration: underline;">{{ $project->name }}</span></h1>
                    <div>
                        @include('partials.status')
                        @unless($project->verified == 1)
                            <div class="alert alert-danger">WARNING: The owners of this project have <strong>not been verified</strong> by LitecoinPledge. Please make sure that this project is advertised by their official website (if applicable)</div>
                        @endunless

                        <form action="{{ url('/projects/'.$project->id.'/pledge') }}" method="POST">
                            {{ csrf_field() }}

                            <label for="type">Type:</label>
                            <input type="hidden" name="type" value="{{ $type }}">
                            @if($type == "monthly")
                                <h4>Monthly</h4>
                                <div class="form-group">
                                    <label for="amount">LTC Amount:</label>
                                    <input placeholder="0.00" type="text" class="form-control" name="amount" />
                                </div>
                                <button class="btn btn-primary">Submit</button>
                            @elseif($type == "once")
                                <h4>One Time</h4>
                                <div class="form-group">
                                    <label for="amount">LTC Amount:</label>
                                    <input placeholder="0.00" type="text" class="form-control" name="amount"/>
                                </div>
                                <button class="btn btn-primary">Submit</button>
                            @elseif($type == "anon")
                                <h4>Anonymous (One Time)</h4>
                                <p>Be aware that sending anonymously means there will be no link to your account, as this is a shared address for the project.</p>
                                <p>Please send any amount of LTC to the following address:</p>
                                <pre>{{ $project->ltc_address }}</pre>
                            @endif
                        </form>

                    </div>

                </div>
            </div>
        </div>
    </div>

@stop