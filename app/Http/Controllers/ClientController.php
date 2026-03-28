<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\DocumentType;
use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Client::class);

        $search = trim((string) $request->string('search'));

        $clients = Client::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('business_name', 'like', "%{$search}%")
                        ->orWhere('trade_name', 'like', "%{$search}%")
                        ->orWhere('document_number', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->withCount('sales')
            ->orderBy('business_name')
            ->get()
            ->map(fn (Client $client) => [
                'id' => $client->id,
                'document_type' => $client->document_type?->value ?? $client->document_type,
                'document_type_label' => $client->document_type?->label() ?? $client->document_type,
                'document_number' => $client->document_number,
                'business_name' => $client->business_name,
                'trade_name' => $client->trade_name,
                'email' => $client->email,
                'phone' => $client->phone,
                'is_active' => $client->is_active,
                'sales_count' => $client->sales_count,
            ]);

        return Inertia::render('Clients/Index', [
            'clients' => $clients,
            'filters' => [
                'search' => $search,
            ],
            'can' => [
                'create' => auth()->user()->can('clients.create'),
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Client::class);

        return Inertia::render('Clients/Create', [
            'documentTypes' => $this->documentTypeOptions(),
        ]);
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Client::create([
            ...$validated,
            'country' => $validated['country'] ?? 'Peru',
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()
            ->route('clients.index')
            ->with('success', 'Cliente registrado exitosamente.');
    }

    public function show(Client $client): Response
    {
        $this->authorize('view', $client);

        $client->loadCount('sales');

        return Inertia::render('Clients/Show', [
            'client' => [
                'id' => $client->id,
                'document_type' => $client->document_type?->value ?? $client->document_type,
                'document_type_label' => $client->document_type?->label() ?? $client->document_type,
                'document_number' => $client->document_number,
                'business_name' => $client->business_name,
                'trade_name' => $client->trade_name,
                'email' => $client->email,
                'phone' => $client->phone,
                'address' => $client->address,
                'city' => $client->city,
                'state' => $client->state,
                'country' => $client->country,
                'contact_person' => $client->contact_person,
                'contact_phone' => $client->contact_phone,
                'notes' => $client->notes,
                'is_active' => $client->is_active,
                'sales_count' => $client->sales_count,
            ],
            'can' => [
                'edit' => auth()->user()->can('clients.edit'),
                'delete' => auth()->user()->can('clients.delete'),
            ],
        ]);
    }

    public function edit(Client $client): Response
    {
        $this->authorize('update', $client);

        return Inertia::render('Clients/Edit', [
            'client' => [
                'id' => $client->id,
                'document_type' => $client->document_type?->value ?? $client->document_type,
                'document_number' => $client->document_number,
                'business_name' => $client->business_name,
                'trade_name' => $client->trade_name,
                'email' => $client->email,
                'phone' => $client->phone,
                'address' => $client->address,
                'city' => $client->city,
                'state' => $client->state,
                'country' => $client->country,
                'contact_person' => $client->contact_person,
                'contact_phone' => $client->contact_phone,
                'notes' => $client->notes,
                'is_active' => $client->is_active,
            ],
            'documentTypes' => $this->documentTypeOptions(),
        ]);
    }

    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $client->update($request->validated());

        return redirect()
            ->route('clients.show', $client)
            ->with('success', 'Cliente actualizado exitosamente.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $this->authorize('delete', $client);

        if ($client->sales()->exists()) {
            return back()->with('error', 'No se puede eliminar un cliente con ventas asociadas.');
        }

        $client->delete();

        return redirect()
            ->route('clients.index')
            ->with('success', 'Cliente eliminado exitosamente.');
    }

    /**
     * Opciones de tipo de documento para formularios.
     */
    private function documentTypeOptions(): array
    {
        return array_map(
            static fn (DocumentType $type): array => [
                'value' => $type->value,
                'label' => $type->label(),
            ],
            DocumentType::cases(),
        );
    }
}
