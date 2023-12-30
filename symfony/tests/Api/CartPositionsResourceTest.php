<?php

namespace App\Tests\Api;

use Symfony\Component\HttpFoundation\Response;

class CartPositionsResourceTest extends ApiTestBase
{
    protected int $idCart;
    protected int $idCartEmpty;
    protected int $idProduct;

    public function setUp(): void
    {
        parent::setUp();

        [, $results] = $this->apiClient->handleJsonCall('/products/', 'GET', [
            'name' => 'Boat',
            'limit' => 1,
        ]);
        $this->idProduct = $results[0]->id;

        [, $results] = $this->apiClient->handleJsonCall('/carts/', 'GET', [
            'comment' => 'Cart for testing',
            'limit' => 1,
        ]);
        $this->idCart = $results[0]->id;

        [, $results] = $this->apiClient->handleJsonCall('/carts/', 'GET', [
            'comment' => 'Empty cart',
            'limit' => 1,
        ]);
        $this->idCartEmpty = $results[0]->id;
    }

    public function testList(): void
    {
        [$statusCode, $results] = $this->apiClient->handleJsonCall('/carts/' . $this->idCart . '/positions/', 'GET', [
            'limit' => 0,
        ]);
        $this->assertSame(Response::HTTP_OK, $statusCode);
        $this->assertEmpty($results);

        [$statusCode, $result] = $this->apiClient->handleJsonCall('/carts/' . $this->idCart . '/positions/', 'POST', [
            'product' => $this->idProduct,
        ]);
        $this->assertSame(Response::HTTP_CREATED, $statusCode);
        $this->assertEquals($this->idProduct, $result->product->id);
        $this->assertSame(1, $result->quantity);

        [$statusCode, $results] = $this->apiClient->handleJsonCall('/carts/' . $this->idCart . '/positions/');
        $this->assertSame(Response::HTTP_OK, $statusCode);
        $this->assertNotEmpty($results);
        $this->assertEquals($result, end($results));

        [$statusCode, $result] = $this->apiClient->handleJsonCall(
            '/carts/' . $this->idCart . '/positions/' . $result->id,
            'DELETE'
        );
        $this->assertSame(Response::HTTP_NO_CONTENT, $statusCode);
    }

    public function testCrud(): void
    {
        $entityData = ['product' => $this->idProduct, 'quantity' => 3];
        $endpoint = '/carts/' . $this->idCart . '/positions/';

        [$statusCode, $result] = $this->apiClient->handleJsonCall($endpoint, 'POST', $entityData);
        $this->assertSame(Response::HTTP_CREATED, $statusCode);
        $this->assertSame($entityData['product'], $result->product->id);
        $this->assertSame($entityData['quantity'], $result->quantity);

        [$statusCode, $resultGet] = $this->apiClient->handleJsonCall($endpoint . $result->id, 'GET');
        $this->assertSame(Response::HTTP_OK, $statusCode);
        $this->assertEquals($resultGet, $result);

        $updatedEntityData = [
            'quantity' => 1337,
        ] + $entityData;
        [$statusCode, $result] = $this->apiClient->handleJsonCall($endpoint . $result->id, 'PUT', $updatedEntityData);
        $this->assertSame(Response::HTTP_OK, $statusCode);
        $this->assertSame($resultGet->id, $result->id);
        $this->assertEquals(1337, $result->quantity);

        [$statusCode, $result] = $this->apiClient->handleJsonCall($endpoint . $result->id, 'DELETE');
        $this->assertSame(Response::HTTP_NO_CONTENT, $statusCode);
        $this->assertNull($result);

        [$statusCode, $resultGet] = $this->apiClient->handleJsonCall($endpoint . $resultGet->id, 'GET');
        $this->assertSame(Response::HTTP_NOT_FOUND, $statusCode);
    }

    public function testClear(): void
    {
        [$statusCode, $result] = $this->apiClient->handleJsonCall('/carts/' . $this->idCart . '/positions/', 'GET');
        $this->assertSame(Response::HTTP_OK, $statusCode);
        $this->assertNotEmpty($result);

        [$statusCode, $result] = $this->apiClient->handleJsonCall('/carts/' . $this->idCart . '/positions/', 'DELETE');
        $this->assertSame(Response::HTTP_NO_CONTENT, $statusCode);
        $this->assertNull($result);
    }
}
