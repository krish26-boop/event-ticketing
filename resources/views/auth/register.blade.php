@extends('layouts.auth')

@section('authContent')

@if ($message = Session::get('success'))

<div class="alert alert-success">

    <p>{{ $message }}</p>

</div>

@endif

<div class="container card shadow p-4 mt-5" style="max-width: 400px; width: 100%;">
    <h4 class="text-center mb-4">Register</h4>
    <form action="{{ route('register') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name">
            @if ($errors->has('name'))
            <span class="help-block">
                <strong class="text-danger">{{ $errors->first('name') }}</strong>
            </span>
            @endif
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input type="email" class="form-control" name="email" id="email" placeholder="Enter Email">
            @if ($errors->has('email'))
            <span class="help-block">
                <strong class="text-danger">{{ $errors->first('email') }}</strong>
            </span>
            @endif
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" id="password" placeholder="Enter password">
            @if ($errors->has('password'))
            <span class="help-block">
                <strong class="text-danger">{{ $errors->first('password') }}</strong>
            </span>
            @endif
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Enter Confirm Password">
            @if ($errors->has('password_confirmation'))
            <span class="help-block">
                <strong class="text-danger">{{ $errors->first('password_confirmation') }}</strong>
            </span>
            @endif
        </div>
        <div class="mb-4">
            <label for="role" class="form-label">Role</label>
            <select class="form-control" name="role" id="role">
                <option value="">Select Role</option>
                @foreach($roles as $role):
                <option value="{{ $role }}">{{ $role }}</option>
                @endforeach
            </select>
            @if ($errors->has('role'))
            <span class="help-block">
                <strong class="text-danger">{{ $errors->first('role') }}</strong>
            </span>
            @endif
        </div>
        <button type="submit" class="btn btn-primary w-100">Register</button>
    </form>
    <div class="text-center mt-3">
        <a href="{{ route('login') }}">Login</a>
    </div>
</div>
@endsection