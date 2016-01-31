@extends('emails.template')
@section('content')
    <h3>Hello {{ $user->name }},</h3><br/>
    <br/>
    We sent you an email a few days ago regarding your pledge. You haven't sent that pledge since then.
    If you were busy, missed the email etc. just click on the link and pay for your pledge, and it'll continue like normal.
    <br/>
    <br/>
    If you don't pay your pledge within the next 3 days, we will remove your pledge, and send you an email to notify you of that.
    <br/>
    <br/>
    To send your {{ number_format($pledge->amount, 4) }} LTC to <strong>{{ $pledge->project->name }}</strong> simply
    <a href="{{ url('/projects/'.$pledge->project_id.'/send_pledge') }}">Click Here</a>
    <hr/>
    LitecoinPledge
@stop
