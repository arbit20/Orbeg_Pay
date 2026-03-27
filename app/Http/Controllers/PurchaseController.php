<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Purchase\StorePurchaseRequest;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class PurchaseController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Purchase::class);

        $purchases = Purchase::query()
            ->with('supplier:id,business_name')
            ->orderByDesc('purchase_datetime')
            ->orderByDesc('id')
            ->get()
            ->map(fn (Purchase $p) => [
                'id' => $p->id,
                'purchase_datetime' => $p->purchase_datetime?->format('d/m/Y - H:i'),
                'weight_grams' => $p->weight_grams,
                'lot_count' => $p->lot_count,
                'purity' => $p->purity,
                'unit_price_bs' => $p->unit_price_bs,
                'total_bs' => $p->total_bs,
                'reference_quote_usd_oz' => $p->reference_quote_usd_oz,
                'quote_source' => $p->quote_source,
                'exchange_rate_bs_usd' => $p->exchange_rate_bs_usd,
                'evidence_url' => $p->evidence_path
                    ? Storage::url($p->evidence_path)
                    : null,
                'supplier_name' => $p->supplier?->business_name,
            ]);

        return Inertia::render('Purchases/Index', [
            'purchases' => $purchases,
            'can' => [
                'create' => auth()->user()->can('purchases.create'),
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Purchase::class);

        $suppliers = Supplier::query()
            ->where('is_active', true)
            ->orderBy('business_name')
            ->get(['id', 'business_name']);

        return Inertia::render('Purchases/Create', [
            'suppliers' => $suppliers,
        ]);
    }

    public function store(StorePurchaseRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $evidencePath = null;
        if ($request->hasFile('evidence')) {
            $evidencePath = $request->file('evidence')->store('purchases/evidence', 'public');
        }

        $purchaseNumber = 'C-' . now()->format('Ymd-His') . '-' . str_pad((string) (Purchase::count() + 1), 4, '0', STR_PAD_LEFT);

        Purchase::create([
            'purchase_number' => $purchaseNumber,
            'supplier_id' => $validated['supplier_id'] ?? null,
            'user_id' => auth()->id(),
            'purchase_date' => now()->toDateString(),
            'purchase_datetime' => $validated['purchase_datetime'],
            'lot_count' => $validated['lot_count'],
            'weight_grams' => $validated['weight_grams'],
            'purity' => $validated['purity'],
            'unit_price_bs' => $validated['unit_price_bs'],
            'total_bs' => $validated['total_bs'],
            'reference_quote_usd_oz' => $validated['reference_quote_usd_oz'] ?? null,
            'quote_source' => $validated['quote_source'] ?? null,
            'exchange_rate_bs_usd' => $validated['exchange_rate_bs_usd'] ?? null,
            'evidence_path' => $evidencePath,
            'total' => $validated['total_bs'],
            'currency' => 'BOB',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('purchases.index')
            ->with('success', 'Compra registrada exitosamente.');
    }

    public function destroy(Purchase $purchase): RedirectResponse
    {
        $this->authorize('delete', $purchase);

        if ($purchase->evidence_path) {
            Storage::disk('public')->delete($purchase->evidence_path);
        }

        $purchase->delete();

        return redirect()
            ->route('purchases.index')
            ->with('success', 'Compra eliminada exitosamente.');
    }
}
