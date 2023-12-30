<?php

namespace App\Client;

interface ApiClientInterface {
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
    public function handleJsonCall(string $endpoint, string $method = 'GET', array $data = []): array;
}