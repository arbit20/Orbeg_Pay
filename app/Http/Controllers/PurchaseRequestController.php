<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\PurchaseRequestStatus;
use App\Models\Metal;
use App\Models\PurchaseRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PurchaseRequestController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $query = PurchaseRequest::with(['metal', 'supplier', 'user', 'reviewer']);

        if ($user->hasRole('cliente')) {
            $query->where('user_id', $user->id);
        }

        $requests = $query->latest()->paginate(15)->through(fn ($r) => [
            'id' => $r->id,
            'request_number' => $r->request_number,
            'metal_name' => $r->metal?->name,
            'metal_symbol' => $r->metal?->symbol,
            'estimated_weight_grams' => $r->estimated_weight_grams,
            'estimated_purity' => $r->estimated_purity,
            'quoted_price_per_gram' => $r->quoted_price_per_gram,
            'estimated_total' => $r->estimated_total,
            'currency' => $r->currency,
            'status' => $r->status->value,
            'status_label' => $r->status->label(),
            'client_notes' => $r->client_notes,
            'operator_notes' => $r->operator_notes,
            'reviewer_name' => $r->reviewer?->name,
            'reviewed_at' => $r->reviewed_at?->format('d/m/Y H:i'),
            'created_at' => $r->created_at?->format('d/m/Y H:i'),
        ]);

        return Inertia::render('PurchaseRequests/Index', [
            'purchaseRequests' => $requests,
        ]);
    }

    public function create(Request $request): Response
    {
        $metals = Metal::where('is_active', true)
            ->with(['prices' => fn ($q) => $q->orderByDesc('effective_date')->limit(1)])
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'name' => $m->name,
                'symbol' => $m->symbol,
                'latest_price' => $m->latestPrice(),
            ]);

        return Inertia::render('PurchaseRequests/Create', [
            'metals' => $metals,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user->supplier_id) {
            return back()->with('error', 'Tu cuenta no está vinculada a un perfil de proveedor. Contacta al administrador.');
        }

        $validated = $request->validate([
            'metal_id' => 'required|exists:metals,id',
            'estimated_weight_grams' => 'required|numeric|min:0.0001',
            'estimated_purity' => 'nullable|numeric|min:0|max:1',
            'client_notes' => 'nullable|string|max:1000',
        ]);

        $metal = Metal::findOrFail($validated['metal_id']);
        $latestPrice = $metal->latestPrice();

        if (! $latestPrice) {
            return back()->with('error', 'No hay precio disponible para este metal. Intenta más tarde.');
        }

        $purity = $validated['estimated_purity'] ?? 1.0;
        $pricePerGram = (float) $latestPrice->price_per_gram;
        $weight = (float) $validated['estimated_weight_grams'];
        $estimatedTotal = $weight * $purity * $pricePerGram;

        PurchaseRequest::create([
            'request_number' => PurchaseRequest::generateNextNumber(),
            'supplier_id' => $user->supplier_id,
            'user_id' => $user->id,
            'metal_id' => $validated['metal_id'],
            'estimated_weight_grams' => $weight,
            'estimated_purity' => $purity,
            'quoted_price_per_gram' => $pricePerGram,
            'estimated_total' => $estimatedTotal,
            'currency' => $latestPrice->currency,
            'status' => PurchaseRequestStatus::PENDIENTE,
            'client_notes' => $validated['client_notes'] ?? null,
        ]);

        return redirect()
            ->route('purchase-requests.index')
            ->with('success', 'Solicitud de compra creada exitosamente. Un operador la revisará pronto.');
    }

    public function show(Request $request, PurchaseRequest $purchaseRequest): Response
    {
        $user = $request->user();

        if ($user->hasRole('cliente') && $purchaseRequest->user_id !== $user->id) {
            abort(403);
        }

        $purchaseRequest->load(['metal', 'supplier', 'user', 'reviewer', 'purchase']);

        return Inertia::render('PurchaseRequests/Show', [
            'purchaseRequest' => [
                'id' => $purchaseRequest->id,
                'request_number' => $purchaseRequest->request_number,
                'metal_name' => $purchaseRequest->metal?->name,
                'metal_symbol' => $purchaseRequest->metal?->symbol,
                'supplier_name' => $purchaseRequest->supplier?->business_name,
                'estimated_weight_grams' => $purchaseRequest->estimated_weight_grams,
                'estimated_purity' => $purchaseRequest->estimated_purity,
                'quoted_price_per_gram' => $purchaseRequest->quoted_price_per_gram,
                'estimated_total' => $purchaseRequest->estimated_total,
                'currency' => $purchaseRequest->currency,
                'status' => $purchaseRequest->status->value,
                'status_label' => $purchaseRequest->status->label(),
                'purchase_number' => $purchaseRequest->purchase?->purchase_number,
                'purchase_id' => $purchaseRequest->purchase_id,
                'client_notes' => $purchaseRequest->client_notes,
                'operator_notes' => $purchaseRequest->operator_notes,
                'reviewer_name' => $purchaseRequest->reviewer?->name,
                'reviewed_at' => $purchaseRequest->reviewed_at?->format('d/m/Y H:i'),
                'created_at' => $purchaseRequest->created_at?->format('d/m/Y H:i'),
                'updated_at' => $purchaseRequest->updated_at?->format('d/m/Y H:i'),
            ],
        ]);
    }
}
