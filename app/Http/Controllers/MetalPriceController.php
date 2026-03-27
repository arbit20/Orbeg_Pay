<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\MetalPrice\StoreMetalPriceRequest;
use App\Models\Metal;
use App\Models\MetalPrice;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MetalPriceController extends Controller
{
    public function create(Metal $metal): Response
    {
        $this->authorize('create', MetalPrice::class);

        return Inertia::render('Metals/Prices/Create', [
            'metal' => [
                'id' => $metal->id,
                'name' => $metal->name,
                'symbol' => $metal->symbol,
            ],
        ]);
    }

    public function store(StoreMetalPriceRequest $request, Metal $metal): RedirectResponse
    {
        $validated = $request->validated();

        $metal->prices()->create([
            ...$validated,
            'price_per_troy_ounce' => $request->input('price_per_troy_ounce'),
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('metals.show', $metal)
            ->with('success', 'Precio registrado exitosamente.');
    }
}
