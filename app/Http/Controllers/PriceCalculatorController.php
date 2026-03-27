<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Metal;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PriceCalculatorController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $metals = Metal::where('is_active', true)
            ->get()
            ->map(function ($metal) {
                $latestPrice = $metal->latestPrice();

                return [
                    'id' => $metal->id,
                    'name' => $metal->name,
                    'symbol' => $metal->symbol,
                    'price_per_gram' => $latestPrice?->price_per_gram,
                    'price_per_troy_ounce' => $latestPrice?->price_per_troy_ounce,
                    'currency' => $latestPrice?->currency ?? 'USD',
                    'effective_date' => $latestPrice?->effective_date?->format('d/m/Y'),
                    'source' => $latestPrice?->source,
                ];
            });

        return Inertia::render('PriceCalculator', [
            'metals' => $metals,
        ]);
    }
}
