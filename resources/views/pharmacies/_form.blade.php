<div class="stack">
    <input type="text" name="pharmacy_name" value="{{ old('pharmacy_name', $pharmacy->pharmacy_name ?? '') }}" placeholder="Pharmacy Name" required>
    @error('pharmacy_name') <p class="error">{{ $message }}</p> @enderror
    <input type="text" name="owner_name" value="{{ old('owner_name', $pharmacy->owner_name ?? '') }}" placeholder="Owner Name">
    @error('owner_name') <p class="error">{{ $message }}</p> @enderror
    <input type="text" name="phone" value="{{ old('phone', $pharmacy->phone ?? '') }}" placeholder="Phone">
    <input type="text" name="area" value="{{ old('area', $pharmacy->area ?? '') }}" placeholder="Area">
    <textarea name="address" placeholder="Address">{{ old('address', $pharmacy->address ?? '') }}</textarea>
    <input type="url" name="google_maps_link" value="{{ old('google_maps_link', $pharmacy->google_maps_link ?? '') }}" placeholder="Google Maps Link">
    <textarea name="notes" placeholder="Notes">{{ old('notes', $pharmacy->notes ?? '') }}</textarea>
    <button class="btn btn-block" type="submit">{{ $buttonText }}</button>
</div>
