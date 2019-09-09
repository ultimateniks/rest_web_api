<?php

namespace App\Test\Feature\ApiController;

use Tests\TestCase;
use Illuminate\Http\JsonResponse;

class OrderIntegrationTest extends TestCase
{
    public function testOrderCreateWithPositiveCase()
    {
        echo "\n  Running Order Test Cases ----> \n";
        echo "\n  Create Order Positive Test Case for Valid Input \n";

        $validData = [
            'origin' => ['26.905727', '75.745567'],
            'destination' => ['26.906900', '75.747912'],
        ];

        $response = $this->json('POST', '/orders', $validData);
        $data = (array) $response->getData();
        //print_r($data);
        echo "\n\t > should got Status: 200 \n";
        $response->assertStatus(JsonResponse::HTTP_OK);
    }

    public function testOrderCreateWithEmptyParameters()
    {
        echo "\n > Negative Test Cases  \n (A)- With Empty Parameters - Should get 400 \n";

        $invalidData = [
            'origin' => ['', '75.745567'],
            'destination' => ['26.906900', '75.747912'],
        ];

        $response = $this->json('POST', '/orders', $invalidData);

        $response->assertStatus(JsonResponse::HTTP_BAD_REQUEST);
    }

    public function testOrderCreateWithInValidParameters()
    {
        echo "\n > (B)- With Invalid Parameter Keys - Should get 400 \n";

        $invalidData = [
            'origin1' => ['75.745567', '26.905727'],
            'destination' => ['75.747912', '26.906900'],
        ];

        $response = $this->json('POST', '/orders', $invalidData);

        $response->assertStatus(JsonResponse::HTTP_BAD_REQUEST);
    }

    public function testOrderCreateForInCorrectParameters()
    {
        echo "\n > (C) - With Additional Parameter  - should get 400 \n";
        $invalidData = [
            'origin' => ['26.905727', '43.958046', '75.745567'],
            'destination' => ['26.906900', '43.958046', '75.747912'],
        ];

        $response = $this->json('POST', '/orders', $invalidData);

        $response->assertStatus(JsonResponse::HTTP_BAD_REQUEST);
    }

    public function testOrderCreateWithInvalidData()
    {
        echo "\n > (D) - With Invalid Parameter Data - should get 400 \n";
        $invalidData = [
            'origin' => ['test', '43.958046'],
            'destination' => ['26.906900', '75.747912'],
        ];

        $response = $this->json('POST', '/orders', $invalidData);

        $response->assertStatus(JsonResponse::HTTP_BAD_REQUEST);
    }

    public function testGetAllOrderSuccessCount()
    {
        echo "\n\n\n Running Get All Orders Test Case Scenario (Positive and Negative) ----> \n";

        echo "\n > Executing Get All Orders Positive Test Case - Valid Data Count(page=1&limit=4) \n";

        $query = 'page=1&limit=4';
        $response = $this->json('GET', "/orders?$query", []);
        $data = (array) $response->getData();

        echo "\n > Get All Orders Positive Test - Should get status as 200  \n";
        $response->assertStatus(JsonResponse::HTTP_OK);

        echo "\n > Get All Orders Positive Test - count of data should less than or equal to 4  \n";
        $this->assertLessThan(5, count($data));
    }

    public function testGetAllOrderSuccessCases()
    {
        echo "\n > Get All Orders Positive Test - Valid Data Keys (page=1&limit=3)\n";

        $query = 'page=2&limit=3';
        $response = $this->json('GET', "/orders?$query", []);
        $data = (array) $response->getData();

        echo "\n\t > Status should be 200\n";
        $response->assertStatus(JsonResponse::HTTP_OK);

        echo "\n\t > Response should contain id, distance and status key\n";
        foreach ($data as $order) {
            $order = (array) $order;
            $this->assertArrayHasKey('id', $order);
            $this->assertArrayHasKey('distance', $order);
            $this->assertArrayHasKey('status', $order);
        }
    }

