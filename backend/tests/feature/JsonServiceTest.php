<?php

namespace Tests\feature;

use App\Services\JsonService;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class JsonServiceTest extends TestCase
{
    public function testApplyFilterWithMatchingFilters()
    {
        $jsonService = new JsonService();

        // Example data to filter
        $sampleData = [
            Config::get('constants.STORAGE_KEY_NAME') => '2TB SSD',
            Config::get('constants.RAM_KEY_NAME') => '32GB',
            Config::get('constants.LOCATION_KEY_NAME')  => 'New York'
        ];

        // Combination of filters
        $filters = [
            'storage' => ['500GB', '2TB'],
            'ram' => ['8GB', '32GB'],
            'location' => 'New York',
            'hardDiskType' => 'SSD'
        ];

        $filterPassed = [
            "storageFilter" => false,
            "ramFilter"     => false,
            "hardDiskFilter"=> false,  
            "locationFilter"=> false,  
        ];
        foreach ($sampleData as $key => $value) {
            $filterPassed = $jsonService->applyFilter($filterPassed, $value, $filters, $key);
        }
        $expect = [
            "storageFilter" => true,
            "ramFilter"     => true,
            "hardDiskFilter"=> true,  
            "locationFilter"=> true,  
        ];
        $this->assertEquals($expect,  $filterPassed, 'The applyFilter method did not return the expected filtered result.');
    }
    
    public function testApplyFilterWithNoFilters()
    {
        $jsonService = new JsonService();

        // Example data to filter
        $sampleData = [
            Config::get('constants.STORAGE_KEY_NAME') => '2TB SATA',
            Config::get('constants.RAM_KEY_NAME') => '32GB',
            Config::get('constants.LOCATION_KEY_NAME')  => 'New York'
        ];

        // Combination of filters
        $filters = [];

        $filterPassed = [
            "storageFilter" => true,
            "ramFilter"     => true,
            "hardDiskFilter"=> true,  
            "locationFilter"=> true,  
        ];
        foreach ($sampleData as $key => $value) {
            $filterPassedR = $jsonService->applyFilter($filterPassed, $value, $filters, $key);
        }
        $this->assertEquals($filterPassed,  $filterPassedR, 'The applyFilter method did not return the expected filtered result.');
    }
    
    public function testApplyFilterWithFiltersNotMatchingAnyData()
    {
        $jsonService = new JsonService();

        // Example data to filter
        $sampleData = [
            Config::get('constants.STORAGE_KEY_NAME') => '2TB SATA',
            Config::get('constants.RAM_KEY_NAME') => '32GB',
            Config::get('constants.LOCATION_KEY_NAME')  => 'New York'
        ];

        // Filters that don't match any data
        $filters = [
            'storage' => ['250GB',"500GB"],
            'ram' => ['4GB'],
            'location' => 'Washington',
            'hardDiskType' => 'NVMe'
        ];

        $filterPassed = [
            "storageFilter" => false,
            "ramFilter"     => false,
            "hardDiskFilter"=> false,  
            "locationFilter"=> false,  
        ];
        foreach ($sampleData as $key => $value) {
            $filterPassed = $jsonService->applyFilter($filterPassed, $value, $filters, $key);
        }
        $this->assertEquals($filterPassed,  $filterPassed, 'The applyFilter method did not return the expected filtered result.');
    }

}
