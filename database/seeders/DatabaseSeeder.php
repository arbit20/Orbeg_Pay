<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            MetalSeeder::class,
        ]);

        $admin = User::factory()->create([
            'name' => 'Administrador Orbeg',
            'email' => 'admin@orbegcapital.com',
            'phone' => '+51999000001',
        ]);
        $admin->assignRole('administrador');

        $operador = User::factory()->create([
            'name' => 'Operador Principal',
            'email' => 'operador@orbegcapital.com',
            'phone' => '+51999000002',
        ]);
        $operador->assignRole('operador');

        $auditor = User::factory()->create([
            'name' => 'Auditor General',
            'email' => 'auditor@orbegcapital.com',
            'phone' => '+51999000003',
        ]);
        $auditor->assignRole('auditor');

        $supplierDemo = Supplier::firstOrCreate(
            ['document_number' => '12345678'],
            [
                'supplier_type' => 'persona',
                'document_type' => 'DNI',
                'business_name' => 'Juan Pérez',
                'phone' => '+51987654321',
                'city' => 'Lima',
                'country' => 'Peru',
                'is_active' => true,
            ]
        );

        $cliente = User::factory()->create([
            'name' => 'Juan Pérez',
            'email' => 'cliente@ejemplo.com',
            'phone' => '+51987654321',
            'supplier_id' => $supplierDemo->id,
        ]);
        $cliente->assignRole('cliente');
    }
}
