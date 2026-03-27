<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Client;
use App\Models\InventoryMovement;
use App\Models\Metal;
use App\Models\Purchase;
use App\Models\PurchaseRequest;
use App\Models\Sale;
use App\Models\Settlement;
use App\Models\Shipment;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        if ($user->hasRole('administrador')) {
            return $this->adminDashboard();
        }

        if ($user->hasRole('operador')) {
            return $this->operadorDashboard($user);
        }

        if ($user->hasRole('auditor')) {
            return $this->auditorDashboard();
        }

        if ($user->hasRole('cliente')) {
            return $this->clienteDashboard($user);
        }

        return Inertia::render('Dashboard', [
            'role' => 'default',
            'stats' => [],
        ]);
    }

    private function adminDashboard(): Response
    {
        $stats = [
            'users' => [
                'total' => User::count(),
                'active' => User::where('is_active', true)->count(),
            ],
            'clients' => [
                'total' => Client::count(),
                'active' => Client::where('is_active', true)->count(),
            ],
            'suppliers' => [
                'total' => Supplier::count(),
                'active' => Supplier::where('is_active', true)->count(),
            ],
            'metals' => [
                'total' => Metal::count(),
                'active' => Metal::where('is_active', true)->count(),
            ],
            'purchases' => [
                'total' => Purchase::count(),
                'draft' => Purchase::where('status', 'borrador')->count(),
                'confirmed' => Purchase::where('status', 'confirmada')->count(),
                'settled' => Purchase::where('status', 'liquidada')->count(),
                'cancelled' => Purchase::where('status', 'cancelada')->count(),
                'total_amount' => Purchase::whereIn('status', ['confirmada', 'liquidada'])->sum('total'),
            ],
            'sales' => [
                'total' => Sale::count(),
                'draft' => Sale::where('status', 'borrador')->count(),
                'confirmed' => Sale::where('status', 'confirmada')->count(),
                'settled' => Sale::where('status', 'liquidada')->count(),
                'cancelled' => Sale::where('status', 'cancelada')->count(),
                'total_amount' => Sale::whereIn('status', ['confirmada', 'liquidada'])->sum('total'),
            ],
            'settlements' => [
                'total' => Settlement::count(),
                'pending' => Settlement::where('status', 'pendiente')->count(),
                'partial' => Settlement::where('status', 'parcial')->count(),
                'completed' => Settlement::where('status', 'completada')->count(),
                'voided' => Settlement::where('status', 'anulada')->count(),
            ],
            'shipments' => [
                'total' => Shipment::count(),
                'preparing' => Shipment::where('status', 'preparando')->count(),
                'in_transit' => Shipment::where('status', 'en_transito')->count(),
                'delivered' => Shipment::where('status', 'entregado')->count(),
            ],
        ];

        $recentActivity = AuditLog::with('user')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn ($log) => [
                'id' => $log->id,
                'user_name' => $log->user?->name ?? 'Sistema',
                'action' => $log->action,
                'auditable_type' => class_basename($log->auditable_type),
                'auditable_id' => $log->auditable_id,
                'created_at' => $log->created_at?->format('d/m/Y H:i'),
            ]);

        $recentPurchases = Purchase::with('supplier')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'purchase_number' => $p->purchase_number,
                'supplier_name' => $p->supplier?->business_name,
                'total' => $p->total,
                'currency' => $p->currency,
                'status' => $p->status,
                'purchase_date' => $p->purchase_date?->format('d/m/Y'),
            ]);

        $recentSales = Sale::with('client')
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'sale_number' => $s->sale_number,
                'client_name' => $s->client?->business_name,
                'total' => $s->total,
                'currency' => $s->currency,
                'status' => $s->status,
                'sale_date' => $s->sale_date?->format('d/m/Y'),
            ]);

        return Inertia::render('Dashboard', [
            'role' => 'administrador',
            'stats' => $stats,
            'recentActivity' => $recentActivity,
            'recentPurchases' => $recentPurchases,
            'recentSales' => $recentSales,
        ]);
    }

    private function operadorDashboard(User $user): Response
    {
        $stats = [
            'clients' => [
                'total' => Client::count(),
                'active' => Client::where('is_active', true)->count(),
            ],
            'suppliers' => [
                'total' => Supplier::count(),
                'active' => Supplier::where('is_active', true)->count(),
            ],
            'purchases' => [
                'total' => Purchase::count(),
                'draft' => Purchase::where('status', 'borrador')->count(),
                'confirmed' => Purchase::where('status', 'confirmada')->count(),
                'my_pending' => Purchase::where('user_id', $user->id)
                    ->where('status', 'borrador')
                    ->count(),
            ],
            'sales' => [
                'total' => Sale::count(),
                'draft' => Sale::where('status', 'borrador')->count(),
                'confirmed' => Sale::where('status', 'confirmada')->count(),
                'my_pending' => Sale::where('user_id', $user->id)
                    ->where('status', 'borrador')
                    ->count(),
            ],
            'settlements' => [
                'pending' => Settlement::where('status', 'pendiente')->count(),
                'partial' => Settlement::where('status', 'parcial')->count(),
            ],
            'shipments' => [
                'preparing' => Shipment::where('status', 'preparando')->count(),
                'in_transit' => Shipment::where('status', 'en_transito')->count(),
            ],
        ];

        $myRecentPurchases = Purchase::with('supplier')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'purchase_number' => $p->purchase_number,
                'supplier_name' => $p->supplier?->business_name,
                'total' => $p->total,
                'currency' => $p->currency,
                'status' => $p->status,
                'purchase_date' => $p->purchase_date?->format('d/m/Y'),
            ]);

        $myRecentSales = Sale::with('client')
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'sale_number' => $s->sale_number,
                'client_name' => $s->client?->business_name,
                'total' => $s->total,
                'currency' => $s->currency,
                'status' => $s->status,
                'sale_date' => $s->sale_date?->format('d/m/Y'),
            ]);

        $pendingShipments = Shipment::with('user')
            ->whereIn('status', ['preparando', 'en_transito'])
            ->latest()
            ->take(5)
            ->get()
            ->map(fn ($sh) => [
                'id' => $sh->id,
                'tracking_number' => $sh->tracking_number,
                'carrier' => $sh->carrier,
                'status' => $sh->status,
                'destination_address' => $sh->destination_address,
                'estimated_delivery' => $sh->estimated_delivery?->format('d/m/Y'),
            ]);

        return Inertia::render('Dashboard', [
            'role' => 'operador',
            'stats' => $stats,
            'myRecentPurchases' => $myRecentPurchases,
            'myRecentSales' => $myRecentSales,
            'pendingShipments' => $pendingShipments,
        ]);
    }

    private function auditorDashboard(): Response
    {
        $stats = [
            'purchases' => [
                'total' => Purchase::count(),
                'confirmed' => Purchase::where('status', 'confirmada')->count(),
                'settled' => Purchase::where('status', 'liquidada')->count(),
                'total_amount' => Purchase::whereIn('status', ['confirmada', 'liquidada'])->sum('total'),
            ],
            'sales' => [
                'total' => Sale::count(),
                'confirmed' => Sale::where('status', 'confirmada')->count(),
                'settled' => Sale::where('status', 'liquidada')->count(),
                'total_amount' => Sale::whereIn('status', ['confirmada', 'liquidada'])->sum('total'),
            ],
            'settlements' => [
                'total' => Settlement::count(),
                'pending' => Settlement::where('status', 'pendiente')->count(),
                'partial' => Settlement::where('status', 'parcial')->count(),
                'completed' => Settlement::where('status', 'completada')->count(),
                'voided' => Settlement::where('status', 'anulada')->count(),
                'total_amount' => Settlement::where('status', 'completada')->sum('total_amount'),
            ],
        ];

        $recentAuditLogs = AuditLog::with('user')
            ->latest()
            ->take(15)
            ->get()
            ->map(fn ($log) => [
                'id' => $log->id,
                'user_name' => $log->user?->name ?? 'Sistema',
                'action' => $log->action,
                'auditable_type' => class_basename($log->auditable_type),
                'auditable_id' => $log->auditable_id,
                'ip_address' => $log->ip_address,
                'created_at' => $log->created_at?->format('d/m/Y H:i'),
            ]);

        $recentSettlements = Settlement::with('user')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'settlement_number' => $s->settlement_number,
                'user_name' => $s->user?->name,
                'total_amount' => $s->total_amount,
                'currency' => $s->currency,
                'status' => $s->status,
                'settlement_date' => $s->settlement_date?->format('d/m/Y'),
            ]);

        return Inertia::render('Dashboard', [
            'role' => 'auditor',
            'stats' => $stats,
            'recentAuditLogs' => $recentAuditLogs,
            'recentSettlements' => $recentSettlements,
        ]);
    }

    private function clienteDashboard(User $user): Response
    {
        $metals = Metal::where('is_active', true)
            ->get()
            ->map(function ($metal) {
                $price = $metal->latestPrice();

                return [
                    'id' => $metal->id,
                    'name' => $metal->name,
                    'symbol' => $metal->symbol,
                    'price_per_gram' => $price?->price_per_gram,
                    'price_per_troy_ounce' => $price?->price_per_troy_ounce,
                    'currency' => $price?->currency ?? 'USD',
                    'effective_date' => $price?->effective_date?->format('d/m/Y'),
                ];
            });

        $myRequests = PurchaseRequest::with('metal')
            ->where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get()
            ->map(fn ($r) => [
                'id' => $r->id,
                'request_number' => $r->request_number,
                'metal_name' => $r->metal?->name,
                'metal_symbol' => $r->metal?->symbol,
                'estimated_weight_grams' => $r->estimated_weight_grams,
                'quoted_price_per_gram' => $r->quoted_price_per_gram,
                'estimated_total' => $r->estimated_total,
                'currency' => $r->currency,
                'status' => $r->status->value,
                'status_label' => $r->status->label(),
                'operator_notes' => $r->operator_notes,
                'created_at' => $r->created_at?->format('d/m/Y H:i'),
            ]);

        $stats = [
            'requests' => [
                'total' => PurchaseRequest::where('user_id', $user->id)->count(),
                'pending' => PurchaseRequest::where('user_id', $user->id)
                    ->whereIn('status', ['pendiente', 'en_revision'])
                    ->count(),
                'approved' => PurchaseRequest::where('user_id', $user->id)
                    ->where('status', 'aprobada')
                    ->count(),
                'converted' => PurchaseRequest::where('user_id', $user->id)
                    ->where('status', 'convertida')
                    ->count(),
            ],
        ];

        $myPurchases = collect();
        if ($user->supplier_id) {
            $myPurchases = Purchase::where('supplier_id', $user->supplier_id)
                ->latest()
                ->take(5)
                ->get()
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'purchase_number' => $p->purchase_number,
                    'total' => $p->total,
                    'currency' => $p->currency,
                    'status' => $p->status instanceof \BackedEnum ? $p->status->value : $p->status,
                    'purchase_date' => $p->purchase_date?->format('d/m/Y'),
                ]);
        }

        return Inertia::render('Dashboard', [
            'role' => 'cliente',
            'stats' => $stats,
            'metals' => $metals,
            'myRequests' => $myRequests,
            'myPurchases' => $myPurchases,
        ]);
    }
}
