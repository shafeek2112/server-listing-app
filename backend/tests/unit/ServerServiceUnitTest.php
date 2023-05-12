<?php

namespace Tests\unit;

use App\Services\ServerService;
use App\Repositories\ServerRepository;
use Exception;
// use PHPUnit\Framework\TestCase;
use Tests\TestCase;


class ServerServiceUnitTest extends TestCase
{
    protected $serverService;
    protected $serverRepository;
    protected $servers;
    protected $requestData;

    protected function setUp(): void
    {
        parent::setUp();
        // $this->serverRepository = $this->createMock(ServerRepository::class);
        $this->serverRepository = \Mockery::mock(ServerRepository::class);
        $this->serverService = new ServerService($this->serverRepository);

        $this->servers = [
            [
                'storage' => '1TB',
                'ram' => '8GB',
                'hardDiskType' => 'SAS',
                'location' => 'AmsterdamAMS-01',
            ],
            [
                'storage' => '2TB',
                'ram' => '16GB',
                'hardDiskType' => 'SAS',
                'location' => 'AmsterdamAMS-01',
            ],
        ];

        $this->requestData = [
            'filters' => [
                'storage'       => ['250GB', '1TB'],
                'ram'           => ['4GB', '8GB'],
                'hardisk'       => 'SAS',
                'location'      => 'AmsterdamAMS-01',
            ],
            'page'  => 1,
        ];
    }

    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    public function testGetFilteredServers()
    {
        $this->serverRepository
            ->shouldReceive('getServers')
            ->andReturn($this->servers);

        // Set up the expectation
        $this->serverRepository
        ->shouldReceive('generateCacheKey')
        ->with([])
        ->andReturn('your_expected_cache_key');

        // Test with no filters
        $requestData = [];
        $result = $this->serverService->getFilteredServers($requestData);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('totalPages', $result);
        $this->assertArrayHasKey('currentPage', $result);

        // Test with filters
        $result = $this->serverService->getFilteredServers($this->requestData);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('totalPages', $result);
        $this->assertArrayHasKey('currentPage', $result);
    }

    public function testGetFilteredServersValidInput()
    {
        $this->serverRepository
            ->shouldReceive('getServers')
            ->andReturn($this->servers);

        // Set up the expectation
        $this->serverRepository
            ->shouldReceive('generateCacheKey')
            ->with([])
            ->andReturn('your_expected_cache_key');

        $this->serverService->getFilteredServers($this->requestData);
        $this->assertTrue(true);
    }

    public static function additionProvider()
    {
        return [
            [
                'filters' => [
                    'storage' => 'invalid_input', //Invalid.
                    'ram' => ['4GB', '8GB', '32GB'],
                    'hardisk' => 'SAS',
                    'location' => 'AmsterdamAMS-01',
                ],
                'page' => 1,
            ],
            [
                'filters' => [
                    'storage' => ['250GB', '1TB'],
                    'ram' => ['4GB', '8GB', '32GB'],
                    'hardisk' => 123123, // Invalid
                    'location' => 'AmsterdamAMS-01',
                ],
                'page' => 1,
            ],
            [
                'filters' => [
                    'storage' => ['250GB', '1TB'],
                    'ram' => ['4GB', '8GB', '32GB'],
                    'hardisk' => 'SAS', 
                    'location' => 1234, // Invalid
                ],
                'page' => 1,
            ],
        ];
    }

    /**
     * @dataProvider additionProvider
     */
    public function testGetFilteredServersInvalidInput($requestData)
    {
        $this->serverRepository
            ->shouldReceive('getServers')
            ->andReturn($this->servers);
        $this->expectException(Exception::class);
        $this->serverService->getFilteredServers($requestData);
    }

    public function testPaginate()
    {
        $reflection = new \ReflectionClass(ServerService::class);
        $paginateMethod = $reflection->getMethod('paginate');
        $paginateMethod->setAccessible(true);

        // Test with 5 items per page
        $result = $paginateMethod->invokeArgs($this->serverService, [$this->servers, 1]);
        $this->assertCount(2, $result['data']);
        $this->assertEquals(1, $result['totalPages']);
        $this->assertEquals(1, $result['currentPage']);

        // Test with no items
        $result = $paginateMethod->invokeArgs($this->serverService, [[], 1]);
        $this->assertCount(0, $result['data']);
        $this->assertEquals(0, $result['totalPages']);
        $this->assertEquals(0, $result['currentPage']);
    }
}
