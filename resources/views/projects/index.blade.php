@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h1 class="text-center">All Projects</h1>
                @include('partials.status')

                <div class="row">
                @foreach($projects as $p)
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
                {!! $projects->links() !!}
            </div>
        </div>
    </div>

@stop