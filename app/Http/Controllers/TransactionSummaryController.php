<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Metal;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class TransactionSummaryController extends Controller
{
    private const ACTIVE_STATUSES = ['confirmada', 'liquidada'];

    public function __invoke(Request $request): Response
    {
        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'group_by' => ['nullable', 'in:day,week,month'],
        ]);

        $dateTo = Carbon::parse($validated['date_to'] ?? now())->endOfDay();
        $dateFrom = Carbon::parse($validated['date_from'] ?? $dateTo->copy()->subMonth()->startOfDay());
        $groupBy = $validated['group_by'] ?? 'day';

        $filters = [
            'date_from' => $dateFrom->toDateString(),
            'date_to' => $dateTo->toDateString(),
            'group_by' => $groupBy,
        ];

        return Inertia::render('Reports/TransactionSummary', [
            'filters' => $filters,
            'totals' => fn () => $this->totals($dateFrom, $dateTo),
            'byMetal' => fn () => $this->byMetal($dateFrom, $dateTo),
            'timeSeries' => fn () => $this->timeSeries($dateFrom, $dateTo, $groupBy),
            'metals' => fn () => Metal::where('is_active', true)->orderBy('name')->get(['id', 'name', 'symbol']),
        ]);
    }

    private function totals(Carbon $from, Carbon $to): array
    {
        $purchases = Purchase::whereIn('status', self::ACTIVE_STATUSES)
            ->whereBetween('purchase_date', [$from, $to]);

        $sales = Sale::whereIn('status', self::ACTIVE_STATUSES)
            ->whereBetween('sale_date', [$from, $to]);

        $purchaseTotal = (float) $purchases->sum('total');
        $saleTotal = (float) $sales->sum('total');

        $purchaseWeight = (float) PurchaseItem::whereHas('purchase', function ($q) use ($from, $to) {
            $q->whereIn('status', self::ACTIVE_STATUSES)->whereBetween('purchase_date', [$from, $to]);
        })->sum('weight_grams');

        $saleWeight = (float) SaleItem::whereHas('sale', function ($q) use ($from, $to) {
            $q->whereIn('status', self::ACTIVE_STATUSES)->whereBetween('sale_date', [$from, $to]);
        })->sum('weight_grams');

        return [
            'purchase_count' => $purchases->count(),
            'sale_count' => $sales->count(),
            'purchase_total' => round($purchaseTotal, 2),
            'sale_total' => round($saleTotal, 2),
            'net' => round($saleTotal - $purchaseTotal, 2),
            'purchase_weight_grams' => round($purchaseWeight, 4),
            'sale_weight_grams' => round($saleWeight, 4),
        ];
    }

    private function byMetal(Carbon $from, Carbon $to): array
    {
        $purchasesByMetal = PurchaseItem::query()
            ->select(
                'metal_id',
                DB::raw('SUM(weight_grams) as total_weight'),
                DB::raw('SUM(subtotal) as total_amount'),
                DB::raw('COUNT(*) as item_count'),
            )
            ->whereHas('purchase', function ($q) use ($from, $to) {
                $q->whereIn('status', self::ACTIVE_STATUSES)->whereBetween('purchase_date', [$from, $to]);
            })
            ->groupBy('metal_id')
            ->get()
            ->keyBy('metal_id');

        $salesByMetal = SaleItem::query()
            ->select(
                'metal_id',
                DB::raw('SUM(weight_grams) as total_weight'),
                DB::raw('SUM(subtotal) as total_amount'),
                DB::raw('COUNT(*) as item_count'),
            )
            ->whereHas('sale', function ($q) use ($from, $to) {
                $q->whereIn('status', self::ACTIVE_STATUSES)->whereBetween('sale_date', [$from, $to]);
            })
            ->groupBy('metal_id')
            ->get()
            ->keyBy('metal_id');

        $metalIds = $purchasesByMetal->keys()->merge($salesByMetal->keys())->unique();
        $metals = Metal::whereIn('id', $metalIds)->get()->keyBy('id');

        return $metalIds->map(function ($metalId) use ($metals, $purchasesByMetal, $salesByMetal) {
            $metal = $metals->get($metalId);
            $p = $purchasesByMetal->get($metalId);
            $s = $salesByMetal->get($metalId);

            $purchaseWeight = (float) ($p->total_weight ?? 0);
            $saleWeight = (float) ($s->total_weight ?? 0);
            $purchaseAmount = (float) ($p->total_amount ?? 0);
            $saleAmount = (float) ($s->total_amount ?? 0);

            return [
                'metal_id' => $metalId,
                'metal_name' => $metal?->name ?? 'Desconocido',
                'metal_symbol' => $metal?->symbol ?? '??',
                'purchase_weight' => round($purchaseWeight, 4),
                'sale_weight' => round($saleWeight, 4),
                'net_weight' => round($saleWeight - $purchaseWeight, 4),
                'purchase_amount' => round($purchaseAmount, 2),
                'sale_amount' => round($saleAmount, 2),
                'net_amount' => round($saleAmount - $purchaseAmount, 2),
                'purchase_items' => (int) ($p->item_count ?? 0),
                'sale_items' => (int) ($s->item_count ?? 0),
            ];
        })->values()->toArray();
    }

    private function timeSeries(Carbon $from, Carbon $to, string $groupBy): array
    {
        $dateFormat = match ($groupBy) {
            'week' => '%x-W%v',
            'month' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        $purchaseSeries = Purchase::query()
            ->select(
                DB::raw("DATE_FORMAT(purchase_date, '{$dateFormat}') as period"),
                DB::raw('SUM(total) as total_amount'),
                DB::raw('COUNT(*) as count'),
            )
            ->whereIn('status', self::ACTIVE_STATUSES)
            ->whereBetween('purchase_date', [$from, $to])
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->keyBy('period');

        $saleSeries = Sale::query()
            ->select(
                DB::raw("DATE_FORMAT(sale_date, '{$dateFormat}') as period"),
                DB::raw('SUM(total) as total_amount'),
                DB::raw('COUNT(*) as count'),
            )
            ->whereIn('status', self::ACTIVE_STATUSES)
            ->whereBetween('sale_date', [$from, $to])
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->keyBy('period');

        $allPeriods = $purchaseSeries->keys()->merge($saleSeries->keys())->unique()->sort()->values();

        return $allPeriods->map(function ($period) use ($purchaseSeries, $saleSeries) {
            $p = $purchaseSeries->get($period);
            $s = $saleSeries->get($period);

            return [
                'period' => $period,
                'purchase_amount' => round((float) ($p->total_amount ?? 0), 2),
                'purchase_count' => (int) ($p->count ?? 0),
                'sale_amount' => round((float) ($s->total_amount ?? 0), 2),
                'sale_count' => (int) ($s->count ?? 0),
            ];
        })->toArray();
    }
}
