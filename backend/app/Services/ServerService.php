<?php

namespace App\Services;

use App\Repositories\ServerRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class ServerService
{
    protected ServerRepository $serverRepository;
    protected array $storageRange;
    protected array $sortedStorageList;

    public function __construct(ServerRepository $serverRepository)
    {
        $this->serverRepository = $serverRepository;
        $this->sortedStorageList = ['0', '250GB', '500GB', '1TB', '2TB', '3TB', '4TB', '8TB', '12TB', '24TB', '48TB', '72TB'];
        $this->storageRange = [];
    }

    public function getFilteredServers($requestData)
    {
        try {
            //Massage data for validation.
            $requestData = $this->alterData($requestData);
            
            //Sanitize the input for SQL Inject & XSS.
            $this->validateServerInput($requestData);
            
            //Fetch the data from JSON.
            $filters = !empty($requestData['filters']) ? $requestData['filters'] : [];
            $servers = $this->serverRepository->getServers($filters);          
            
            //Pagination.
            $page = !empty($requestData['page']) ? $requestData['page'] : 1;
            $data = $this->paginate($servers,$page);
            
            //Get the location list.
            $locationList = $this->getLocations($filters);
            $data['locationList'] = $locationList;
            
            //Final data
            return $data;

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
    *   Get the location list from Cache.
    *   @param array 
    *   @return array
    */
    private function getLocations(array $filters):array {
        $locationList = [];
        $cacheKey = $this->serverRepository->generateCacheKey($filters);
        if (Cache::has($cacheKey.Config::get('constants.CACHE_LOCATION_LIST'))) {
            $locationList = Cache::get($cacheKey.Config::get('constants.CACHE_LOCATION_LIST'));
        }
        return $locationList; 
    }

    /**
    *   Massage the incoming data.
    *   @param array 
    *   @return array
    */
    private function alterData($requestData) {
        if(!empty($requestData['storage']) && !is_array($requestData['storage'])) {
            $requestData['storage'] = explode(',',$requestData['storage']);
            //Unset the null value if any.
            foreach($requestData['storage'] as $key => $val) {
                if($val == 'null') {
                    unset($requestData['storage'][$key]);
                }
            }
        } 
        if(!empty($requestData['ram']) && !is_array($requestData['ram'])) {
            $requestData['ram'] = explode(',',$requestData['ram']);
            //Unset the null value if any.
            foreach($requestData['ram'] as $key => $val) {
                if($val == 'null') {
                    unset($requestData['ram'][$key]);
                }
            }
        }
        $filters = [];
        $filters['page']  = !empty($requestData['page']) ? $requestData['page'] : 1;
        if(!empty($requestData['storage']) || !empty($requestData['ram']) ||
           !empty($requestData['hardisk']) || !empty($requestData['location'])) 
        {    
            if(!empty($requestData['storage']) && ($requestData['storage'] != 'null')) $filters['filters']['storage'] = $requestData['storage'];
            if(!empty($requestData['ram']) && ($requestData['ram'] != 'null'))  $filters['filters']['ram'] = $requestData['ram'];
            if(!empty($requestData['hardisk']) && ($requestData['hardisk'] != 'null')) $filters['filters']['hardDiskType'] = $requestData['hardisk'];
            if(!empty($requestData['location']) && ($requestData['location'] != 'null')) $filters['filters']['location'] = $requestData['location'];
        }
        return $filters;
    }

    /**
    *   Load Json from Excel.
    *   @param string $fileName
    *   @return void
    */
    public function loadDataIntoJsonFromExcel($fileName=NULL)
    {
        try {
            return $this->serverRepository->loadDataIntoJsonFromExcel($fileName);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
    *   Get the storage types
    *   @param array $range
    *   @return void
    */
    private function storageRangeMapper(array $range):void 
    {
        if(!empty($this->sortedStorageList)) {
            $include = false;
            foreach($this->sortedStorageList as $storage) {
                //Include the value in the array
                if($storage === $range[0]) {
                    $include = true;
                }
                if($include) {
                    $this->storageRange[] = $storage;
                }
                //Break the loop if reached the end limit
                if($storage === $range[1]) {
                    break;
                }

            }
        }
    }

    /**
    *   Validate & sanitize the input
    *   @param array $input
    *   @return void
    */
    private function validateServerInput(array $input)
    {
        if(!empty($input['filters'])) {
            $rules = [
                'storage'       => 'nullable|array|min:0|max:5',
                'storage.*'     => 'string|max:5',
                'ram'           => 'nullable|array|min:0|max:25',
                'ram.*'         => 'string|max:255',
                'hardDiskType'  => 'nullable|string|max:255',
                'location'      => 'nullable|string|max:255',
            ];
            $validator = Validator::make($input['filters'], $rules);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        }
    }
   
    /**
    *   Paginate based on the page & data.
    *   @param array $input
    *   @return void
    */
    private function paginate($servers,$page):array
    {
        $pageSize = Config::get('constants.PAGE_SIZE');
        //Total pages.
        $totalPages = ceil(count($servers)/$pageSize);
        //Set Pagenumber
        $pageNumber = !empty($page) ? $page: 1 ;
        if($pageNumber > $totalPages) {
            $pageNumber = $totalPages;
        }
        //Current page data
        $currentPage = ($pageNumber - 1) * $pageSize;
        $currentPageData = [];
        for ($i=$currentPage; $i < $currentPage+$pageSize; $i++) { 
            if(!empty($servers[$i])) {
                array_push($currentPageData,$servers[$i]);
            }
        }
        $startNumber = 0;$endNumber=0;
        if($totalPages > 0) {
            $startNumber = (($pageNumber-1) * $pageSize) + 1;
            $endNumber = $startNumber + count($currentPageData) - 1;
        }
        return [
            "data"          => $currentPageData, 
            "totalPages"    => $totalPages,
            "currentPage"   => $pageNumber,
            "startNumber"   => $startNumber,
            "endNumber"     => $endNumber,
            "totalRecords"  => count($servers),
        ];
    }
}
