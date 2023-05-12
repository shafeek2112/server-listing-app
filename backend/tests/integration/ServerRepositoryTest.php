<?php

namespace Tests\integration;

use App\Repositories\ServerRepository;
use App\Services\ExcelService;
use Closure;
use Illuminate\Support\Facades\Cache;

use Mockery;
use Tests\TestCase;

class ServerRepositoryTest extends TestCase
{
    protected $excelService;
    protected $serverRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->excelService = Mockery::mock(ExcelService::class);
        $this->serverRepository = new ServerRepository(new ExcelService);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testLoadDataIntoJsonFromExcel()
    {
        $this->excelService
            ->shouldReceive('loadJsonDataFromExcel')
            ->andReturn(true);
        $result = $this->serverRepository->loadDataIntoJsonFromExcel();
        $this->assertNotEmpty($result);
    }

    public function testGetServersWithoutFilters()
    {
        $result = $this->serverRepository->getServers([]);
        $this->assertIsArray($result);
    }
    
    public function testGetServersWithFilters()
    {
        $requestData = [
            'storage'       => ['33GB','1TB'],
            'ram'           => ['4GB', '8GB', '32GB'],
            'hardisk'       => 'SAS',
            'location'      => 'AmsterdamAMS-01',
        ];
        $result = $this->serverRepository->retreiveData($requestData);
        $this->assertIsArray($result);
    }
    
    public function testGetServersWithInvalidFilters()
    {
        $requestData = [
            'location'      => 'AmsterdamAMS-XXXXXX',
        ];
        $result = $this->serverRepository->retreiveData($requestData);
        $this->assertEmpty($result);
    }

    /* public function testGetServersUsesCache()
    {
        $filters = [
            'storage' => ['500GB'],
            'ram' => ['8GB'],
            'hardDiskType' => 'SSD',
            'location' => 'AmsterdamAMS-01',
        ];
    
        $cacheKey = 'servers_' . md5(json_encode($filters));
    
        Cache::shouldReceive('remember')
            ->once()
            ->with($cacheKey, 60, \Mockery::any())
            ->andReturn(['data']);
    
        $serverRepository = new ServerRepository(new ExcelService());
        $result = $serverRepository->getServers($filters);
    
        $this->assertEquals(['data'], $result);
    }

    public function testFlushCache()
    {
        Cache::shouldReceive('flush')->once();

        $this->serverRepository->flushCache();
    } */

}
