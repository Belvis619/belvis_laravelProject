<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Donation;
use App\Models\DonationType;

class DonationSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name'=>'Cash','description'=>'Monetary donations'],
            ['name'=>'Goods','description'=>'Non-cash items'],
            ['name'=>'Medical Supplies','description'=>'Medical donations'],
            ['name'=>'Clothing','description'=>'Clothing donations'],
        ];

        foreach ($types as $t) {
            DonationType::firstOrCreate(
                ['name' => $t['name']],
                ['description' => $t['description']]
            );
        }

        // create sample donations; this will reference existing types
        Donation::factory()->count(10)->create();
    }
}