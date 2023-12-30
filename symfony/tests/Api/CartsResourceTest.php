<?php

namespace App\Tests\Api;

use Symfony\Component\HttpFoundation\Response;

class CartsResourceTest extends ApiTestBase
{
    public function testList(): void
    {
        [$statusCode, $results] = $this->handleJsonCall('/carts/', 'GET', [
            'comment' => 'These are not the carts you are looking for.',
        ]);
        $this->assertSame(Response::HTTP_OK, $statusCode);
        $this->assertEmpty($results);

        [$statusCode, $result] = $this->handleJsonCall('/carts/', 'POST', ['comment' => 'New cart']);
        $this->assertSame(Response::HTTP_CREATED, $statusCode);
        $this->assertEquals('New cart', $result->comment);

        [$statusCode, $results] = $this->handleJsonCall('/carts/', 'GET', [
            'comment' => 'New cart',
        ]);
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
        $this->assertSame(Response::HTTP_CREATED, $statusCode);
        $this->assertEquals(count($entityData['positions'] ?? []), count($result->positions));
        foreach ($entityData['positions'] ?? [] as $index => $position) {
            $this->assertSame($position['product'], $result->positions[$index]->product->id);
            $this->assertSame($position['quantity'] ?? 1, $result->positions[$index]->quantity);
        }

        [$statusCode, $resultGet] = $this->handleJsonCall('/carts/' . $result->id, 'GET');
        $this->assertSame(Response::HTTP_OK, $statusCode);
        $this->assertEquals($resultGet, $result);

        [$statusCode, $result] = $this->handleJsonCall('/carts/' . $resultGet->id, 'DELETE');
        $this->assertSame(Response::HTTP_NO_CONTENT, $statusCode);
        $this->assertNull($result);

        [$statusCode, $result] = $this->handleJsonCall('/carts/' . $resultGet->id, 'GET');
        $this->assertSame(Response::HTTP_NOT_FOUND, $statusCode);
        $this->assertNull($result);
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
            'cart with product' => [
                [
                    'positions' => [
                        ['product' => 1, 'quantity' => 3],
                        ['product' => 2],
                        ['product' => 1, 'quantity' => 0]
                    ],
                ],
            ],
        ];
    }
}
