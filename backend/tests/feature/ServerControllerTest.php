<?php

use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use Illuminate\Http\Response;
use App\Services\ServerService;
use Illuminate\Support\Facades\Config;

class ServerControllerTest extends BaseTestCase
{
    public function createApplication()
    {
        return require __DIR__.'/../../bootstrap/app.php';
    }

    public function tearDown(): void
    {
        // parent::tearDown();
    }

    public function testGetFilteredServers()
    {
        $serverService = $this->createMock(ServerService::class);
        $serverService->expects($this->once())
            ->method('getFilteredServers')
            ->willReturn([]);
        $this->app->instance(ServerService::class, $serverService);

        $response = $this->call('GET', '/api/servers');
        $this->assertEquals(Response::HTTP_OK, $response->status());
        $this->assertEquals([], json_decode($response->getContent(), true));
    }

    public function testLoadDataIntoJson()
    {
        $serverService = $this->createMock(ServerService::class);
        $serverService->expects($this->once())
            ->method('loadDataIntoJsonFromExcel')
            ->willReturn([]);
        $this->app->instance(ServerService::class, $serverService);

        $response = $this->call('GET', '/api/servers/json-file-generate');
        $this->assertEquals(Response::HTTP_OK, $response->status());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertArrayHasKey('status', $data);
    }

    public function testGetServers()
    {
        $response = $this->call('GET', '/api/servers');
        $this->assertEquals(Response::HTTP_OK, $response->status());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('totalPages', $data);
        $this->assertArrayHasKey('currentPage', $data);
        $this->assertThat(
            count($data['data']),
            $this->logicalOr(
                $this->equalTo(Config::get('constants.PAGE_SIZE')),
                $this->equalTo(0)
            )
        );
        
    }
}
