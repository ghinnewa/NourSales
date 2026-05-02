@extends('layouts.app')
@section('content')
<div class="card">
    <h2>Edit Pharmacy</h2>
    <form method="POST" action="{{ route('pharmacies.update', $pharmacy) }}">
        @csrf
        @method('PUT')
        @include('pharmacies._form', ['buttonText' => 'Update Pharmacy'])
    </form>
</div>
@endsection
