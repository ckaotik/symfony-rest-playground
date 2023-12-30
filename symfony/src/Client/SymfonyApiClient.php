<?php

namespace App\Client;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class SymfonyApiClient implements ApiClientInterface
{
    protected const API_BASE_URL = 'http://symfony.localhost/api/v1';

    /**
     * The toolkit to use to get responses. Either 'http_client' or 'http_kernel'.
     *
     * @todo Fix local Docker setup not being able to connect using http_client.
     */
    protected const API_REQUEST_SERVICE = 'http_kernel';

    /**
     * @var \Symfony\Contracts\HttpClient\HttpClientInterface
     */
    protected HttpClientInterface $httpClient;

    /**
     * @var \Symfony\Component\HttpKernel\HttpKernelInterface $service
     */
    protected HttpKernelInterface $httpKernel;

    public function __construct(HttpClientInterface $httpClient, HttpKernelInterface $httpKernel)
    {
        $this->httpClient = $httpClient;
        $this->httpKernel = $httpKernel;
    }

    /**
     * @inheritDoc
     */
    public function handleJsonCall(string $endpoint, string $method = 'GET', array $data = []): array
    {
        $url = static::API_BASE_URL . $endpoint;
        $data = array_filter($data, fn($value) => $value !== null);

        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
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
    protected function httpClientRequest(string $url, string $method = 'GET', ?array $data = null): ResponseInterface
    {
        return $this->httpClient->request($method, $url, [
            'body' => json_encode($data),
        ]);
    }

    /**
     * @param string $url
     * @param string $method
     * @param array<string,bool|int|string> $data
     */
    protected function httpKernelRequest(string $url, string $method = 'GET', ?array $data = null): Response
    {
        $subRequest = Request::create($url, $method, [], [], [], [], json_encode($data));

        return $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }
}
