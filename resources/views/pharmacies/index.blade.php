@extends('layouts.app')
@section('content')
<div class="stack">
    <form method="GET" action="{{ route('pharmacies.index') }}">
        <input class="input" name="search" placeholder="Search pharmacies..." value="{{ $search }}">
    </form>
    <a class="btn" href="{{ route('pharmacies.create') }}">Add Pharmacy</a>

    @foreach($pharmacies as $pharmacy)
    <a class="card link-card" href="{{ route('pharmacies.show', $pharmacy) }}">
        <h3>{{ $pharmacy->pharmacy_name }}</h3>
        @if($pharmacy->owner_name)<p>Owner: {{ $pharmacy->owner_name }}</p>@endif
        @if($pharmacy->phone)<p>Phone: {{ $pharmacy->phone }}</p>@endif
        @if($pharmacy->area)<p>Area: {{ $pharmacy->area }}</p>@endif
    </a>
    @endforeach

    {{ $pharmacies->links() }}
</div>
@endsection
