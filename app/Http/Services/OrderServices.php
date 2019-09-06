<?php

namespace App\Http\Services;

use App\Validators\LocationCoordinatesValidator;
use Illuminate\Http\JsonResponse;

class OrderServices
{
    public $error = null;

    public $errorCode;

    protected $locationCoordinatesValidator;

    protected $DistanceRepo;

    protected $orderRepo;

    public function __construct(
        LocationCoordinatesValidator $locationCoordinatesValidator,
        \App\Http\Repository\orderRepo $orderRepo,
        \App\Http\Repository\DistanceRepo $DistanceRepo
    ) {
        $this->locationCoordinatesValidator = $locationCoordinatesValidator;
        $this->DistanceRepo = $DistanceRepo;
        $this->orderRepo = $orderRepo;
    }

    // create order using origin and destination points
    public function addOrder($requestData)
    {
        $startLat = $requestData->origin[0];
        $startLong = $requestData->origin[1];
        $endLat = $requestData->destination[0];
        $endLong = $requestData->destination[1];

        $validateLocationsCoordinates = $this->locationCoordinatesValidator
            ->validate($startLat, $startLong, $endLat, $endLong);

        if (!$validateLocationsCoordinates) {
            $this->error = $this->locationCoordinatesValidator->getError();
            $this->errorCode = JsonResponse::HTTP_BAD_REQUEST;

            return false;
        }

        //Fetching existing distance data
        $distance = $this->DistanceRepo->get($startLat, $startLong, $endLat, $endLong);

        if (false === $distance) {
            $distance = $this->DistanceRepo->create($startLat, $startLong, $endLat, $endLong);
        }

        if (!$distance instanceof \App\Distance) {
            $this->error = $distance;
            $this->errorCode = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;

            return false;
        }

        //Create new record
        $attributes = [
            'distance_id' => $distance->id,
            'distance' => $distance->distance,
        ];

        $order = $this->orderRepo->create($attributes);

        return $order;
    }

    // get list of orders
    public function getAllOrders($page, $limit)
    {
        $page = (int) $page;
        $limit = (int) $limit;
        $orders = [];

        if ($page > 0 && $limit > 0) {
            $skip = ($page - 1) * $limit;
            $orders = $this->orderRepo->getAllOrder($skip, $limit);
        }

        return $orders;
    }

    public function takeOrder($orderId)
    {
        return $this->orderRepo->takeOrder($orderId);
    }

    public function getOrderUsingId($id)
    {
        return $this->orderRepo->getOrderById($id);
    }
}
