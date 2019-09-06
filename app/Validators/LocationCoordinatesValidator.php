<?php

namespace App\Validators;

class LocationCoordinatesValidator
{
    const LATITUDE_RANGE_LOWER_LIMIT = -90;
    const LATITUDE_RANGE_UPPER_LIMIT = 90;
    const LONGITUDE_RANGE_LOWER_LIMIT = -180;
    const LONGITUDE_RANGE_UPPER_LIMIT = 180;

  
    protected $error;

    public function getError()
    {
        return $this->error;
    }

    
    public function validate($startLat, $startLong, $endLat, $endLong)
    {
        if ($startLat == $endLat && $startLong == $endLong) {
            $this->error = 'REQUESTED_ORIGIN_DESTINATION_SAME';
        } elseif (!$startLat || !$startLong || !$endLat || !$endLong) {
            $this->error = 'REQUEST_PARAMETER_MISSING';
        } elseif ($startLat < self::LATITUDE_RANGE_LOWER_LIMIT
            || $startLat > self::LATITUDE_RANGE_UPPER_LIMIT
            || $endLat < self::LATITUDE_RANGE_LOWER_LIMIT
            || $endLat > self::LATITUDE_RANGE_UPPER_LIMIT
        ) {
            $this->error = 'LATITUDE_OUT_OF_RANGE';
        } elseif ($startLong < self::LONGITUDE_RANGE_LOWER_LIMIT
            || $startLong > self::LONGITUDE_RANGE_UPPER_LIMIT
            || $endLong < self::LONGITUDE_RANGE_LOWER_LIMIT
            || $endLong > self::LONGITUDE_RANGE_UPPER_LIMIT
        ) {
            $this->error = 'LONGITUDE_OUT_OF_RANGE';
        } elseif (!is_numeric($startLat)
            || !is_numeric($endLat)
            || !is_numeric($startLong)
            || !is_numeric($endLong)
        ) {
            $this->error = 'INVALID_PARAMETERS';
        }

        return $this->error ? false : true;
    }
}
