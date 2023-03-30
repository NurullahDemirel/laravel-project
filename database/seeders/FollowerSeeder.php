<?php

namespace Database\Seeders;

use App\Models\Follower;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FollowerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = array();

        foreach (range(1, 500) as $follow) {
            $data[] = [
                'follow_to' => User::inRandomOrder()->first()->id,
                'follow_by' => User::inRandomOrder()->first()->id,
                'is_accepted' => rand(0, 1),
            ];
        }

        Follower::insert($data);
    }
}
