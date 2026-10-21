@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div style="max-width: 400px; margin: 0 auto;">
        <h1>Login</h1>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <label>
                <input type="checkbox" name="remember"> Remember me
            </label>

            <br><br>

            <button type="submit" class="btn">Login</button>
        </form>
    </div>
@endsection
