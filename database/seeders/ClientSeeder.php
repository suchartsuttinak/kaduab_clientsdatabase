<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\Problem;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        // ดึง problem ทั้งหมด
        $problemIds = Problem::pluck('id')->toArray();

        Client::factory()
            ->count(100)
            ->create()
            ->each(function ($client) use ($problemIds) {

                /*
                ---------------------------------
                สุ่มปัญหาให้แต่ละ client 1-4 ปัญหา
                ---------------------------------
                */

                if(!empty($problemIds)){

                    $randomProblems = collect($problemIds)
                        ->random(
                           rand(1,min(4,count($problemIds)))
                        )
                        ->toArray();

                    $client->problems()->sync($randomProblems);
                }

            });
    }
}