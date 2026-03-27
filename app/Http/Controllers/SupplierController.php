<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Supplier\StoreSupplierRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SupplierController extends Controller
{
    private const SUPPLIER_TYPES = [
        ['value' => 'empresa', 'label' => 'Empresa / Comercializadora'],
        ['value' => 'persona', 'label' => 'Persona (balsero/trabajador)'],
    ];

    private const SUPPLIER_DOCUMENT_TYPES = [
        ['value' => 'NIT', 'label' => 'NIT (Bolivia)'],
        ['value' => 'CI', 'label' => 'Carnet de Identidad'],
        ['value' => 'LICENCIA', 'label' => 'Licencia de Conducir'],
        ['value' => 'PASAPORTE', 'label' => 'Pasaporte'],
    ];

    private const PAYMENT_METHODS = [
        ['value' => 'efectivo', 'label' => 'Efectivo'],
        ['value' => 'transferencia', 'label' => 'Transferencia'],
        ['value' => 'cheque', 'label' => 'Cheque'],
        ['value' => 'qr', 'label' => 'QR'],
        ['value' => 'otro', 'label' => 'Otro'],
    ];

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Supplier::class);

        $search = trim((string) $request->string('search'));

        $suppliers = Supplier::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('business_name', 'like', "%{$search}%")
                        ->orWhere('trade_name', 'like', "%{$search}%")
                        ->orWhere('document_number', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('bank_name', 'like', "%{$search}%");
                });
            })
            ->withCount('purchases')
            ->orderBy('business_name')
            ->get()
            ->map(fn (Supplier $supplier) => [
                'id' => $supplier->id,
                'supplier_type' => $supplier->supplier_type,
                'supplier_type_label' => $this->labelFor(self::SUPPLIER_TYPES, (string) $supplier->supplier_type),
                'document_type' => (string) $supplier->document_type,
                'document_type_label' => $this->labelFor(self::SUPPLIER_DOCUMENT_TYPES, (string) $supplier->document_type),
                'document_number' => $supplier->document_number,
                'business_name' => $supplier->business_name,
                'trade_name' => $supplier->trade_name,
                'email' => $supplier->email,
                'phone' => $supplier->phone,
                'bank_name' => $supplier->bank_name,
                'payment_method' => $supplier->payment_method,
                'payment_method_label' => $this->labelFor(self::PAYMENT_METHODS, (string) $supplier->payment_method),
                'is_active' => $supplier->is_active,
                'purchases_count' => $supplier->purchases_count,
            ]);

        return Inertia::render('Suppliers/Index', [
            'suppliers' => $suppliers,
            'filters' => [
                'search' => $search,
            ],
            'can' => [
                'create' => auth()->user()->can('suppliers.create'),
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Supplier::class);

        return Inertia::render('Suppliers/Create', [
            'supplierTypes' => self::SUPPLIER_TYPES,
            'documentTypes' => self::SUPPLIER_DOCUMENT_TYPES,
            'paymentMethods' => self::PAYMENT_METHODS,
        ]);
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Supplier::create([
            ...$validated,
            'country' => $validated['country'] ?? 'Bolivia',
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Proveedor registrado exitosamente.');
    }

    public function show(Supplier $supplier): Response
    {
        $this->authorize('view', $supplier);

        $supplier->loadCount('purchases');

        return Inertia::render('Suppliers/Show', [
            'supplier' => [
                'id' => $supplier->id,
                'supplier_type' => $supplier->supplier_type,
                'supplier_type_label' => $this->labelFor(self::SUPPLIER_TYPES, (string) $supplier->supplier_type),
                'document_type' => (string) $supplier->document_type,
                'document_type_label' => $this->labelFor(self::SUPPLIER_DOCUMENT_TYPES, (string) $supplier->document_type),
                'document_number' => $supplier->document_number,
                'business_name' => $supplier->business_name,
                'trade_name' => $supplier->trade_name,
                'email' => $supplier->email,
                'phone' => $supplier->phone,
                'address' => $supplier->address,
                'city' => $supplier->city,
                'state' => $supplier->state,
                'country' => $supplier->country,
                'contact_person' => $supplier->contact_person,
                'contact_phone' => $supplier->contact_phone,
                'bank_name' => $supplier->bank_name,
                'bank_account_number' => $supplier->bank_account_number,
                'bank_cci' => $supplier->bank_cci,
                'payment_method' => $supplier->payment_method,
                'payment_method_label' => $this->labelFor(self::PAYMENT_METHODS, (string) $supplier->payment_method),
                'notes' => $supplier->notes,
                'is_active' => $supplier->is_active,
                'purchases_count' => $supplier->purchases_count,
            ],
            'can' => [
                'edit' => auth()->user()->can('suppliers.edit'),
                'delete' => auth()->user()->can('suppliers.delete'),
            ],
        ]);
    }

    public function edit(Supplier $supplier): Response
    {
        $this->authorize('update', $supplier);

        return Inertia::render('Suppliers/Edit', [
            'supplier' => [
                'id' => $supplier->id,
                'supplier_type' => $supplier->supplier_type,
                'document_type' => (string) $supplier->document_type,
                'document_number' => $supplier->document_number,
                'business_name' => $supplier->business_name,
                'trade_name' => $supplier->trade_name,
                'email' => $supplier->email,
                'phone' => $supplier->phone,
                'address' => $supplier->address,
                'city' => $supplier->city,
                'state' => $supplier->state,
                'country' => $supplier->country,
                'contact_person' => $supplier->contact_person,
                'contact_phone' => $supplier->contact_phone,
                'bank_name' => $supplier->bank_name,
                'bank_account_number' => $supplier->bank_account_number,
                'bank_cci' => $supplier->bank_cci,
                'payment_method' => $supplier->payment_method,
                'notes' => $supplier->notes,
                'is_active' => $supplier->is_active,
            ],
            'supplierTypes' => self::SUPPLIER_TYPES,
            'documentTypes' => self::SUPPLIER_DOCUMENT_TYPES,
            'paymentMethods' => self::PAYMENT_METHODS,
        ]);
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($request->validated());

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'Proveedor actualizado exitosamente.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $this->authorize('delete', $supplier);

        if ($supplier->purchases()->exists()) {
            return back()->with('error', 'No se puede eliminar un proveedor con compras asociadas.');
        }

        $supplier->delete();

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Proveedor eliminado exitosamente.');
    }

    private function labelFor(array $options, string $value): string
    {
        foreach ($options as $option) {
            if (($option['value'] ?? null) === $value) {
                return (string) $option['label'];
            }
        }

        return $value;
    }
}
