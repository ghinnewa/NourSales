@extends('layouts.app')
@section('content')
<div class="card">
    <h2>Create Account</h2>
    <form method="POST" action="{{ route('register.store') }}" class="stack">
        @csrf
        <input type="text" name="name" value="{{ old('name') }}" placeholder="Name" required>
        @error('name') <p class="error">{{ $message }}</p> @enderror
        <input type="email" name="email" value="{{ old('email') }}" placeholder="Email" required>
        @error('email') <p class="error">{{ $message }}</p> @enderror
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
        @error('password') <p class="error">{{ $message }}</p> @enderror
        <button class="btn" type="submit">Register</button>
    </form>
    <p class="muted">Already have an account? <a href="{{ route('login') }}">Login</a></p>
</div>
@endsection
