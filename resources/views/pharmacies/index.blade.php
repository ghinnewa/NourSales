@extends('layouts.app')
@section('content')
<form method="GET" class="stack mb-16">
    <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, owner, phone, or area">
    <button class="btn" type="submit">Search</button>
</form>
<a href="{{ route('pharmacies.create') }}" class="btn btn-block mb-16">Add Pharmacy</a>

<div class="stack">
@forelse ($pharmacies as $pharmacy)
    <a href="{{ route('pharmacies.show', $pharmacy) }}" class="card card-link">
        <h3>{{ $pharmacy->pharmacy_name }}</h3>
        @if($pharmacy->owner_name)<p>Owner: {{ $pharmacy->owner_name }}</p>@endif
        @if($pharmacy->phone)<p>Phone: {{ $pharmacy->phone }}</p>@endif
        @if($pharmacy->area)<p>Area: {{ $pharmacy->area }}</p>@endif
    </a>
@empty
    <p class="muted">No pharmacies found.</p>
@endforelse
</div>
<div class="mt-16">{{ $pharmacies->links() }}</div>
@endsection
