<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PayableCategory;

class PayableCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['name' => 'Alimentação', 'color' => '#22C55E'],
            ['name' => 'Transporte', 'color' => '#3B82F6'],
            ['name' => 'Serviços', 'color' => '#FFBD59'],
            ['name' => 'Impostos', 'color' => '#F87171'],
            ['name' => 'Fornecedores', 'color' => '#8B5CF6'],
            ['name' => 'Salários', 'color' => '#10B981'],
            ['name' => 'Aluguel', 'color' => '#F59E0B'],
            ['name' => 'Energia/Água', 'color' => '#06B6D4'],
            ['name' => 'Marketing', 'color' => '#EC4899'],
            ['name' => 'Outros', 'color' => '#9CA3AF'],
        ];

        foreach ($categories as $category) {
            // Verificar se já existe (evitar duplicatas)
            $exists = PayableCategory::where('name', $category['name'])
                ->whereNull('user_id')
                ->first();

            if (!$exists) {
                PayableCategory::create([
                    'user_id' => null, // Categoria global
                    'name' => $category['name'],
                    'color' => $category['color'],
                ]);
            }
        }
    }
}

