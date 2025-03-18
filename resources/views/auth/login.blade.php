@extends('layouts.auth')

@section('authContent')

@if ($message = Session::get('success'))

<div class="alert alert-success">

    <p>{{ $message }}</p>

</div>

@endif

<div class="container card shadow p-4 mt-5" style="max-width: 400px; width: 100%;">
    <h4 class="text-center mb-4">Login</h4>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="col-md-4 control-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Enter email">
            @if ($errors->has('email'))
            <span class="help-block">
                <strong class="text-danger">{{ $errors->first('email') }}</strong>
            </span>
            @endif
        </div>
        <div class="mb-3">
            <label for="password" class="col-md-4 control-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Enter password">
            @if ($errors->has('password'))
            <span class="help-block">
                <strong class="text-danger">{{ $errors->first('password') }}</strong>
            </span>
            @endif
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
    <div class="text-center mt-3">
        <a href="{{ route('register') }}">Create Account</a>
    </div>
</div>
@endsection