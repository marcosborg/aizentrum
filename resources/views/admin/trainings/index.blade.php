@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('cruds.training.title') }}
    </div>

    <div class="card-body">
        <div class="row">
            @foreach ($assistants as $assistant)
            <div class="col-md-3">
                <div class="card">
                    <img src="https://robohash.org/{{ $assistant->name }}">
                    <div class="card-body">
                        <h3>{{ $assistant->name }}</h3>
                        <h5>{{ $assistant->project->name }}</h5>
                    </div>
                    <div class="card-footer">
                        <a href="/admin/trainings/assistant/{{ $assistant->id }}" class="btn btn-success">Train {{ $assistant->name }}</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection