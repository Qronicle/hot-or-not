@extends('layouts.default')

@section('content')
    <h2>Add new user</h2>

    {{ Form::open(['url' => '/users', 'class' => 'vertical-form', 'novalidate' => 'novalidate', 'files' => true]) }}

    @include('common.errors')
    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

    <div class="form-group">
        {{ Form::label('Name') }}
        {{ Form::text('name', null, ['maxlength' => 24]) }}
    </div>

    <div class="form-group">
        {{ Form::label('Picture') }}
        {{ Form::file('image', null) }}
    </div>

    <div class="form-group">
        {{ Form::submit('Add user!') }}
    </div>
    {{ Form::close() }}
@endsection

@section('content-class')content-small
@endsection