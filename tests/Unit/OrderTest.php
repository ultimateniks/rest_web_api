<?php

namespace Tests\Unit;

use Tests\TestCase;
use Faker\Factory as Faker;
use App\Http\Controllers\OrderController;
use App\Order;
use Illuminate\Http\JsonResponse;
use App\Http\Services\OrderServices;
use App\Distance;

class OrderTest extends TestCase
{
    protected static $allowedOrderStatus = [
        Order::UNASSIGNED_ORDER_STATUS,
        Order::ASSIGNED_ORDER_STATUS,
    ];
    protected $locationCoordsValidatorMock;
    protected $orderRepoMock;
    protected $distRepoMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = Faker::create();
        $this->orderServiceMock = \Mockery::mock(OrderServices::class);
        $this->responseMock = $this->createResponseMock();
        $this->locationCoordsValidatorMock = $this->createMock(\App\Validators\LocationCoordinatesValidator::class);
        $this->orderRepoMock = \Mockery::mock(\App\Http\Repository\OrderRepo::class);
        $this->distRepoMock = \Mockery::mock(\App\Http\Repository\DistanceRepo::class);

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
        echo "\n\n\n Add valid Order ----> \n";

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

        $data = (array) $response->getData();
        // print_r($data);
        //print_r($response->getData()->data);

