<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Factory::create('ja_JP');


        $categoryIds = DB::table('categories')->pluck('id')->toArray();
        $tagIds = DB::table('tags')->pluck('id')->toArray();

        for ($i = 0; $i < 20; $i++) {

            $contactId = DB::table('contacts')->insertGetId([
                'first_name' => $faker->lastName,
                'last_name' => $faker->firstName,
                'gender' => $faker->numberBetween(1, 3),
                'email' => $faker->unique()->safeEmail,
                'tel_1' => $faker->regexify('0[0-9]{2}'),
                'tel_2' => $faker->regexify('[0-9]{4}'),
                'tel_3' => $faker->regexify('[0-9]{4}'),
                'address' => $faker->prefecture . $faker->city . $faker->streetAddress,
                'building' => $faker->optional(0.7)->secondaryAddress,
                'category_id' => $faker->randomElement($categoryIds),
                'detail' => $faker->realText(100),
                'created_at' => now(),
                'updated_at' => now(),
            ]);


            $randomTagIds = $faker->randomElements($tagIds, $faker->numberBetween(0, 4));

            foreach ($randomTagIds as $tagId) {
                DB::table('contact_tags')->insert([
                    'contact_id' => $contactId,
                    'tag_id' => $tagId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
