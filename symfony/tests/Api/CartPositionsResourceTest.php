<?php

namespace App\Tests\Api;

use Symfony\Component\HttpFoundation\Response;

class CartPositionsResourceTest extends ApiTestBase
{
    protected int $idCart;
    protected int $idProduct;

    public function setUp(): void
    {
        [, $result] = $this->handleJsonCall('/products/', 'POST', ['name' => 'Dummy product']);
        $this->idProduct = $result->id;

        [, $result] = $this->handleJsonCall('/carts/', 'POST', ['comment' => 'Dummy cart']);
        $this->idCart = $result->id;
    }

    public function testList(): void
    {
        [$statusCode, $results] = $this->handleJsonCall('/carts/' . $this->idCart . '/positions/');
        $this->assertSame(Response::HTTP_OK, $statusCode);
        $this->assertEmpty($results);

        [$statusCode, $result] = $this->handleJsonCall('/carts/' . $this->idCart . '/positions/', 'POST', [
            'product' => $this->idProduct,
        ]);
        $this->assertSame(Response::HTTP_CREATED, $statusCode);
        $this->assertEquals($this->idProduct, $result->product->id);
        $this->assertSame(1, $result->quantity);

        [$statusCode, $results] = $this->handleJsonCall('/carts/' . $this->idCart . '/positions/');
        $this->assertSame(Response::HTTP_OK, $statusCode);
        $this->assertNotEmpty($results);
        $this->assertEquals($result, $results[0]);
    }

    public function testCrud()
    {
        $entityData = ['product' => $this->idProduct, 'quantity' => 3];
        $endpoint = '/carts/' . $this->idCart . '/positions/';

        [$statusCode, $result] = $this->handleJsonCall($endpoint, 'POST', $entityData);
        $this->assertSame(Response::HTTP_CREATED, $statusCode);
        $this->assertSame($entityData['product'], $result->product->id);
        $this->assertSame($entityData['quantity'], $result->quantity);

        [$statusCode, $resultGet] = $this->handleJsonCall($endpoint . $result->id, 'GET');
        $this->assertSame(Response::HTTP_OK, $statusCode);
        $this->assertEquals($resultGet, $result);

        $updatedEntityData = [
            'quantity' => 1337,
        ] + $entityData;
        [$statusCode, $result] = $this->handleJsonCall($endpoint . $result->id, 'PUT', $updatedEntityData);
        $this->assertSame(Response::HTTP_OK, $statusCode);
        $this->assertSame($resultGet->id, $result->id);
        $this->assertEquals(1337, $result->quantity);

        [$statusCode, $result] = $this->handleJsonCall($endpoint . $result->id, 'DELETE');
        $this->assertSame(Response::HTTP_NO_CONTENT, $statusCode);
        $this->assertSame(null, $result);

        [$statusCode, $resultGet] = $this->handleJsonCall($endpoint . $resultGet->id, 'GET');
        $this->assertSame(Response::HTTP_NOT_FOUND, $statusCode);
    }
}
