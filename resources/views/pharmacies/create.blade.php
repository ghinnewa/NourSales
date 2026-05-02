@extends('layouts.app')
@section('content')
<form class="card" method="POST" action="{{ route('pharmacies.store') }}">
    @include('pharmacies._form')
</form>
@endsection
