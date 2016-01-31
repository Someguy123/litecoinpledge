@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h1>My Account</h1>
                <div class="col-sm-12 col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">Wallet</div>

                        <div class="panel-body">
                            <p>Balance: <strong>{{ $user->balance }}</strong> LTC</p>

                            <p>Deposit Address: </p>
                            @if($user->ltc_address == "")
                                <a href="{{ url('/account/generate') }}">Generate Address</a>
                            @else
                                <pre>{{ $user->ltc_address }}</pre>
                            @endif
                            <h4>Withdraw</h4>

                            <form action="/account/withdraw" method="POST">
                                <div class="form-group">
                                    <div class="col-sm-6 col-xs-12">
                                        <input type="text" class="form-control" name="address" placeholder="address"/>
                                    </div>
                                    <div class="col-sm-6 col-xs-12">
                                        <input type="text" class="form-control" name="amount" placeholder="0.00"/>
                                    </div>
                                    <div class="col-xs-12">
                                        <hr>
                                        <input type="submit" class="form-control btn btn-primary" value="Withdraw"/>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">My Pledges</div>

                        <div class="panel-body">
                            <p>Below are your <strong>recurring</strong> pledges</p>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Project</th>
                                        <th>Amount</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user_pledges as $pl)
                                        <tr>
                                            <td>{{ $pl->project->name }}</td>
                                            <td>{{ $pl->amount }}</td>
                                            <td>{{ $pl->project_id }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
