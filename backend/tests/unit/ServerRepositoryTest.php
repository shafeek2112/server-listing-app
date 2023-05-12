<?php

namespace Tests\Unit;

use App\Repositories\ServerRepository;
use App\Services\ExcelService;
use Tests\TestCase;

class ServerRepositoryTest extends TestCase
{
    protected $serverRepository;
    protected function setUp(): void
    {
        parent::setUp();
        // Set the environment variable
        putenv('APP_ENV=testing');
        $this->serverRepository = new ServerRepository(new ExcelService);
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testGenerateCacheKey()
    {
        $filters = [
            'storage' => ['500GB'],
            'ram' => ['8GB'],
            'hardDiskType' => 'SSD',
            'location' => 'AmsterdamAMS-01',
        ];

        $expectedCacheKey = 'storage_500GB_ram_8GB_hardDiskType_SSD_location_AmsterdamAMS-01';
        $reflectionClass = new \ReflectionClass(ServerRepository::class);
        $reflectionMethod = $reflectionClass->getMethod('generateCacheKey');
        $reflectionMethod->setAccessible(true);
        $generatedCacheKey = $reflectionMethod->invoke($this->serverRepository, $filters);
        $this->assertEquals($expectedCacheKey, $generatedCacheKey);
    }
}