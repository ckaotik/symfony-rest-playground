<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class ApiTestBase extends KernelTestCase
{
    protected const API_BASE_URL = 'http://symfony.localhost/api/v1';

    /**
     * The toolkit to use to get responses. Either 'http_client' or 'http_kernel'.
     *
     * @todo Fix local Docker setup not being able to connect using http_client.
     */
    protected const API_REQUEST_SERVICE = 'http_kernel';

    /**
     * Handles an API request using the configured service.
     *
     * @param string $endpoint
     * @param string $method
     * @param array<string,bool|int|string> $data
     *
     * @return array
     *   The status code and content/error message of the call.
     */
    protected function handleJsonCall(string $endpoint, string $method = 'GET', array $data = []): array
    {
        $url = static::API_BASE_URL . $endpoint;
        $data = array_filter($data, fn($value) => $value !== null);

        if (in_array($method, ['POST', 'PUT', 'PATCH'], true)) {
            // Pass data in body.
        } elseif ($data) {
            // Pass data in URL query.
            $url .= '?' . http_build_query($data);
            $data = null;
        }

        $response = static::API_REQUEST_SERVICE === 'http_kernel'
            ? $this->httpKernelRequest($url, $method, $data)
            : $this->httpClientRequest($url, $method, $data);

        $content = json_decode($response->getContent() ?: '');

        return [
            $response->getStatusCode(),
            $response->isSuccessful() ? $content : (is_object($content) ? $content?->error : null),
        ];
    }

    /**
     * @param string $url
     * @param string $method
     * @param array<string,bool|int|string> $data
     */
    protected function httpClientRequest(string $url, string $method = 'GET', ?array $data = []): ResponseInterface
    {
        /** @var \Symfony\Contracts\HttpClient\HttpClientInterface $httpClient */
        $httpClient = static::getContainer()->get('http_client');

        return $httpClient->request($method, $url, [
            'body' => json_encode($data),
        ]);
    }

    /**
     * @param string $url
     * @param string $method
     * @param array<string,bool|int|string> $data
     */
    protected function httpKernelRequest(string $url, string $method = 'GET', ?array $data = []): Response
    {
        /** @var \Symfony\Component\HttpKernel\HttpKernelInterface $httpKernel */
        $httpKernel = static::getContainer()->get('http_kernel');

        $subRequest = Request::create($url, $method, $data);

        return $httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }
}