    public function testGetAllOrderFailureCases()
    {
        echo "\n > Get All Orders Negative Tests \n (A) - Invalid Params (page1) \n";
        $query = 'page1=1&limit=2';
        $this->orderListFailure($query, JsonResponse::HTTP_BAD_REQUEST);

        echo "\n > (B) - Invalid Params (limit1) \n";
        $query = 'page=1&limit1=3';
        $this->orderListFailure($query, JsonResponse::HTTP_BAD_REQUEST);

        echo "\n > (C) - Invalid Params Value (page = 0) \n";
        $query = 'page=0&limit=5';
        $this->orderListFailure($query, JsonResponse::HTTP_BAD_REQUEST);

        echo "\n > (D) - Invalid Params Value (limit = 0) \n";
        $query = 'page=1&limit=0';
        $this->orderListFailure($query, JsonResponse::HTTP_BAD_REQUEST);

        echo "\n > (E) - Invalid Params Value (limit = -1) \n";
        $query = 'page=1&limit=0';
        $this->orderListFailure($query, JsonResponse::HTTP_BAD_REQUEST);
    }

    protected function orderListFailure($query, $expectedCode)
    {
        $response = $this->json('GET', "/orders?$query", []);
        $data = (array) $response->getData();

        echo "\n \t > Get All Orders Negative Test - response should has status $expectedCode \n";
        $response->assertStatus($expectedCode);

        echo "\n \t > Get All Orders Negative Test  - response should has key `error` \n";
        $this->assertArrayHasKey('error', $data);
    }

    public function testPatchOrderStatusCases()
    {
        echo "\n \n \n   Running Patch Order Status Positive Test Case Scenario ----> \n";
        echo "\n \t > Create new Order to test Patch Order Status \n";
        $validData = [
            'origin' => ['26.905727', '75.745567'],
            'destination' => ['26.906900', '75.747912'],
        ];

        $updateData = ['status' => 'TAKEN'];
        $response = $this->json('POST', '/orders', $validData);
        $data = (array) $response->getData();
        //print_r($data);
        $orderId = $data['id'];

        // echo "\n > Order has been created with id : ".$orderId;

        echo "\n \t > Updating Order.........  \n";
        $response = $this->json('PATCH', '/orders/'.$orderId, $updateData);
        $data = (array) $response->getData();

        echo "\n > Patch Order Status - should have status 200 \n";
        $response->assertStatus(JsonResponse::HTTP_OK);

        echo "\n > Patch Order Status - response has key as `status` \n";
        $this->assertArrayHasKey('status', $data);

        echo "\n > Patch Order Status Negative Test - For already updated order \n";

        $updateData = ['status' => 'TAKEN'];

        $response = $this->json('PATCH', '/orders/'.$orderId, $updateData);
        $data = (array) $response->getData();

        echo "\n \t > Trying to update same order - should has status 409";
        $response->assertStatus(JsonResponse::HTTP_CONFLICT);

        echo "\n \t > Trying to update same order - response should has key `error` \n";
        $this->assertArrayHasKey('error', $data);

        echo "\n > Patch Order Status Negative Test Cases (A)- Invalid Params key (status1) \n";
        $this->updatePatchOrderFailureInvalidParams($orderId, ['status1' => 'TAKEN'], $expectedCode = JsonResponse::HTTP_BAD_REQUEST);

        echo "\n > (B) - Empty Param value \n";
        $this->updatePatchOrderFailureInvalidParams($orderId, ['status' => ''], $expectedCode = JsonResponse::HTTP_BAD_REQUEST);

        echo "\n > (C) - Non numeric order id \n";
        $this->updatePatchOrderFailureInvalidParams('22d3', ['status' => 'TAKEN'], $expectedCode = JsonResponse::HTTP_BAD_REQUEST);

        echo "\n > (D) - Not existing order id \n";
        $this->updatePatchOrderFailureInvalidParams('379873', ['status' => 'TAKEN'], $expectedCode = JsonResponse::HTTP_NOT_FOUND);
    }

    protected function updatePatchOrderFailureInvalidParams($orderId, $params, $expectedCode)
    {
        $response = $this->json('PATCH', '/orders/'.$orderId, $params);
        $data = (array) $response->getData();

        echo "\n \t > Trying to update Invalid Order - response should has status $expectedCode \n";
        $response->assertStatus($expectedCode);

        echo "\n \t > Trying to update Invalid Order - response should has key `error` \n";
        $this->assertArrayHasKey('error', $data);
    }
}
