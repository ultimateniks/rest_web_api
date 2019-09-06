<?php

namespace App\Http\Repository;

use App\Helpers\DistanceHelper;
use App\Distance;

class DistanceRepo
{
    /**
     * @var distanceHelper
     */
    protected $distanceHelper;

    /**
     * @var Distance
     */
    protected $distanceModel;

    /**
     * @param DistanceHelper $distanceHelper
     * @param Distance       $distanceModel
     */
    public function __construct(
        DistanceHelper $distanceHelper,
        Distance $distanceModel
    ) {
        $this->distanceHelper = $distanceHelper;
        $this->distanceModel = $distanceModel;
    }

    /**
     * save in distance table.
     *
     * @param string $startLatitude
     * @param string $startLongitude
     * @param string $endLatitude
     * @param string $endLongitude
     *
     * @return distance
     */
    public function create($startLatitude, $startLongitude, $endLatitude, $endLongitude)
    {
        //no existing distance found so get distance from google maps api
        $source = $startLatitude.','.$startLongitude;
        $destination = $endLatitude.','.$endLongitude;
        $distanceBetween = $this->distanceHelper->getDistance($source, $destination);

        if (!is_int($distanceBetween)) {
            return $distanceBetween;
        }

        return $this->distanceModel->saveDistance(
            $startLatitude,
            $startLongitude,
            $endLatitude,
            $endLongitude,
            $distanceBetween
        );
    }

    public function get($startLatitude, $startLongitude, $endLatitude, $endLongitude)
    {
        //check if distance already exists in distance table then use that
        $existingDistance = $this->distanceModel->getDistanceIfExists(
            $startLatitude,
            $startLongitude,
            $endLatitude,
            $endLongitude
        );

        if (!empty($existingDistance)) {
            return $existingDistance;
        }

        return false;
    }
}
