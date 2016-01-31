@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h1 class="text-center">Create a Project</h1>
                @include('partials.status')

                <form action="/projects" method="POST">
                    <div class="form-group">
                        <label for="name">Project Name:</label>
                        <input type="text" class="form-control" name="name" value="{{ old('name') }}"/>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea class="form-control" name="description" rows="5">{{ old('description') }}</textarea>

                        <div class="well">
                            <small>Note: You can use
                                <a href="https://github.com/adam-p/markdown-here/wiki/Markdown-Cheatsheet">markdown</a>,
                                including links and images for your project description. Subject to moderation.<br/>
                                Examples: <em>*italics*</em>, <strong>**bold**</strong>, [my link](http://example.org)
                            </small>
                        </div>
                    </div>
                    <div class="form-group">
                        {{ csrf_field() }}
                        <input type="submit" value="Create Project" class="btn btn-primary"/>
                    </div>
                </form>

            </div>
        </div>
    </div>

@stop