@extends('emails.template')
@section('content')
    <h3>Hello {{ $user->name }},</h3><br/>
    <br/>
    It's that time again, it's been a month.
    <br/>
    <br/>
    To send your {{ number_format($pledge->amount, 4) }} LTC to <strong>{{ $pledge->project->name }}</strong> simply
    <a href="{{ url('/projects/'.$pledge->project_id.'/send_pledge') }}">Click Here</a>
    <hr/>
    LitecoinPledge
@stop
