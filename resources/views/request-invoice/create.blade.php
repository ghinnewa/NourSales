<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Request</title>
    <style>
        body{font-family:Arial,sans-serif;background:#f5f7fb;margin:0;padding:16px} .container{max-width:1000px;margin:0 auto;background:#fff;padding:20px;border-radius:10px}
        .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px}.input,textarea,select{width:100%;padding:10px;border:1px solid #d0d6e0;border-radius:8px}
        table{width:100%;border-collapse:collapse}th,td{padding:8px;border-bottom:1px solid #eee;text-align:left}.sticky{position:sticky;bottom:0;background:#fff;padding:10px 0}
        .btn{background:#0d6efd;color:#fff;border:none;padding:12px 18px;border-radius:8px;cursor:pointer} .error{color:#b20000;font-size:13px}
    </style>
</head>
<body>
<div class="container">
    <h2>Public Invoice Request</h2>

    @if ($errors->any()) <div class="error">{{ $errors->first() }}</div> @endif

    <form method="POST" action="{{ route('request-invoice.store') }}">
        @csrf
        <input type="text" name="website" value="" style="display:none">
        <div class="grid">
            <input class="input" type="text" name="requester_name" value="{{ old('requester_name') }}" placeholder="Requester Name" required>
            <input class="input" type="text" name="pharmacy_name" value="{{ old('pharmacy_name') }}" placeholder="Pharmacy Name" required>
            <input class="input" type="text" name="phone" value="{{ old('phone') }}" placeholder="Phone (optional)">
            <select class="input" id="brandFilter" name="company_filter">
                <option value="">All Brands</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand }}">{{ $brand }}</option>
                @endforeach
            </select>
        </div>

        <div style="margin-top:10px">
            <input class="input" type="search" id="productSearch" placeholder="Search products">
            <textarea name="notes" placeholder="Notes (optional)" style="margin-top:10px">{{ old('notes') }}</textarea>
        </div>

        @foreach($products as $brand => $brandProducts)
            <h4 class="brand-block" data-brand="{{ $brand }}">{{ $brand }}</h4>
            <table class="brand-block" data-brand="{{ $brand }}">
                <thead><tr><th>Product</th><th>Price</th><th>Qty</th></tr></thead>
                <tbody>
                @foreach($brandProducts as $product)
                    <tr data-name="{{ strtolower($product->name) }}" data-brand="{{ strtolower($brand) }}">
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->price !== null ? number_format($product->price,2) : '-' }}</td>
                        <td><input class="input qty-input" style="max-width:90px" type="number" min="0" name="quantities[{{ $product->id }}]" value="{{ old('quantities.'.$product->id,0) }}"></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endforeach

        <div style="margin-top:14px"><strong>Selected Items:</strong> <span id="selectedSummary">None selected</span></div>
        <div class="sticky"><button class="btn" type="submit">Submit Request</button></div>
    </form>
</div>
<script>
const search=document.getElementById('productSearch'); const filter=document.getElementById('brandFilter');
function applyFilter(){const q=search.value.toLowerCase();const b=filter.value.toLowerCase();document.querySelectorAll('tr[data-name]').forEach(row=>{const m=row.dataset.name.includes(q)&&(!b||row.dataset.brand===b);row.style.display=m?'':'none';});document.querySelectorAll('.brand-block').forEach(el=>{const brand=el.dataset.brand?.toLowerCase();if(!brand)return;el.style.display=(!b||brand===b)?'':'none';});}
search.addEventListener('input',applyFilter); filter.addEventListener('change',applyFilter);
function updateSummary(){let c=0;document.querySelectorAll('.qty-input').forEach(i=>{if(parseInt(i.value||0)>0)c++;});document.getElementById('selectedSummary').textContent=c?`${c} product(s) selected`:'None selected';}
document.querySelectorAll('.qty-input').forEach(i=>i.addEventListener('input',updateSummary)); updateSummary();
</script>
</body>
</html>
