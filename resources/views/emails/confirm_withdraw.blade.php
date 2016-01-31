
@extends('emails.template')
@section('content')
Hello,<br/>
<br/>
Someone (hopefully you), has requested a withdrawal from your account.<br/>
<table border="1" class="table">
    <tr>
        <th>Amount</th>
        <td>{{ $transaction->amount }} LTC</td>
    </tr>
    <tr>
        <th>To Address</th>
        <td>{{ $transaction->address }}</td>
    </tr>
    <tr>
        <th>Time of request</th>
        <td>{{ $transaction->created_at }}</td>
    </tr>
</table>
<br/>
If you were expecting this email,
<a href="{{ url('/wallet/release/' . $transaction->confirmation_key) }}">Click here to CONFIRM the withdrawal.</a><br/>
<br/>
If you've changed your mind, or didn't request this withdrawal,
<a href="{{ url('/wallet/cancel/' . $transaction->confirmation_key) }}">Click here to CANCEL the withdrawal.</a>
<br/>
<br/>
If you take no action, the withdrawal will be cancelled and
returned to your balance after ~48 hours.
<br/>
<strong>If you did not request this</strong>, your account may have
been compromised and you should change all passwords immediately.
<br/>
<br/>
<hr/>
LitecoinPledge
@stop
