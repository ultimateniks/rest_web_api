<?php

namespace Tests\Unit;

use Tests\TestCase;
use Faker\Factory as Faker;
use App\Http\Controllers\OrderController;
use App\Order;
use Illuminate\Http\JsonResponse;

class OrderTest extends TestCase
{
    protected static $allowedOrderStatus = [
        Order::UNASSIGNED_ORDER_STATUS,
        Order::ASSIGNED_ORDER_STATUS,
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = Faker::create();
        $this->orderServiceMock = \Mockery::mock(\App\Http\Services\OrderServices::class);
        $this->responseMock = $this->createResponseMock();

        $this->app->instance(
            OrderController::class,
            new OrderController(
                $this->orderServiceMock,
                $this->responseMock
            )
        );
    }

    public function tearDown(): void
    {
        \Mockery::close();
    }

    public function testAddOrderForValidData()
    {
        echo "\n Unit Test Cases running ---->\n\n\n";
        echo "\n Add valid Order ----> \n";

        $order = $this->generateTestOrder();

        $params = [
            'origin' => [$this->faker->latitude(), $this->faker->longitude()],
            'destination' => [$this->faker->latitude(), $this->faker->longitude()],
        ];

        //Order Service will return success
        $this->orderServiceMock
            ->shouldReceive('addOrder')
            ->once()
            ->andReturn($order);

        $response = $this->call('POST', '/orders', $params);
        $data = (array) $response->getData()->data;

        //print_r($response->getData()->data);

        $response->assertStatus(JsonResponse::HTTP_OK);
        $this->assertInternalType('array', $data);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('distance', $data);
    }

    public function testAddOrderForInvalidData()
    {
        echo "\n Add invalid Order ----> \n";

        $params = [
            'origin' => [$this->faker->latitude(), $this->faker->longitude()],
            'destination' => [$this->faker->latitude(), $this->faker->longitude()],
        ];

        //Order Service will return failure
        $this->orderServiceMock
            ->shouldReceive('addOrder')
            ->once()
            ->andReturn(false);

        $this->orderServiceMock->error = 'INVALID_PARAMETERS';
        $this->orderServiceMock->errorCode = JsonResponse::HTTP_BAD_REQUEST;

        $response = $this->call('POST', '/orders', $params);
        $data = (array) $response->getData();

        $response->assertStatus(JsonResponse::HTTP_BAD_REQUEST);
        $this->assertInternalType('array', $data);
        $this->assertArrayHasKey('error', $data);
    }

