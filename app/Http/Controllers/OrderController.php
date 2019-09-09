<?php

namespace App\Http\Controllers;

use App\Http\Services\OrderServices;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\AddOrderRequest;
use App\Http\Response\ResponseHelper;
use App\Http\Requests\OrderStatusRequest;
use App\Http\Requests\OrderListRequest;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    protected $responseHelper;
    protected $orderServices;

    public function __construct(OrderServices $orderServices, ResponseHelper $responseHelper)
    {
        $this->orderServices = $orderServices;
        $this->responseHelper = $responseHelper;
    }

    // store order data
    public function store(AddOrderRequest $request)
    {
        try {
            $order = $this->orderServices->addOrder($request);

            if ($order instanceof \App\Order) {
                return $this->responseHelper->formatOrderAsResponse($order);
            } else {
                $messages = $this->orderServices->error;
                $errorCode = $this->orderServices->errorCode;
                Log::error('Add Order error-'.$messages.$errorCode);

                return $this->responseHelper->sendErrorResponse($messages, $errorCode);
            }
        } catch (\Exception $e) {
            Log::error('Add Order error -'.$e->getMessage());

            return $this->responseHelper->sendErrorResponse($e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // get list of all orders
    public function getAllOrder(OrderListRequest $request)
    {
        try {
            Log::info('Request: '.json_encode($request->all()));
            $page = (int) $request->get('page', 1);
            $limit = (int) $request->get('limit', 1);

            $records = $this->orderServices->getAllOrders($page, $limit);

            $allOrders = [];
            foreach ($records as $record) {
                $allOrders[] = $this->responseHelper->formatOrderAsResponse($record);
            }

            return $this->responseHelper->setSuccessResponse($allOrders);
        } catch (\Exception $e) {
            Log::error('Error in Order List'.$e->getMessage());

            return $this->responseHelper->sendErrorResponse($e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // update the status of orders
    public function patchOrderStatus(OrderStatusRequest $request, $id)
    {
        try {
            $order = $this->orderServices->getOrderUsingId($id);
            if (false === $order) {
                Log::error('Order Status error '.'- order_not_found in Order Status');

                return $this->responseHelper->sendErrorResponse('order_not_found', JsonResponse::HTTP_NOT_FOUND);
            }

            if (false === $this->orderServices->takeOrder($id)) {
                Log::error('Order Status error '.'- order_already_taken in Order Status');

                return $this->responseHelper->sendErrorResponse('order_already_taken', JsonResponse::HTTP_CONFLICT);
            }

            return $this->responseHelper->sendSuccessResponse('success', JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Order Status error with id :-'.$id.$e->getMessage());

            return $this->responseHelper->sendErrorResponse($e->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
