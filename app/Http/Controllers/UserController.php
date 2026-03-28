<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', User::class);

        $search = trim((string) $request->string('search'));

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->with(['roles', 'supplier:id,business_name'])
            ->orderBy('username')
            ->get()
            ->map(fn (User $user) => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
                'is_active' => $user->is_active,
                'role' => $user->roles->first()?->name,
                'supplier' => $user->supplier?->business_name,
                'created_at' => $user->created_at?->format('d/m/Y'),
            ]);

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => [
                'search' => $search,
            ],
            'can' => [
                'create' => auth()->user()->can('users.create'),
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', User::class);

        return Inertia::render('Users/Create', [
            'roles' => $this->roleOptions(),
            'suppliers' => $this->supplierOptions(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'birth_date' => $validated['birth_date'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'supplier_id' => $validated['supplier_id'] ?? null,
        ]);

        $user->assignRole($validated['role']);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    public function show(User $user): Response
    {
        $this->authorize('view', $user);

        $user->load(['roles', 'supplier:id,business_name']);

        return Inertia::render('Users/Show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'birth_date' => $user->birth_date?->format('d/m/Y'),
                'email' => $user->email,
                'phone' => $user->phone,
                'is_active' => $user->is_active,
                'role' => $user->roles->first()?->name,
                'supplier' => $user->supplier ? [
                    'id' => $user->supplier->id,
                    'business_name' => $user->supplier->business_name,
                ] : null,
                'created_at' => $user->created_at?->format('d/m/Y H:i'),
                'updated_at' => $user->updated_at?->format('d/m/Y H:i'),
            ],
            'can' => [
                'edit' => auth()->user()->can('users.edit'),
                'delete' => auth()->user()->can('users.delete') && auth()->id() !== $user->id,
            ],
        ]);
    }

    public function edit(User $user): Response
    {
        $this->authorize('update', $user);

        $user->load(['roles', 'supplier:id,business_name']);

        return Inertia::render('Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'birth_date' => $user->birth_date?->format('Y-m-d'),
                'email' => $user->email,
                'phone' => $user->phone,
                'is_active' => $user->is_active,
                'role' => $user->roles->first()?->name,
                'supplier_id' => $user->supplier_id,
            ],
            'roles' => $this->roleOptions(),
            'suppliers' => $this->supplierOptions(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        $data = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'birth_date' => $validated['birth_date'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => $validated['is_active'] ?? $user->is_active,
            'supplier_id' => $validated['supplier_id'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);
        $user->syncRoles([$validated['role']]);

        return redirect()
            ->route('users.show', $user)
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        if (auth()->id() === $user->id) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }

    private function roleOptions(): array
    {
        return [
            ['value' => 'administrador', 'label' => 'Administrador'],
            ['value' => 'operador', 'label' => 'Operador'],
            ['value' => 'auditor', 'label' => 'Auditor'],
            ['value' => 'cliente', 'label' => 'Cliente'],
        ];
    }

    private function supplierOptions(): array
    {
        return Supplier::query()
            ->where('is_active', true)
            ->orderBy('business_name')
            ->get(['id', 'business_name'])
            ->map(fn (Supplier $supplier) => [
                'value' => $supplier->id,
                'label' => $supplier->business_name,
            ])
            ->toArray();
    }
}
