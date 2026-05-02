<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePharmacyRequest;
use App\Http\Requests\UpdatePharmacyRequest;
use App\Models\Pharmacy;
use Illuminate\Http\Request;

class PharmacyController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $pharmacies = Pharmacy::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('pharmacy_name', 'like', "%{$search}%")
                        ->orWhere('owner_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('area', 'like', "%{$search}%");
                });
            })
            ->orderBy('pharmacy_name')
            ->paginate(10)
            ->withQueryString();

        return view('pharmacies.index', compact('pharmacies', 'search'));
    }

    public function create()
    {
        return view('pharmacies.create');
    }

    public function store(StorePharmacyRequest $request)
    {
        $pharmacy = Pharmacy::create($request->validated());

        return redirect()->route('pharmacies.show', $pharmacy)->with('status', 'Pharmacy created successfully.');
    }

    public function show(Pharmacy $pharmacy)
    {
        return view('pharmacies.show', compact('pharmacy'));
    }

    public function edit(Pharmacy $pharmacy)
    {
        return view('pharmacies.edit', compact('pharmacy'));
    }

    public function update(UpdatePharmacyRequest $request, Pharmacy $pharmacy)
    {
        $pharmacy->update($request->validated());

        return redirect()->route('pharmacies.show', $pharmacy)->with('status', 'Pharmacy updated successfully.');
    }

    public function destroy(Pharmacy $pharmacy)
    {
        $pharmacy->delete();

        return redirect()->route('pharmacies.index')->with('status', 'Pharmacy deleted successfully.');
    }
}
