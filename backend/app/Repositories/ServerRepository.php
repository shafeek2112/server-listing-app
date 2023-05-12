<?php

namespace App\Repositories;

use App\Services\ExcelService;
use App\Listeners\JsonSearchListener;
use JsonStreamingParser\Parser;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;

class ServerRepository
{
    protected ExcelService $excelService;
    protected array $locationList;

    public function __construct(ExcelService $excelService)
    {
        $this->excelService = $excelService;
        $this->locationList = [];
    }

    /**
    *   Get the servers.
    *   @param array $filters
    *   @return array
    */
    public function getServers(array $filters):array
    {
        try {
            // Generate a cache key based on filter values
            $cacheKey = $this->generateCacheKey($filters);

            // Check if the cache has an entry for the generated cache key
            if (Cache::has($cacheKey)) {
                // If it exists, return the cached data
                return Cache::get($cacheKey);
            }
            // If the cache doesn't have an entry for the cache key, perform the filtering
            $filteredData = $this->retreiveData($filters);

            // Cache the resulting data with the cache key and set an appropriate cache expiration time (e.g., 60 minutes)
            Cache::put($cacheKey, $filteredData, Config::get('constants.CACHE_EXPIRATION_TIME'));
            
            //Add location list to the cache.
            Cache::put($cacheKey.Config::get('constants.CACHE_LOCATION_LIST'), $this->locationList, Config::get('constants.CACHE_EXPIRATION_TIME'));

            // Return the filtered data
            return $filteredData;

        } catch (\Throwable $th) {
            // dd($th);
            throw $th;
        }
    }

    /**
    *   Generate the cacheKey using the filters
    *   @param array $filters
    *   @return string 
    */
    public function generateCacheKey(array $filters): string
    {
        $cacheKey = '';
        foreach ($filters as $key => $value) {
            if (is_array($value)) {
                $value = implode('_', $value);
            }
            $cacheKey .= "{$key}_{$value}_";
        }
        // Remove the trailing underscore
        $cacheKey = rtrim($cacheKey, '_');
        //If no filter.
        if(empty($cacheKey)) $cacheKey = "_ALL_"; 
        return $cacheKey;
    }

    /**
    *   Load Json from Excel.
    *   @param string $fileName
    *   @return void
    */
    public function loadDataIntoJsonFromExcel($fileName=NULL)
    {
        return $this->excelService->loadJsonDataFromExcel($fileName);
    }

    /**
    *   Retrieve the data based on filters
    *   @param array $filters
    *   @return array
    */
    public function retreiveData($filters) {
        try {
            $fil = storage_path(Config::get('constants.JSON_FILE_PATH')).'/'.Config::get('constants.MASTER_JSON_FILE');
            //Read JSON file
            try {
                $stream = fopen($fil, 'r');
            } catch (\Exception $e) {
                throw new \Exception("Something wrong with the JSON file.");
            }
            //Use json-streaming-parser for low memory usage, scalable and event driven parsing.
            try {
                $listener = new JsonSearchListener($filters);
                $parser = new Parser($stream, $listener);
                $parser->parse();
            } catch (\Exception $e) {
                throw new \Exception("Something wrong with the JSON Stream Lib.");
            }
            //Location
            $this->locationList = $listener->getLocationList();
            return $listener->getResult();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
    *   Flushing the cache
    *   @param null
    *   @return void
    */
    public function flushCache()
    {
        Cache::flush();
    }
}
