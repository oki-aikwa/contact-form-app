<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create('ja_JP');


        $categoryIds = DB::table('categories')->pluck('id')->toArray();

        for ($i = 0; $i < 20; $i++) {
            DB::table('contacts')->insert([
                'first_name' => $faker->lastName(),
                'last_name' => $faker->firstName(),
                'gender' => $faker->numberBetween(1, 3),
                'email' => $faker->unique()->safeEmail(),

                // 💡 マイグレーション（設計図）に合わせてアンダーバーなしに修正
                'tel1' => $faker->regexify('0[0-9]{2}'),
                'tel2' => $faker->regexify('[0-9]{4}'),
                'tel3' => $faker->regexify('[0-9]{4}'),

                'address' => $faker->prefecture() . $faker->city() . $faker->streetAddress(),
                'building' => $faker->optional(0.7)->secondaryAddress(),
                'category_id' => $faker->randomElement($categoryIds),


                'body' => $faker->realText(100),

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
