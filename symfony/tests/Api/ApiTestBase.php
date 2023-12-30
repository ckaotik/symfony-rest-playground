<?php

namespace App\Tests\Api;

use App\Client\ApiClientInterface;
use App\Client\SymfonyApiClient;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class ApiTestBase extends KernelTestCase
{
    /**
     * @var \App\Client\ApiClientInterface $apiClient
     */
    protected ApiClientInterface $apiClient;

    protected function setUp(): void
    {
        /** @var \App\Client\ApiClientInterface $apiClient */
        $apiClient = static::getContainer()->get(SymfonyApiClient::class);
        $this->apiClient = $apiClient;
    }
}