    public function testAddOrder_NegativeTestCase_Exception()
    {
        echo "\n Add Order Negative Test Case for Exception Handling ----> \n";

        $order = $this->generateTestOrder();

        $params = [
            'origin' => [strval($this->faker->latitude()), strval($this->faker->longitude())],
            'destination' => [strval($this->faker->latitude()), strval($this->faker->longitude())],
        ];

        //Order Service will return failure
        $this->orderServiceMock
            ->shouldReceive('addOrder')
            ->once()
            ->andThrow(
                new \InvalidArgumentException()
            );

        $this->orderServiceMock->error = 'Invalid_Argument_Exception';
        $this->orderServiceMock->errorCode = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;

        $response = $this->call('POST', '/orders', $params);
        $data = (array) $response->getData();
        $response->assertStatus(JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        $this->assertInternalType('array', $data);
        $this->assertArrayHasKey('error', $data);
    }

    public function testPatchOrderStatus_NegativeTestCase_Exception()
    {
        echo "\n Patch Order Status  for Negative Test Case using Exception Handling ----> \n";

        $id = $this->faker->randomDigit();

        $order = $this->generateTestOrder($id);

        $this->orderServiceMock
            ->shouldReceive('getOrderById')
            ->andThrow(
                new \InvalidArgumentException()
            );

        $params = ['status' => 'TAKEN'];

        $this->orderServiceMock->error = 'Invalid_Argument_Exception';
        $this->orderServiceMock->errorCode = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;

        $response = $this->call('PATCH', "/orders/{$id}", $params);
        $data = (array) $response->getData();

        $response->assertStatus(JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        $this->assertInternalType('array', $data);
        $this->assertArrayHasKey('error', $data);
    }

    public function testGetAllOrderForValidParams()
    {
        echo "\n\n\n\n Get All valid Orders ---->  \n";

        $page = 1;
        $limit = 5;

        $orderList = [];

        for ($i = 0; $i < 5; ++$i) {
            $orderList[] = $this->generateTestOrder();
        }

        $orderRecordCollection = new \Illuminate\Database\Eloquent\Collection($orderList);

        $this->orderServiceMock
            ->shouldReceive('getAllOrders')
            ->once()
            ->with($page, $limit)
            ->andReturn($orderRecordCollection);

        $params = ['page' => $page, 'limit' => $limit];

        $response = $this->call('GET', '/orders', $params);
        $data = $response->getData();

        $response->assertStatus(JsonResponse::HTTP_OK);

        $this->assertInternalType('array', $data);

        $this->assertArrayHasKey('id', (array) $data[0]);
        $this->assertArrayHasKey('distance', (array) $data[0]);
        $this->assertArrayHasKey('status', (array) $data[0]);
    }

    public function testGetAllOrderForBlankData()
    {
        echo "\n Get all orders using blank data ----> \n";

        $page = 1;
        $limit = 5;

        $orderRecordCollection = new \Illuminate\Database\Eloquent\Collection([]);

        $this->orderServiceMock
            ->shouldReceive('getAllOrders')
            ->once()
            ->andReturn($orderRecordCollection);

        $params = ['page' => $page, 'limit' => $limit];

        $response = $this->call('GET', '/orders', $params);

        $response->assertStatus(JsonResponse::HTTP_OK);
        $response->assertJson([]);
    }

    public function testGetAllOrder_NegativeTestCase_Exception()
    {
        echo "\n Get All orders for Negative Test Case using Exception Handling ----> \n";

        $page = 1;
        $limit = 7;

        $this->orderServiceMock
            ->shouldReceive('getAllOrders')
            ->with($page, $limit)
            ->andThrow(
                new \InvalidArgumentException()
            );

        $params = ['page' => $page, 'limit' => $limit];

        $this->orderServiceMock->error = 'Invalid_Argument_Exception';
        $this->orderServiceMock->errorCode = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;

        $response = $this->call('GET', '/orders', $params);
        $data = (array) $response->getData();

        $response->assertStatus(JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        $this->assertInternalType('array', $data);
        $this->assertArrayHasKey('error', $data);
    }

    /**
     * @param int|null $id
     *
     * @return Order
     */
    private function generateTestOrder($id = null)
    {
        $id = $id ?: $this->faker->randomDigit();

        $order = new Order();
        $order->id = $id;
        $order->status = $this->faker->randomElement(self::$allowedOrderStatus);
        $order->distance_id = $this->faker->randomDigit();
        $order->distance_value = $this->faker->numberBetween(1000, 9999);
        $order->created_at = $this->faker->dateTimeBetween();
        $order->updated_at = $this->faker->dateTimeBetween();

        return $order;
    }

    /**
     * @return Mockery_2_App_Http_Response_Response
     */
    private function createResponseMock()
    {
        $messageHelperMock = \Mockery::mock('\App\Helpers\MessageHelper')->makePartial();

        $responseMock = \Mockery::mock('\App\Http\Response\ResponseHelper[formatOrderAsResponse]', [$messageHelperMock]);

        $responseMock
            ->shouldReceive('formatOrderAsResponse')
            ->andReturnUsing(function ($argument) {
                return [
                    'id' => $argument->id,
                    'status' => $argument->status,
                    'distance' => $argument->distance_value,
                ];
            });

        return $responseMock;
    }
}
