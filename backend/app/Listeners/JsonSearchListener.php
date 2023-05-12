<?php 

namespace App\Listeners;

use JsonStreamingParser\Listener\ListenerInterface;
use App\Services\JsonService;
use Illuminate\Support\Facades\Config;

class JsonSearchListener implements ListenerInterface
{
    protected $searchKey;
    protected $searchValue;
    protected $result;
    protected $currentKey;
    protected array $filterPassed;
    protected bool $storageFilter;
    protected bool $hardDiskFilter;
    protected bool $ramFilter;
    protected bool $locationFilter;
    protected JsonService $jsonService;
    protected array $locationsList;
    protected array $locationsHash;
    
    public function __construct($filters=null)
    {
        $this->jsonService = new JsonService();
        $this->filters = $filters;
        $this->result = [];
        $this->stack = [];
        $this->currentKey = null;
        $this->retrieveAll = $filters === null;
    }
    public function startDocument(): void {
        $this->stack = array();

        $this->currentKey = null;
    }

    public function endDocument(): void {}

    public function startObject(): void
    {
        // $this->filterPassed     = (empty($this->filters)) ? true : false;
        // $this->storageFilter    = (!empty($this->filters['storage'])) ? false : true;
        // $this->ramFilter        = (!empty($this->filters['ram'])) ? false : true;
        // $this->hardDiskFilter   = (!empty($this->filters['hardDiskType'])) ? false : true;
        // $this->locationFilter   = (!empty($this->filters['location'])) ? false : true;
        $this->filterPassed = [
            "storageFilter" => (!empty($this->filters['storage']))      ? false : true,
            "ramFilter"     => (!empty($this->filters['ram']))          ? false : true,
            "hardDiskFilter"=> (!empty($this->filters['hardDiskType'])) ? false : true,  
            "locationFilter"=> (!empty($this->filters['location']))     ? false : true,  
        ];
        array_push($this->stack, array());
    }

    public function endObject(): void
    {
        $obj = array_pop($this->stack);
        if (empty($this->stack)) {
            // doc is DONE!
            $this->result = $obj;
        } else {
            $this->value($obj);
        }
        //Remove if the data is not in filter
        $allPassed = ($this->filterPassed['storageFilter'] && $this->filterPassed['ramFilter'] && $this->filterPassed['hardDiskFilter'] && $this->filterPassed['locationFilter']);
        if(!$allPassed && !empty($this->stack[0])) {
            array_pop($this->stack[0]);
        }
    }

    public function startArray(): void
    {
        $this->startObject();
    }

    public function endArray(): void
    {
        $this->endObject();
    }

    public function key(string $key): void
    {
        $this->currentKey = trim($key);
    }

    public function value($value): void
    {
        $obj = array_pop($this->stack);
        if ($this->currentKey && (gettype($value) === 'string')) {
            //If filter exists, should match the filter.
            if(!empty($this->filters)) {
                //JSON Service do the complete filtering of data.
                $this->filterPassed = $this->jsonService->applyFilter($this->filterPassed,$value,$this->filters,$this->currentKey);
            }
            //Store location.
            $this->locationList($value);

            $obj[strtolower($this->currentKey)] = $value;
            $this->currentKey = null;
        } else {
            array_push($obj, $value);
        }
        array_push($this->stack, $obj);
    }

    public function locationList($value):void 
    {
        //Store the location list
        if($this->currentKey === Config::get('constants.LOCATION_KEY_NAME') && empty($this->locationsHash[$value])) {
            $this->locationsList[] = $value;
            //This hash is to track, whether this location already added or not.
            $this->locationsHash[$value] = true;
        }
    }

    public function whitespace(string $whitespace): void {}

    public function getLocationList(): array
    {
        return !empty($this->locationsList) ? $this->locationsList : [];
    }
    
    public function getResult(): array
    {
        return !empty($this->result) ? $this->result : [];
    }
}
