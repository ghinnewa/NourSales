@extends('layouts.app')
@section('content')
<div class="card">
    <h2>Login</h2>
    <form method="POST" action="{{ route('login.store') }}" class="stack">
        @csrf
        <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required>
        @error('email') <p class="error">{{ $message }}</p> @enderror
        <input type="password" name="password" placeholder="Password" required>
        @error('password') <p class="error">{{ $message }}</p> @enderror
        <button class="btn" type="submit">Login</button>
    </form>
    <p class="muted">No account? <a href="{{ route('register') }}">Register</a></p>
</div>
@endsection
