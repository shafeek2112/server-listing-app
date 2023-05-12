<?php

namespace Tests\Unit;

use App\Services\JsonService;
use Tests\TestCase;

class JsonServiceUnitTest extends TestCase
{
    protected $jsonService;

    protected function setUp(): void
    {
        parent::setUp();
        // Set the environment variable
        putenv('APP_ENV=testing');
        $this->jsonService = new JsonService();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testGetStorageFromUnit()
    {
        $reflectionClass = new \ReflectionClass(JsonService::class);
        $reflectionMethod = $reflectionClass->getMethod('getStorageFromUnit');
        $reflectionMethod->setAccessible(true);
        $result = $reflectionMethod->invokeArgs($this->jsonService, ['TB', 1]);
        $this->assertEquals(1024, $result);
    }

    public function testExtractTotalStorageWithMockedGetStorageFromUnit()
    {
        $jsonService = $this->getMockBuilder(JsonService::class)
            ->onlyMethods(['getStorageFromUnit'])
            ->getMock();

        $jsonService->method('getStorageFromUnit')
            ->willReturnCallback(function ($unit, $value) {
                if ($unit === 'TB') {
                    return floatval($value) * 1000;
                } else {
                    return floatval($value);
                }
            });

        $storageValue = '8x2TBSATA2';
        $expectedTotalStorage = 2048;

        $reflectionClass = new \ReflectionClass(JsonService::class);
        $reflectionMethod = $reflectionClass->getMethod('extractTotalStorage');
        $reflectionMethod->setAccessible(true);
        $result = $reflectionMethod->invokeArgs($this->jsonService, [$storageValue]);
        $this->assertEquals($expectedTotalStorage, $result, 'The extractTotalStorage method did not return the expected value with a mocked getStorageFromUnit method.');
    }

    public function testCheckStorage()
    {
        // Test data
        $data = [
            ['input' => '2x2TBSATA2', 'expected' => true],
            ['input' => '4x480GBSSD', 'expected' => false],
            ['input' => '4x500GBSSD', 'expected' => true],
            ['input' => '2x3TBSATA2', 'expected' => false],
        ];

        // Set up filters in JsonService using reflection
        $reflection = new \ReflectionClass(JsonService::class);
        $filtersProperty = $reflection->getProperty('filters');
        $filtersProperty->setAccessible(true);
        $filtersProperty->setValue($this->jsonService, [
            'storage' => ['500GB', '2TB']
        ]);

        // Access the private method checkStorage using reflection
        $method = $reflection->getMethod('checkStorage');
        $method->setAccessible(true);
        
        // Run test cases
        foreach ($data as $testCase) {
            $result = $method->invoke($this->jsonService, $testCase['input']);
            $this->assertEquals($testCase['expected'], $result, "The checkStorage method returned an incorrect result for input '{$testCase['input']}'.");
        }
    }
    
    public function testCheckStorageValue()
    {
        // Test data
        $data = [
            ['input' => '2x2TBSATA2', 'type' => 'size', 'expected' => true],
            ['input' => '4x480GBSSD', 'type' => 'type', 'expected' => false],
            ['input' => '4x480GBSSD', 'type' => 'size', 'expected' => false],
            ['input' => '2x3TBSATA2', 'type' => 'type', 'expected' => true],
        ];

        // Set up filters in JsonService using reflection
        $reflection = new \ReflectionClass(JsonService::class);
        $filtersProperty = $reflection->getProperty('filters');
        $filtersProperty->setAccessible(true);
        $filtersProperty->setValue($this->jsonService, [
            'storage' => ['500GB', '2TB'],
            'hardDiskType' => 'SATA'
        ]);

        // Access the private method checkStorage using reflection
        $method = $reflection->getMethod('checkStorageValue');
        $method->setAccessible(true);
        
        // Run test cases
        foreach ($data as $testCase) {
            $result = $method->invokeArgs($this->jsonService, [$testCase['input'], $testCase['type']]);
            $this->assertEquals($testCase['expected'], $result, "The checkStorage method returned an incorrect result for input '{$testCase['input']}'.");
        }
    }

    public function testCheckRamValue()
    {
        // Test data
        $data = [
            ['input' => '16GBDDR3', 'expected' => false],
            ['input' => '32GBDDR3', 'expected' => true],
            ['input' => '64GBDDR4', 'expected' => false],
            ['input' => '128GBDDR4', 'expected' => true],
        ];
        
        // Set up filters in JsonService using reflection
        $reflectionClass = new \ReflectionClass(JsonService::class);
        $filtersProperty = $reflectionClass->getProperty('filters');
        $filtersProperty->setAccessible(true);
        $filtersProperty->setValue($this->jsonService, [
            'ram' => ['32GB', '128GB'],
        ]);
        $reflectionMethod = $reflectionClass->getMethod('checkRamValue');
        $reflectionMethod->setAccessible(true);
        // Run test cases
        foreach ($data as $testCase) {
            $result = $reflectionMethod->invoke($this->jsonService, $testCase['input']);
            $this->assertEquals($testCase['expected'], $result, "The checkStorage method returned an incorrect result for input '{$testCase['input']}'.");
        }
    }
    
    public function testCheckLocation()
    {
        // Test data
        $data = [
            ['input' => 'AmsterdamAMS-01', 'expected' => false],
            ['input' => 'NewYork', 'expected' => true],
        ];
        
        // Set up filters in JsonService using reflection
        $reflectionClass = new \ReflectionClass(JsonService::class);
        $filtersProperty = $reflectionClass->getProperty('filters');
        $filtersProperty->setAccessible(true);
        $filtersProperty->setValue($this->jsonService, [
            'location' => 'NewYork',
        ]);
        $reflectionMethod = $reflectionClass->getMethod('checkLocation');
        $reflectionMethod->setAccessible(true);
        // Run test cases
        foreach ($data as $testCase) {
            $result = $reflectionMethod->invoke($this->jsonService, $testCase['input']);
            $this->assertEquals($testCase['expected'], $result, "The checkStorage method returned an incorrect result for input '{$testCase['input']}'.");
        }
    }

    public function testApplyFilterWithValidStorageFilter()
    {
        $filters = [
            'storage' => ['500GB', '2TB'],
        ];
        $currentKey = 'HDD';
        $value = '2x2TBSATA2';

        $filterPassed = [
            "storageFilter" => false,
            "ramFilter"     => true,
            "hardDiskFilter"=> true,  
            "locationFilter"=> true,  
        ];
        
        $result = $this->jsonService->applyFilter($filterPassed, $value, $filters, $currentKey);

        $this->assertTrue($result['storageFilter'], 'The applyFilter method did not work as expected with a valid storage filter.');
    }
    
    public function testApplyFilterWithInValidStorageFilter()
    {
        $filters = [
            'storage' => ['500GB', '1TB'],
        ];
        $currentKey = 'HDD';
        $value = '2x2TBSATA2';
        $filterPassed = [
            "storageFilter" => false,
            "ramFilter"     => true,
            "hardDiskFilter"=> true,  
            "locationFilter"=> true,  
        ];
        $result = $this->jsonService->applyFilter($filterPassed, $value, $filters, $currentKey);
        $this->assertFalse($result['storageFilter'], 'The applyFilter method did not work as expected with a valid storage filter.');
    }
}
