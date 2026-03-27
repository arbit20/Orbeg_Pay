<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Metal\StoreMetalRequest;
use App\Http\Requests\Metal\UpdateMetalRequest;
use App\Models\Metal;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MetalController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Metal::class);

        $metals = Metal::query()
            ->withCount(['purchaseItems', 'saleItems'])
            ->orderBy('name')
            ->get()
            ->map(fn (Metal $metal) => [
                'id' => $metal->id,
                'name' => $metal->name,
                'symbol' => $metal->symbol,
                'description' => $metal->description,
                'is_active' => $metal->is_active,
                'purchase_items_count' => $metal->purchase_items_count,
                'sale_items_count' => $metal->sale_items_count,
                'latest_price' => $metal->latestPrice(),
            ]);

        return Inertia::render('Metals/Index', [
            'metals' => $metals,
            'can' => [
                'create' => auth()->user()->can('metals.create'),
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Metal::class);

        return Inertia::render('Metals/Create');
    }

    public function store(StoreMetalRequest $request): RedirectResponse
    {
        Metal::create($request->validated());

        return redirect()
            ->route('metals.index')
            ->with('success', 'Metal registrado exitosamente.');
    }

    public function show(Metal $metal): Response
    {
        $this->authorize('view', $metal);

        $metal->load(['prices' => fn ($query) => $query->orderByDesc('effective_date')->limit(30)]);

        return Inertia::render('Metals/Show', [
            'metal' => [
                'id' => $metal->id,
                'name' => $metal->name,
                'symbol' => $metal->symbol,
                'description' => $metal->description,
                'is_active' => $metal->is_active,
                'prices' => $metal->prices->map(fn ($price) => [
                    'id' => $price->id,
                    'price_per_gram' => $price->price_per_gram,
                    'price_per_troy_ounce' => $price->price_per_troy_ounce,
                    'currency' => $price->currency,
                    'effective_date' => $price->effective_date->format('Y-m-d'),
                    'source' => $price->source,
                    'creator' => $price->creator?->name,
                ]),
            ],
            'can' => [
                'edit' => auth()->user()->can('metals.edit'),
                'delete' => auth()->user()->can('metals.delete'),
                'create_price' => auth()->user()->can('metal_prices.create'),
            ],
        ]);
    }

    public function edit(Metal $metal): Response
    {
        $this->authorize('update', $metal);

        return Inertia::render('Metals/Edit', [
            'metal' => [
                'id' => $metal->id,
                'name' => $metal->name,
                'symbol' => $metal->symbol,
                'description' => $metal->description,
                'is_active' => $metal->is_active,
            ],
        ]);
    }

    public function update(UpdateMetalRequest $request, Metal $metal): RedirectResponse
    {
        $metal->update($request->validated());

        return redirect()
            ->route('metals.show', $metal)
            ->with('success', 'Metal actualizado exitosamente.');
    }

    public function destroy(Metal $metal): RedirectResponse
    {
        $this->authorize('delete', $metal);

        $hasTransactions = $metal->purchaseItems()->exists() || $metal->saleItems()->exists();
        if ($hasTransactions) {
            return back()->with('error', 'No se puede eliminar un metal con transacciones asociadas.');
        }

        $metal->delete();

        return redirect()
            ->route('metals.index')
            ->with('success', 'Metal eliminado exitosamente.');
    }
}
