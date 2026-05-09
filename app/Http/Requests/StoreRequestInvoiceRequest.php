<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreRequestInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'requester_name' => ['required', 'string', 'max:255'],
            'pharmacy_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'company_filter' => ['nullable', 'string', 'max:255'],
            'website' => ['nullable', 'size:0'],
            'quantities' => ['required', 'array'],
            'quantities.*' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $quantities = collect($this->input('quantities', []))
                ->mapWithKeys(fn ($quantity, $productId) => [(int) $productId => (int) $quantity])
                ->filter(fn (int $quantity) => $quantity > 0);

            if ($quantities->isEmpty()) {
                $validator->errors()->add('quantities', 'Please select at least one product quantity greater than zero.');
                return;
            }

            $validIds = Product::query()->whereIn('id', $quantities->keys())->pluck('id')->all();

            if (count($validIds) !== $quantities->count()) {
                $validator->errors()->add('quantities', 'One or more selected products are invalid.');
            }
        });
    }
}
