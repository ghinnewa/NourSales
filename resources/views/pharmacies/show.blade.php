@extends('layouts.app')
@section('content')
<div class="card">
    <h2>{{ $pharmacy->pharmacy_name }}</h2>
    <p><strong>Owner:</strong> {{ $pharmacy->owner_name ?: 'N/A' }}</p>
    <p><strong>Phone:</strong> {{ $pharmacy->phone ?: 'N/A' }}</p>
    <p><strong>Area:</strong> {{ $pharmacy->area ?: 'N/A' }}</p>
    <p><strong>Address:</strong> {{ $pharmacy->address ?: 'N/A' }}</p>
    <p><strong>Maps:</strong> @if($pharmacy->google_maps_link)<a href="{{ $pharmacy->google_maps_link }}" target="_blank">Open Location</a>@else N/A @endif</p>
    <p><strong>Notes:</strong> {{ $pharmacy->notes ?: 'N/A' }}</p>
</div>

<div class="card"><strong>Current Balance:</strong> Coming soon</div>
<div class="card"><strong>Orders History:</strong> Coming soon</div>
<div class="card"><strong>Payments History:</strong> Coming soon</div>

<div class="stack mt-16">
    <a href="{{ route('pharmacies.edit', $pharmacy) }}" class="btn btn-block">Edit</a>
    <form method="POST" action="{{ route('pharmacies.destroy', $pharmacy) }}">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Delete pharmacy?')">Delete</button>
    </form>
</div>
@endsection
