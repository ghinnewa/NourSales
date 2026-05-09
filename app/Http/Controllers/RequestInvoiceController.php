<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequestInvoiceRequest;
use App\Models\Product;
use App\Models\RequestInvoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RequestInvoiceController extends Controller
{
    public function create(): View
    {
        $products = Product::query()
            ->select(['id', 'name', 'brand', 'price'])
            ->orderByRaw('COALESCE(brand, "")')
            ->orderBy('name')
            ->get()
            ->groupBy(fn ($product) => $product->brand ?: 'Other');

        $brands = $products->keys()->sort()->values();

        return view('request-invoice.create', compact('products', 'brands'));
    }

    public function store(StoreRequestInvoiceRequest $request): RedirectResponse
    {
        $quantities = collect($request->input('quantities', []))
            ->mapWithKeys(fn ($quantity, $productId) => [(int) $productId => (int) $quantity])
            ->filter(fn (int $quantity) => $quantity > 0);

        $invoice = DB::transaction(function () use ($request, $quantities) {
            $products = Product::query()->whereIn('id', $quantities->keys())->get()->keyBy('id');

            $invoice = RequestInvoice::create([
                'requester_name' => $request->input('requester_name'),
                'pharmacy_name' => $request->input('pharmacy_name'),
                'phone' => $request->input('phone') ?: null,
                'notes' => $request->input('notes') ?: null,
                'status' => 'pending',
                'submitted_at' => now(),
            ]);

            $total = 0;
            $items = [];
            $lines = [];
            foreach ($quantities as $productId => $qty) {
                $product = $products->get($productId);
                if (! $product) {
                    continue;
                }

                $lineTotal = $product->price !== null ? bcmul((string) $product->price, (string) $qty, 2) : null;
                if ($lineTotal !== null) {
                    $total = bcadd((string) $total, (string) $lineTotal, 2);
                }

                $items[] = [
                    'product_id' => $product->id,
                    'product_name_snapshot' => $product->name,
                    'product_price_snapshot' => $product->price,
                    'quantity' => $qty,
                    'line_total' => $lineTotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $pricePart = $product->price !== null ? ' @ ' . number_format((float) $product->price, 2) : '';
                $lineTotalPart = $lineTotal !== null ? ' = ' . number_format((float) $lineTotal, 2) : '';
                $lines[] = $product->name . ' x ' . $qty . $pricePart . $lineTotalPart;
            }

            $invoice->items()->createMany($items);

            $message = $this->buildWhatsAppMessage($invoice, $lines, $total > 0 ? (string) $total : null);
            $invoice->update([
                'whatsapp_message' => $message,
                'total_amount' => $total > 0 ? $total : null,
            ]);

            return $invoice;
        });

        return redirect()->route('request-invoice.success', $invoice);
    }

    public function success(RequestInvoice $requestInvoice): View
    {
        $number = preg_replace('/\D+/', '', (string) config('services.company_whatsapp_number'));
        $whatsAppUrl = $number
            ? 'https://wa.me/' . $number . '?text=' . urlencode((string) $requestInvoice->whatsapp_message)
            : null;

        return view('request-invoice.success', [
            'requestInvoice' => $requestInvoice->load('items'),
            'whatsAppUrl' => $whatsAppUrl,
        ]);
    }

    private function buildWhatsAppMessage(RequestInvoice $invoice, array $lines, ?string $totalAmount): string
    {
        $message = "New Invoice Request\n\n";
        $message .= "Requester: {$invoice->requester_name}\n";
        $message .= "Pharmacy: {$invoice->pharmacy_name}\n";
        $message .= 'Phone: ' . ($invoice->phone ?: '-') . "\n\n";
        $message .= "Products:\n";

        foreach (array_values($lines) as $index => $line) {
            $message .= ($index + 1) . '. ' . $line . "\n";
        }

        $message .= "\nNotes:\n" . ($invoice->notes ?: '-') . "\n";
        if ($totalAmount !== null) {
            $message .= "\nTotal: " . number_format((float) $totalAmount, 2) . "\n";
        }
        $message .= "\nRequest ID: #{$invoice->id}";

        return $message;
    }
}
