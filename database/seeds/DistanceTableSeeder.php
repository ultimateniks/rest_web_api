<?php

use Illuminate\Database\Seeder;
use App\Order;

class DistanceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locations = [
            ['startLat' => '26.905727', 'startLong' => '75.745567', 'endLat' => '26.906900', 'endLong' => '75.747912', 'distance' => 270,
            ],
            ['startLat' => '26.905727', 'startLong' => '75.745567', 'endLat' => '26.906900', 'endLong' => '75.747912', 'distance' => 270,
            ],
            ['startLat' => '26.905727', 'startLong' => '75.745567', 'endLat' => '26.906900', 'endLong' => '75.747912', 'distance' => 270,
            ],
        ];
        foreach ($locations as $disData) {
            DB::table('distance')->insert([
                'start_latitude' => $disData['startLat'],
                'start_longitude' => $disData['startLong'],
                'end_latitude' => $disData['endLat'],
                'end_longitude' => $disData['endLong'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'distance' => $disData['distance'],
            ]);
        }

        $faker = Faker\Factory::create();

        for ($i = 0; $i < 5; ++$i) {
            $lat1 = $faker->latitude();
            $lat2 = $faker->latitude();
            $lon1 = $faker->longitude();
            $lon2 = $faker->longitude();
            $distance = $this->distance($lat1, $lon1, $lat2, $lon2);

            DB::table('distance')->insert([
                'start_latitude' => $lat1,
                'start_longitude' => $lon1,
                'end_latitude' => $lat2,
                'end_longitude' => $lon2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'distance' => $distance,
            ]);
        }

        $distances = DB::table('distance')->orderBy('id')->each(function ($response) {
            for ($i = 0; $i < 5; ++$i) {
                DB::table('orders')->insert([
                    'distance_id' => $response->id,
                    'distance_value' => $response->distance,
                    'status' => 0 == $i % 2 ? Order::UNASSIGNED_ORDER_STATUS : Order::ASSIGNED_ORDER_STATUS,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        });
    }

    public function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $distanceInMetre = $dist * 60 * 1.1515 * 1.609344 * 1000;

        return $distanceInMetre;
    }
}
