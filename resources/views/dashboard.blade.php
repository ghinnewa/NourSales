@extends('layouts.app')
@section('content')
<div class="card">
    <h2>Welcome, {{ auth()->user()->name }}</h2>
    <p class="muted">You are ready to manage your pharmacy visits.</p>
    <a href="{{ route('pharmacies.index') }}" class="btn btn-block">Go to Pharmacies</a>
</div>
@endsection
