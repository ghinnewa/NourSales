@extends('layouts.app')
@section('content')
<form class="card" method="POST" action="{{ route('pharmacies.update', $pharmacy) }}">
    @method('PUT')
    @include('pharmacies._form', ['pharmacy' => $pharmacy])
</form>
@endsection
