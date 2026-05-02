@csrf
<div class="stack">
    @foreach(['pharmacy_name'=>'Pharmacy Name*','owner_name'=>'Owner Name','phone'=>'Phone','area'=>'Area','google_maps_link'=>'Google Maps Link'] as $name => $label)
        <label>{{ $label }}
            <input class="input" name="{{ $name }}" value="{{ old($name, $pharmacy->$name ?? '') }}">
        </label>
        @error($name)<small class="error">{{ $message }}</small>@enderror
    @endforeach

    <label>Address
        <textarea class="input" name="address">{{ old('address', $pharmacy->address ?? '') }}</textarea>
    </label>
    @error('address')<small class="error">{{ $message }}</small>@enderror

    <label>Notes
        <textarea class="input" name="notes">{{ old('notes', $pharmacy->notes ?? '') }}</textarea>
    </label>
    @error('notes')<small class="error">{{ $message }}</small>@enderror

    <button class="btn" type="submit">Save</button>
</div>
