@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <h1 class="text-center">LitecoinPledge</h1>
            <p>Welcome to LitecoinPledge. The best way to support community-funded projects
            with both recurring Litecoin payments, and one-time payments.</p>
            <p>Project ownership is verified by moderators to protect against people taking
            ownership of other peoples projects.</p>
            <h2 class="text-center">Top Projects</h2>
            @foreach($top_projects as $p)
                <div class="col-md-4">
                    <div class="well top-project">
                        <div class="text-center">
                            <img src="{{ $p->project_img }}" alt="Project Image">

                            <p><a href="{{ url('/projects/'.$p->id) }}">{{ $p->name }}</a></p>

                            <p>Total: <strong>{{ number_format($p->total_pledged, 2) }}</strong> LTC</p>

                        </div>


                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
