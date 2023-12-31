<?php

namespace App\Tests\Api;

use Symfony\Component\HttpFoundation\Response;

class ProductsResourceTest extends ApiTestBase
{
    public function testRedirect(): void
    {
        // Symfony redirects on missing trailing slash.
        [$statusCode, $result] = $this->apiClient->handleJsonCall('/products');
        $this->assertSame(301, $statusCode);
    }

    public function testList(): void
    {
        [$statusCode, $results] = $this->apiClient->handleJsonCall('/products/', 'GET');
        $this->assertSame(Response::HTTP_OK, $statusCode);
        $this->assertNotEmpty($results);

        [$statusCode, $result] = $this->apiClient->handleJsonCall('/products/', 'POST', ['name' => 'Product A']);
        $this->assertSame(Response::HTTP_CREATED, $statusCode);
        $this->assertEquals('Product A', $result->name);

        [$statusCode, $results] = $this->apiClient->handleJsonCall('/products/');
        $this->assertSame(Response::HTTP_OK, $statusCode);
        $this->assertNotEmpty($results);
        $this->assertEquals($result, end($results));
    }

    /**
     * @dataProvider provideProducts
     *
     * @param array<string,mixed> $entityData
     *
     * @note Update operation is not yet implemented.
     */
    public function testCrud(array $entityData): void
    {
        [$statusCode, $result] = $this->apiClient->handleJsonCall('/products/', 'POST', $entityData);
        $resultData = (array)$result;
        $this->assertSame(Response::HTTP_CREATED, $statusCode);
        $this->assertEquals($resultData, $entityData + $resultData);

        [$statusCodeGet, $resultGet] = $this->apiClient->handleJsonCall('/products/' . $result->id, 'GET');
        $this->assertSame(Response::HTTP_OK, $statusCodeGet);
        $this->assertEquals($resultGet, $result);

        [$statusCode, $result] = $this->apiClient->handleJsonCall('/products/' . $result->id, 'DELETE');
        $this->assertSame(Response::HTTP_NO_CONTENT, $statusCode);
        $this->assertNull($result);

        [$statusCodeGet, $result] = $this->apiClient->handleJsonCall('/products/' . $resultGet->id, 'GET');
        $this->assertSame(Response::HTTP_NOT_FOUND, $statusCodeGet);
        $this->assertNull($result);
    }

    /**
     * Provides data for ::testCrud.
     *
     * @return array<array<array<string,mixed>>>
     */
    public function provideProducts(): array
    {
        return [
            'enabled product' => [
                ['name' => 'Product', 'status' => true],
            ],
            'custom date' => [
                ['name' => 'Product', 'created' => '2000-01-01T12:00:00+00:00'],
            ],
            'priced product' => [
                ['name' => 'Product', 'price' => 1337],
            ],
            'data uri product' => [
                [
                    'name' => 'Product',
                    'imageUrl' => 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7',
                ],
            ],
            'full product' => [
                [
                    'name' => 'Product A',
                    'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. '
                        . 'Dolorum ut error necessitatibus soluta itaque laborum? Commodi est enim quaerat, '
                        . 'excepturi odit rerum ab nesciunt debitis quidem eius, temporibus perspiciatis aperiam?',
                    'imageUrl' => 'https://picsum.photos/id/300/200.jpg',
                    'price' => 42,
                    'status' => true,
                ],
            ],
        ];
    }
}
