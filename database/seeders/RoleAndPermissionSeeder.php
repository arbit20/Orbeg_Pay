<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Usuarios
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // Clientes
            'clients.view',
            'clients.create',
            'clients.edit',
            'clients.delete',

            // Proveedores
            'suppliers.view',
            'suppliers.create',
            'suppliers.edit',
            'suppliers.delete',

            // Metales y precios
            'metals.view',
            'metals.create',
            'metals.edit',
            'metals.delete',
            'metal_prices.view',
            'metal_prices.create',

            // Compras
            'purchases.view',
            'purchases.create',
            'purchases.edit',
            'purchases.delete',
            'purchases.confirm',
            'purchases.cancel',

            // Ventas
            'sales.view',
            'sales.create',
            'sales.edit',
            'sales.delete',
            'sales.confirm',
            'sales.cancel',

            // Liquidaciones
            'settlements.view',
            'settlements.create',
            'settlements.edit',
            'settlements.void',
            'settlements.download_pdf',

            // Pagos
            'settlement_payments.view',
            'settlement_payments.create',

            // Inventario
            'inventory.view',
            'inventory.adjust',

            // Envios
            'shipments.view',
            'shipments.create',
            'shipments.edit',

            // Auditoria
            'audit_logs.view',

            // Reportes
            'reports.view',

            // Solicitudes de compra (clientes)
            'purchase_requests.view',
            'purchase_requests.create',
            'purchase_requests.view_own',

            // Cotizador
            'price_calculator.use',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Administrador: acceso total
        $admin = Role::firstOrCreate(['name' => 'administrador']);
        $admin->syncPermissions(Permission::all());

        // Operador: operaciones diarias sin gestion de usuarios ni auditoria directa
        $operador = Role::firstOrCreate(['name' => 'operador']);
        $operador->syncPermissions(
            Permission::whereNotIn('name', [
                'users.create',
                'users.edit',
                'users.delete',
                'audit_logs.view',
            ])->get()
        );

        // Auditor: solo lectura y reportes
        $auditor = Role::firstOrCreate(['name' => 'auditor']);
        $auditor->syncPermissions(
            Permission::where('name', 'like', '%.view')
                ->orWhere('name', 'settlements.download_pdf')
                ->orWhere('name', 'reports.view')
                ->orWhere('name', 'audit_logs.view')
                ->get()
        );

        // Cliente: persona que vende oro a Orbeg Capital
        $cliente = Role::firstOrCreate(['name' => 'cliente']);
        $cliente->syncPermissions([
            'purchase_requests.view_own',
            'purchase_requests.create',
            'price_calculator.use',
            'metal_prices.view',
            'settlements.view',
            'settlements.download_pdf',
        ]);
    }
}
