<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Client\StoreClientRequest;
use App\Http\Requests\Api\Client\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->string('search'));
        $perPage = (int) ($request->integer('per_page') ?: 15);
        $perPage = max(1, min(100, $perPage));

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
            ->paginate($perPage);

        $clients->getCollection()->transform(static fn (Client $client) => [
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

        return response()->json($clients);
    }

    public function store(StoreClientRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $client = Client::create([
            ...$validated,
            'country' => $validated['country'] ?? 'Peru',
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'data' => [
                'id' => $client->id,
            ] + $client->fresh()->toArray(),
        ], Response::HTTP_CREATED);
    }

    public function show(Client $client): JsonResponse
    {
        $client->loadCount('sales');

        return response()->json([
            'data' => [
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
                'deleted_at' => $client->deleted_at,
            ],
        ]);
    }

    public function update(UpdateClientRequest $request, Client $client): JsonResponse
    {
        $client->update($request->validated());

        return response()->json([
            'data' => [
                'id' => $client->id,
            ] + $client->fresh()->toArray(),
        ]);
    }

    public function destroy(Client $client): JsonResponse
    {
        if ($client->sales()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar un cliente con ventas asociadas.',
            ], Response::HTTP_CONFLICT);
        }

        $client->delete();

        return response()->json(status: Response::HTTP_NO_CONTENT);
    }
}

