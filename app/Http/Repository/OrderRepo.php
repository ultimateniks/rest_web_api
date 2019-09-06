<?php

namespace App\Http\Repository;

use App\Order;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderRepo
{
    /**
     *
     * @var Order
     */
    protected $orderModel;

    /**
     *
     * @param Order $orderModel
     */
    public function __construct(Order $orderModel)
    {
        $this->orderModel = $orderModel;
    }


    public function create($attributes)
    {
        $this->orderModel->status = $attributes['status'] ?? Order::UNASSIGNED_ORDER_STATUS;
        $this->orderModel->distance_id = $attributes['distance_id'];
        $this->orderModel->distance_value = $attributes['distance'];
        $this->orderModel->save();

        return $this->orderModel;
    }

    /**
    * Fetches a order model using its primary key
    *
    * @param int $id
    *
    * @return self|false
    */
    public function getOrderById($id)
    {
        try {
            return $this->orderModel->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }

    /**
     * Update order status from UNASSIGNED to TAKEN if order is not already taken
     *
     * @param int $orderId
     *
     * @return bool
     */
    public function takeOrder($orderId)
    {
        $affectedRows = $this->orderModel->where([
            ['id', '=', $orderId],
            ['status', '=', Order::UNASSIGNED_ORDER_STATUS],
        ])
        ->update(['orders.status' => Order::ASSIGNED_ORDER_STATUS]);

        return $affectedRows > 0 ? true : false;
    }

    /**
     * to get order list
     *
     * @param int $skip
     * @param int $limit
     * @return array
     */
    public function getAllOrder($skip, $limit)
    {
        return $this->orderModel->skip($skip)->take($limit)->orderBy('id', 'asc')->get();
    }
}
