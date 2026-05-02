@extends('layouts.app')
@section('content')
<div class="card">
    <h2>Add Pharmacy</h2>
    <form method="POST" action="{{ route('pharmacies.store') }}">
        @csrf
        @include('pharmacies._form', ['buttonText' => 'Save Pharmacy'])
    </form>
</div>
@endsection
