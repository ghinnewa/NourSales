@extends('layouts.app')
@section('content')
<div class="stack">
    <div class="card">
        <h2>{{ $pharmacy->pharmacy_name }}</h2>
        <p><strong>Owner:</strong> {{ $pharmacy->owner_name ?: '—' }}</p>
        <p><strong>Phone:</strong> {{ $pharmacy->phone ?: '—' }}</p>
        <p><strong>Area:</strong> {{ $pharmacy->area ?: '—' }}</p>
        <p><strong>Address:</strong> {{ $pharmacy->address ?: '—' }}</p>
        <p><strong>Map:</strong>
            @if($pharmacy->google_maps_link)
                <a href="{{ $pharmacy->google_maps_link }}" target="_blank">Open Link</a>
            @else — @endif
        </p>
        <p><strong>Notes:</strong> {{ $pharmacy->notes ?: '—' }}</p>
    </div>
    <a class="btn" href="{{ route('pharmacies.edit', $pharmacy) }}">Edit Pharmacy</a>
    <form method="POST" action="{{ route('pharmacies.destroy', $pharmacy) }}">
        @csrf
        @method('DELETE')
        <button class="btn btn-danger" type="submit">Delete Pharmacy</button>
    </form>
    <div class="card"><strong>Current Balance:</strong> Coming soon</div>
    <div class="card"><strong>Orders History:</strong> Coming soon</div>
    <div class="card"><strong>Payments History:</strong> Coming soon</div>
</div>
@endsection
