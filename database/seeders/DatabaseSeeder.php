<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(200)->create();

        echo "Users was created successfully" . PHP_EOL;

        Post::factory(1000)->create();

        echo "Posts was created successfully" . PHP_EOL;

        Comment::factory(400)->create();

        foreach (Comment::inRandomOrder()->take(150)->get() as $comment) {
            $comment->update(['parent_id' => Comment::inRandomOrder()->first()->id]);
        }

        Comment::where('id', '=', 'parent_id')->delete();

        echo "Comments was created successfully" . PHP_EOL;

        $this->call(FollowerSeeder::class);

        echo "Followers was added randomly" . PHP_EOL;

        $this->call(LikeSeeder::class);

        echo "Likes was added randomly" . PHP_EOL;
    }
}
