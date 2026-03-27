<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Metal;
use App\Services\CurrencyConverter;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PriceCalculatorController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $fx = CurrencyConverter::fromConfig();

        $metals = Metal::where('is_active', true)
            ->get()
            ->map(function ($metal) use ($fx) {
                $latestPrice = $metal->latestPrice();

                $currency = $latestPrice?->currency ?? 'USD';
                $pricePerGram = $latestPrice?->price_per_gram;
                $pricePerTroyOunce = $latestPrice?->price_per_troy_ounce;

                $pricePerGramUsd = null;
                $pricePerGramPen = null;
                $pricePerTroyOunceUsd = null;
                $pricePerTroyOuncePen = null;

                if ($latestPrice && $pricePerGram !== null && $pricePerTroyOunce !== null) {
                    if (strtoupper($currency) === 'USD') {
                        $pricePerGramUsd = (string) $pricePerGram;
                        $pricePerTroyOunceUsd = (string) $pricePerTroyOunce;
                        $pricePerGramPen = $fx->convert((string) $pricePerGram, 'USD', 'PEN');
                        $pricePerTroyOuncePen = $fx->convert((string) $pricePerTroyOunce, 'USD', 'PEN');
                    } elseif (strtoupper($currency) === 'PEN') {
                        $pricePerGramPen = (string) $pricePerGram;
                        $pricePerTroyOuncePen = (string) $pricePerTroyOunce;
                        $pricePerGramUsd = $fx->convert((string) $pricePerGram, 'PEN', 'USD');
                        $pricePerTroyOunceUsd = $fx->convert((string) $pricePerTroyOunce, 'PEN', 'USD');
                    }
                }

                return [
                    'id' => $metal->id,
                    'name' => $metal->name,
                    'symbol' => $metal->symbol,
                    'price_per_gram' => $pricePerGram,
                    'price_per_troy_ounce' => $pricePerTroyOunce,
                    'currency' => $currency,
                    'price_per_gram_usd' => $pricePerGramUsd,
                    'price_per_gram_pen' => $pricePerGramPen,
                    'price_per_troy_ounce_usd' => $pricePerTroyOunceUsd,
                    'price_per_troy_ounce_pen' => $pricePerTroyOuncePen,
                    'effective_date' => $latestPrice?->effective_date?->format('d/m/Y'),
                    'source' => $latestPrice?->source,
                ];
            });

        return Inertia::render('PriceCalculator', [
            'metals' => $metals,
            'fx' => [
                'usd_pen' => (string) config('currency.usd_pen', '3.75'),
            ],
        ]);
    }
}
