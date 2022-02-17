<?php

namespace Database\Seeders;

use App\Models\Teacher;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();
        foreach (range(1, 100) as $item) {
            $lastId = Teacher::latest()->first();
            Teacher::create([
                'create_by'     => 1,
                'name'          => $faker->unique()->name,
                'gender'        => gender(true),
                'religion'      => religion(true),
                'phone'         => random_phone(),
                'email'         => $faker->unique()->email,
                'address'       => $faker->address,
                'date_of_birth' => date('Y-m-d', strtotime('-18 years')),
                'join_date'     => date('Y-m-d'),
                'photo'         => $faker->imageUrl,
                'username'      => $faker->unique()->userName,
                'password'      => bcrypt($faker->password),
                'status'        => random_status(),
                'uu_id'         => str_pad($item, 6, 0, STR_PAD_LEFT)
            ]);
        }
    }
}
