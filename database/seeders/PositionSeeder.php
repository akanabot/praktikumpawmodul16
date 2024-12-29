<?php
namespace Database\Seeders;
use App\Models\Position;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;



class PositionSeeder extends Seeder
{
    public function run()
    {
        Position::factory()->count(5)->create();
    }
}
