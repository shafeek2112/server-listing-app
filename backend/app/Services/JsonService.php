<?php
declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Config;

class JsonService
{
    private array $filters;

    public function __construct()
    {

    }

    /**
    *   Apply filter.
    *   @param array filterPassed
    *   @param string value
    *   @param array filters
    *   @param string currentKey
    *   @return bool
    */
    public function applyFilter(array $filterPassed,$value,array $filters,string $currentKey):array 
    {
        $this->filters = $filters;
        $value = gettype($value) === 'string' ? trim($value) : $value;

        //1. Storage
        if(!empty($this->filters['storage'])) {
            if($currentKey === Config::get('constants.STORAGE_KEY_NAME')) {
                $filterPassed['storageFilter'] = ($this->matchFilterAndValue('storageSize',$value)) ? true : false;
            }
        }
        //2. Ram
        if(!empty($this->filters['ram'])) {
            if($currentKey === Config::get('constants.RAM_KEY_NAME')) {
                $filterPassed['ramFilter'] = ($this->matchFilterAndValue('ram',$value)) ? true : false;
            }
        }
        //3. HDD Type
        if(!empty($this->filters['hardDiskType'])) {
            if($currentKey === Config::get('constants.STORAGE_KEY_NAME')) {
                $filterPassed['hardDiskFilter'] = ($this->matchFilterAndValue('storageType',$value)) ? true : false;
            }
        }
        //4. Location
        if(!empty($this->filters['location'])) {
            if($currentKey === Config::get('constants.LOCATION_KEY_NAME')) {
                $filterPassed['locationFilter'] = ($this->matchFilterAndValue('location',$value)) ? true : false;
            }
        }
        return $filterPassed;
    }

    /**
    *   Match the appropriate helper function to filter.
    *   @param string key
    *   @param string val
    *   @return bool
    */
    private function matchFilterAndValue(string $key, string $value):bool 
    {
        switch ($key) {
            case 'storageSize':
                return $this->checkStorageValue($value,'size');
                break;
            
            case 'storageType':
                return $this->checkStorageValue($value,'type');
                break;
            
            case 'ram':
                return $this->checkRamValue($value);
                break;
            
            case 'location':
                return $this->checkLocation($value);
                break;
            
            default:
                return false;
                break;
        }
        return false;
    }

    /**
    *   Check the storage value.
    *   @param string hdd
    *   @param string type
    *   @return bool
    */
    private function checkStorageValue(string $hdd,string $type):bool 
    {
        //Match TB|GB
        if($type === 'size') {
            $pregMatcher = Config::get('constants.PREG_MATCH_STORAGE');
            return $this->checkStorage($hdd);
        }
        //Match SSD|SATA|SAS
        else if($type === 'type') {
            $filterKey = $this->filters['hardDiskType'];
            if(strpos($hdd, $filterKey) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
    *   Check & compare the storage value with current value.
    *   @param string hdd
    *   @return bool
    */
    private function checkStorage(string $hdd):bool 
    {
        $start = !empty($this->filters['storage'][0]) ? $this->filters['storage'][0] : 0;
        $end = !empty($this->filters['storage'][1]) ? $this->filters['storage'][1] : INF;
        $startLimit = $this->extractTotalStorage($start);
        $endLimit   = $this->extractTotalStorage($end);
        $currentHdd = $this->extractTotalStorage($hdd);
        return ( $currentHdd >= $startLimit && $currentHdd <= $endLimit) ? true : false;
    }

    /**
    *   Extract the total storage
    *   @param string hdd
    *   @return float
    */
    private function extractTotalStorage(string $hdd)
    {
        preg_match_all(Config::get('constants.PREG_MATCH_STORAGE'), $hdd, $matches);
        $totalStorage = 0;
        foreach ($matches[1] as $index => $value) {
            $unit = $matches[2][$index];
            $totalStorage += $this->getStorageFromUnit($unit,$value);
        }
        return $totalStorage;
    }

    /**
    *   Convert the HDD into unit based on type
    *   @param string unit
    *   @param string value
    *   @return integer
    */
    protected function getStorageFromUnit(string $unit,$value) 
    {
        if ($unit === 'TB') {
            $totalStorage = floatval($value) * Config::get('constants.TB_GB_CONVERTION');
        } else {
            $totalStorage = floatval($value);
        }
        return $totalStorage;
    }

    /**
    *   Check the RAM size
    *   @param string ram
    *   @return bool
    */
    private function checkRamValue(string $ram):bool 
    {
        //Match GB
        preg_match(Config::get('constants.PREG_MATCH_RAM'), $ram, $matches);
        if(in_array($matches[1] . $matches[2],$this->filters['ram'])) {
            return true;
        }
        return false;
    }
   
    /**
    *   Check the location
    *   @param string location
    *   @return bool
    */
    private function checkLocation(string $location):bool 
    {
        //Match location
        return ($location === $this->filters['location']) ? true : false; 
    }
}