        $response->assertStatus(JsonResponse::HTTP_OK);
        $this->assertInternalType('array', $data);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('distance', $data);
    }

    public function testAddOrderForValidCoordinates()
    {
        echo "\n Add order for valid Coordinates  ----> \n";

        $order = $this->generateTestOrder();
        $distance = $this->generateTestDisatnceObj();
        $distCoords = $this->generateTestCords();

        $origin = implode(',', $distCoords['origin']);
        $destination = implode(',', $distCoords['destination']);

        //Mock dependencies
        $this->locationCoordsValidatorMock->method('validate')->with(
            $distCoords['origin'][0],
            $distCoords['origin'][1],
            $distCoords['destination'][0],
            $distCoords['destination'][1]
        )->willReturn(true);

        $this->distRepoMock->shouldReceive('get')->andReturn($distance);

        $this->orderRepoMock->shouldReceive('create')->andReturn($order);

        $orderServices = new OrderServices(
            $this->locationCoordsValidatorMock,
            $this->orderRepoMock,
            $this->distRepoMock
        );

        $this->assertInstanceOf('App\Order', $orderServices->addOrder((object) $distCoords));
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

    public function testAddOrderForInvalidCoordinates()
    {
        echo "\n Add order for Invalid Coordinates  ----> \n";

        $order = $this->generateTestOrder();
        $distance = $this->generateTestDisatnceObj();
        $distCoords = $this->generateTestCords();

        $origin = implode(',', $distCoords['origin']);
        $destination = implode(',', $distCoords['destination']);

        $this->locationCoordsValidatorMock->method('validate')->with(
            $distCoords['origin'][0],
            $distCoords['origin'][1],
            $distCoords['destination'][0],
            $distCoords['destination'][1]
        )->willReturn(false);

        $this->distRepoMock->shouldReceive('get')->andReturn($distance);
        $this->distRepoMock->shouldReceive('create')->andReturn($order);

        $orderServices = new OrderServices(
            $this->locationCoordsValidatorMock,
            $this->orderRepoMock,
            $this->distRepoMock
        );

        $this->assertEquals(false, $orderServices->addOrder((object) $distCoords));
    }

    public function testAddDistanceForValidCoordinates()
    {
        echo "\n create order using new distance coordinates -----> \n";

        $order = $this->generateTestOrder();
        $distance = $this->generateTestDisatnceObj();
        $distCoords = $this->generateTestCords();

        $origin = implode(',', $distCoords['origin']);
        $destination = implode(',', $distCoords['destination']);

        $this->locationCoordsValidatorMock->method('validate')->with(
            $distCoords['origin'][0],
            $distCoords['origin'][1],
            $distCoords['destination'][0],
            $distCoords['destination'][1]
        )->willReturn(true);
        $this->distRepoMock->shouldReceive('get')->andReturn(false);
        $this->distRepoMock->shouldReceive('create')->andReturn($distance);
        $this->orderRepoMock->shouldReceive('create')->andReturn($order);

        $orderService = new OrderServices(
            $this->locationCoordsValidatorMock,
            $this->orderRepoMock,
            $this->distRepoMock
        );

        $this->assertInstanceOf('App\Order', $orderService->addOrder((object) $distCoords));
    }

    public function testAddDistanceForInValidData()
    {
        echo "\n  create order using new distance for invalid Response -----> \n";

        $order = $this->generateTestOrder();
        $distance = $this->generateTestDisatnceObj();
        $distCoords = $this->generateTestCords();

        $origin = implode(',', $distCoords['origin']);
        $destination = implode(',', $distCoords['destination']);

        //Mock dependencies
        $this->locationCoordsValidatorMock->method('validate')->with(
            $distCoords['origin'][0],
            $distCoords['origin'][1],
            $distCoords['destination'][0],
            $distCoords['destination'][1]
        )->willReturn(true);
        $this->distRepoMock->shouldReceive('get')->andReturn(false);
        $this->distRepoMock->shouldReceive('create')->andReturn(false);
        $this->orderRepoMock->shouldReceive('create')->andReturn($order);

        $orderServices = new OrderServices(
            $this->locationCoordsValidatorMock,
            $this->orderRepoMock,
            $this->distRepoMock
        );

        $this->assertEquals(false, $orderServices->addOrder((object) $distCoords));
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

    public function testgetOrderUsingId()
    {
        echo "\n Get Order using valid order id -----> \n";
        $orderServices = new OrderServices(
            $this->locationCoordsValidatorMock,
            $this->orderRepoMock,
            $this->distRepoMock
        );

        $id = $this->faker->randomDigit();
        $order = $this->generateTestOrder();

        $this->orderRepoMock->shouldReceive('getOrderById')->with($id)->andReturn($order);

        $response = $orderServices->getOrderUsingId($id);

        echo "\n \t Response should be intance of Order Model .. \n";
        $this->assertInstanceOf('\App\Order', $response);
    }

    public function testtakeOrder()
    {
        echo "\n Take Order using valid order id -----> \n";
        $orderServices = new OrderServices(
            $this->locationCoordsValidatorMock,
            $this->orderRepoMock,
            $this->distRepoMock
        );

        $id = $this->faker->randomDigit();
        $order = $this->generateTestOrder();

        $this->orderRepoMock->shouldReceive('takeOrder')->with($id)->andReturn(true);

        $response = $orderServices->takeOrder($id);

        echo "\n \t should be boolean Type response \n";
        $this->assertEquals(true, $response);
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

    private function generateTestDisatnceObj()
    {
        $id = $this->faker->randomDigit();

        $distObject = new Distance();
        $distObject->id = $id;
        $distObject->start_latitude = $this->faker->latitude();
        $distObject->start_longitude = $this->faker->longitude();
        $distObject->end_latitude = $this->faker->latitude();
        $distObject->end_longitude = $this->faker->longitude();
        $distObject->distance = $this->faker->numberBetween(1000, 5000);

        return $distObject;
    }

    protected function generateTestCords()
    {
        $faker = Faker::create();

        $startLat = $faker->latitude();
        $startLong = $faker->latitude();
        $endLat = $faker->longitude();
        $endLong = $faker->longitude();

        $distance = $this->distance($startLat, $startLong, $endLat, $endLong);

        return [
            'origin' => [$startLat, $startLong],
            'destination' => [$endLat, $endLong],
            'distance' => $distance,
        ];
    }

    public function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $distInMetre = $dist * 60 * 1.1515 * 1.609344 * 1000;

        return (int) $distInMetre;
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
