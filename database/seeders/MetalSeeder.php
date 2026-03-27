<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Metal;
use Illuminate\Database\Seeder;

class MetalSeeder extends Seeder
{
    public function run(): void
    {
        $metals = [
            [
                'name' => 'Oro',
                'symbol' => 'Au',
                'description' => 'Metal precioso, elemento quimico numero 79',
            ],
            [
                'name' => 'Plata',
                'symbol' => 'Ag',
                'description' => 'Metal precioso, elemento quimico numero 47',
            ],
            [
                'name' => 'Platino',
                'symbol' => 'Pt',
                'description' => 'Metal precioso, elemento quimico numero 78',
            ],
        ];

        foreach ($metals as $metal) {
            Metal::firstOrCreate(
                ['symbol' => $metal['symbol']],
                $metal
            );
        }
    }
}
