<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Distance extends Model
{
    protected $table = 'distance';

    public function getDistanceIfExists($startLat, $startLong, $endLat, $endLong)
    {
        $distance = self::where([
            ['start_latitude', '=', $startLat],
            ['start_longitude', '=', $startLong],
            ['end_latitude', '=', $endLat],
            ['end_longitude', '=', $endLong],
        ])->first();

        return $distance;
    }

    public function saveDistance($startLat, $startLong, $endLat, $endLong, $distanceBetween)
    {
        $distance = new Distance;
        $distance->start_latitude = $startLat;
        $distance->start_longitude = $startLong;
        $distance->end_latitude = $endLat;
        $distance->end_longitude = $endLong;
        $distance->distance = $distanceBetween;
        $distance->save();

        return $distance;
    }
}
