<?php

namespace App\Tests\Api;

use Symfony\Component\HttpFoundation\Response;

class CartsResourceTest extends ApiTestBase
{
    public function testList(): void
    {
        [$statusCode, $results] = $this->handleJsonCall('/carts/');
        $this->assertSame(Response::HTTP_OK, $statusCode);
        $this->assertEmpty($results);

        [$statusCode, $result] = $this->handleJsonCall('/carts/', 'POST', ['comment' => 'New cart']);
        $this->assertSame(Response::HTTP_CREATED, $statusCode);
        $this->assertEquals('New cart', $result->comment);

        [$statusCode, $results] = $this->handleJsonCall('/carts/');
        $this->assertSame(Response::HTTP_OK, $statusCode);
        $this->assertNotEmpty($results);
        $this->assertEquals($result, $results[0]);
    }

    /**
     * @note Update operation is not yet implemented.
     *
     * @dataProvider provideProductData
     */
    public function testCrud(array $entityData)
    {
        [$statusCode, $result] = $this->handleJsonCall('/carts/', 'POST', $entityData);
        $resultData = (array)$result;
        $this->assertSame(Response::HTTP_CREATED, $statusCode);
        $this->assertEquals($resultData, $entityData + $resultData);

        [$statusCode, $resultGet] = $this->handleJsonCall('/carts/' . $result->id, 'GET');
        $this->assertSame(Response::HTTP_OK, $statusCode);
        $this->assertEquals($resultGet, $result);

        [$statusCode, $result] = $this->handleJsonCall('/carts/' . $resultGet->id, 'DELETE');
        $this->assertSame(Response::HTTP_NO_CONTENT, $statusCode);
        $this->assertSame(null, $result);

        [$statusCode, $result] = $this->handleJsonCall('/carts/' . $resultGet->id, 'GET');
        $this->assertSame(Response::HTTP_NOT_FOUND, $statusCode);
        $this->assertSame(null, $result);
    }

    /**
     * Provides data for ::testCrud.
     */
    public function provideProductData(): array
    {
        return [
            'empty cart' => [
                [],
            ],
            'cart with comment' => [
                ['comment' => '⭐ Sur&shy;prise! 😉'],
            ],
            // @todo invalid cart id.
            /* 'cart with product' => [
                [
                    'positions' => [
                        ['product_id' => 1, 'quantity' => 3],
                        ['product_id' => 2],
                        ['product_id' => 1, 'quantity' => 0]
                    ],
                ],
            ], */
        ];
    }
}
