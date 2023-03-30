<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $likeablClasses = [Post::class, User::class];

        $data = array();

        foreach (range(1, 100) as $number) {


            $likeablClass = $likeablClasses[rand(0, 1)];

            $data[] = [
                'user_id' => User::inRandomOrder()->first()->id,
                'likeable_type' => $likeablClass,
                'likeable_id' => $likeablClass == Post::class
                    ? Post::inRandomOrder()->first()->id
                    : Comment::inRandomOrder()->first()->id
            ];
        }
        Like::insert($data);


    }
}
