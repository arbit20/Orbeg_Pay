<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Sale\StoreSaleRequest;
use App\Models\Client;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SaleController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Sale::class);

        $sales = Sale::query()
            ->with('client:id,business_name')
            ->orderByDesc('sale_datetime')
            ->orderByDesc('id')
            ->get()
            ->map(fn (Sale $s) => [
                'id' => $s->id,
                'sale_datetime' => $s->sale_datetime?->format('d/m/Y - H:i'),
                'weight_grams' => $s->weight_grams,
                'lot_count' => $s->lot_count,
                'purity' => $s->purity,
                'unit_price_bs' => $s->unit_price_bs,
                'total_bs' => $s->total_bs,
                'reference_quote_usd_oz' => $s->reference_quote_usd_oz,
                'quote_source' => $s->quote_source,
                'exchange_rate_bs_usd' => $s->exchange_rate_bs_usd,
                'evidence_url' => $s->evidence_path
                    ? Storage::url($s->evidence_path)
                    : null,
                'client_name' => $s->client?->business_name,
            ]);

        return Inertia::render('Sales/Index', [
            'sales' => $sales,
            'can' => [
                'create' => auth()->user()->can('sales.create'),
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Sale::class);

        $clients = Client::query()
            ->where('is_active', true)
            ->orderBy('business_name')
            ->get(['id', 'business_name']);

        return Inertia::render('Sales/Create', [
            'clients' => $clients,
        ]);
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $evidencePath = null;
        if ($request->hasFile('evidence')) {
            $evidencePath = $request->file('evidence')->store('sales/evidence', 'public');
        }

        $saleNumber = 'V-' . now()->format('Ymd-His') . '-' . str_pad((string) (Sale::count() + 1), 4, '0', STR_PAD_LEFT);

        Sale::create([
            'sale_number' => $saleNumber,
            'client_id' => $validated['client_id'] ?? null,
            'user_id' => auth()->id(),
            'sale_date' => now()->toDateString(),
            'sale_datetime' => $validated['sale_datetime'],
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
            ->route('sales.index')
            ->with('success', 'Venta registrada exitosamente.');
    }

    public function destroy(Sale $sale): RedirectResponse
    {
        $this->authorize('delete', $sale);

        if ($sale->evidence_path) {
            Storage::disk('public')->delete($sale->evidence_path);
        }

        $sale->delete();

        return redirect()
            ->route('sales.index')
            ->with('success', 'Venta eliminada exitosamente.');
    }
}
