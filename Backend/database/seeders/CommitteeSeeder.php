<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Committee;
class CommitteeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nepal = Committee::create([
            'name' => 'Nepal'
        ]);
        $india = Committee::create([
            'name' => 'India',
        ]);
        $bagmati = Committee::create([
            'name' => 'Bagmati',
            'parent_id' => $nepal->id,
        ]);

        $gandaki = Committee::create([
            'name' => 'Gandaki',
            'parent_id' => $nepal->id,
        ]);

        // India children
        $gujarat = Committee::create([
            'name' => 'Gujarat',
            'parent_id' => $india->id,
        ]);

        // Bagmati departments
        Committee::create([
            'name' => 'IT',
            'parent_id' => $bagmati->id,
        ]);

        Committee::create([
            'name' => 'Finance',
            'parent_id' => $bagmati->id,
        ]);
    }
}
