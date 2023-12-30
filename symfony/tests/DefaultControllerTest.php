<?php

namespace App\Tests;

use App\Controller\DefaultController;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class DefaultControllerTest extends KernelTestCase
{
    public function testSomething(): void
    {
        $kernel = self::bootKernel();

        $this->assertSame('test', $kernel->getEnvironment());
        // $routerService = static::getContainer()->get('router');
        // $myCustomService = static::getContainer()->get(CustomService::class);
    }

    /**
     * @dataProvider provideCurrentApiData
     */
    public function testCurrentApi(?string $expectedResult, string $requestUri): void
    {
        /** @var \Symfony\Bundle\FrameworkBundle\Routing\Router $router */
        $router = static::getContainer()->get('router');

        if ($expectedResult === null) {
            $this->expectException(ResourceNotFoundException::class);
        }

        $match = $router->getMatcher()->match($requestUri);
        if (!$match) {
            return;
        }

        if ($match && $match['_controller'] == DefaultController::class . '::currentApi') {
            $uut = new DefaultController();
            $response = $uut->currentApi($match['version']);

            $actualResult = $response instanceof RedirectResponse
                ? $response->getTargetUrl()
                : $response->headers->get('Location');
        } else {
            $parameters = array_filter($match, function(string $key) {
                return substr($key, 0, 1) !== '_';
            }, ARRAY_FILTER_USE_KEY);

            $actualResult = $router->generate($match['_route'], $parameters);
        }

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * Provide test cases for ::testCurrentApi.
     *
     * @return array<string,array<string>>
     */
    public function provideCurrentApiData(): array
    {
        return [
            'non-API url' => [
                '/cart', '/cart',
            ],
            'unversioned API url' => [
                '/api/v1/carts/', '/api/carts/'
            ],
            'versioned API url' => [
                '/api/v1/carts/', '/api/v1/carts/',
            ],
            'incorrectly versioned API url' => [
                null, '/api/v1337/carts/',
            ],
            'incorrectly versioned API url' => [
                null, '/api/v1337/carts/',
            ],
            'unknown route' => [
                null, '/foo',
            ],
        ];
    }
}
