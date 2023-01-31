<?php

namespace Database\Seeders;

use Faker\Factory;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        $faker = Factory::create('fr_FR');
        $category = Category::pluck('id')->toArray();
        $user = User::pluck('id')->toArray();


        for ($i = 0; $i < 50; $i++) {
          $post = new Post();
          $post->title = $faker->text(100);
          $post->description = $faker->text(1000);
          $post->category_id = $faker->randomElement($category);
          $post->user_id = $faker->randomElement($user);
          $post->save();
    }
}
}