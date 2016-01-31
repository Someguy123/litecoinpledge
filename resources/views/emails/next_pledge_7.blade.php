@extends('emails.template')
@section('content')
    <h3>Hello {{ $user->name }},</h3><br/>
    <br/>
    It's been 7 days since your {{ number_format($pledge->amount, 4) }} LTC
    pledge to {{ $pledge->project->name }} was due, and we sent out 2 emails
    to warn you of this.
    <br/>
    <br/>
    Because you haven't paid your pledge in so long, we have removed the pledge
    from your account. You won't receive any more emails about this project.
    <br/>
    <br/>
    If you change your mind, you can always go back to the project and re-create your pledge.
    <br/>
    <hr/>
    LitecoinPledge
@stop
