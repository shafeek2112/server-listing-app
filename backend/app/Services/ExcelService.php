<?php
declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel as MaatExcel;
use Symfony\Component\Finder\Finder;

class ExcelService
{
    private $jsonFilesPath;
    private $excelFilesPath;
    private $jsonFileName;
    private $jsonFilePath;
    private $jsonData;
    private $excelData;

    public function __construct()
    {
        $this->jsonFilesPath = storage_path(Config::get('constants.JSON_FILE_PATH'));
        $this->excelFilesPath = storage_path(Config::get('constants.EXCELS_FILE_PATH'));
        $this->jsonFileName = Config::get('constants.MASTER_JSON_FILE');
        $this->jsonFilePath = $this->jsonFilesPath . '/' . $this->jsonFileName;
        $this->jsonData = [];
        $this->excelData = [];
    }

    /**
    *   Call the create json function based on the param. 
    *   @param null
    *   @return void
    */
    public function loadJsonDataFromExcel($fileName=NULL) {
        //If the filename is exists, call append method
        if(!empty($fileName)) {
            $this->appendToMasterJsonFileFromExcel($fileName);
        } 
        else {
            //otherwise, call create method.
            $this->createMasterJsonFileFromExcels();
        }

        //For simplicity, just delete all the cache whenever new data are loaded.
        Cache::flush();

        return $this->jsonData;
    }

    /**
    *   Create master Json from all the excels in the folder. 
    *   @param null
    *   @return void
    */
    private function createMasterJsonFileFromExcels() {
        //Read data from excel & convert into JSON.
        $this->readFromExcelFiles();
        if(!empty($this->excelData)) {
            //fetch exisitng json data from master.
            $this->getMasterJson();
            //Store into the Master json file.
            $this->putJson();
        }
    }
    
    /**
    *   Append data from a excel into master Json file. 
    *   @param string filename
    *   @return void
    */
    private function appendToMasterJsonFileFromExcel(string $fileName) {
        //Read data from excel & convert into JSON.
        $this->readFromExcelFile($fileName);
        if(!empty($this->excelData)) {
            //fetch exisitng json data from master.
            $this->getMasterJson();
            //Store into the Master json file.
            $this->putJson();
        }
    }

    /**
    *   Read data from excel & convert into array 
    *   @param null
    *   @return void
    */
    private function readFromExcelFile(string $fileName):void
    {
        //Get excel data.
        $fileName = $this->checkFileExtension($fileName);
        //Export the data.
        $this->exportExcelData($this->excelFilesPath.'/'.$fileName);
    }

    /**
    *   Export data from excel. 
    *   @param string filepath
    *   @return void
    */
    private function exportExcelData(string $filePath):void {
        try {
            $excelData = MaatExcel::toArray([], $filePath)[0];
            $headers = array_shift($excelData);
            $array = array_map(function ($row) use ($headers) {
                return array_combine($headers, $row);
            }, $excelData);
            $this->excelData = array_merge($this->excelData, $array); 
        } catch (\Throwable $th) {
            // dd($th);
            throw $th;
        }
    }
    
    /**
    *   Read data from excels & convert into array 
    *   @param null
    *   @return void
    */
    private function readFromExcelFiles():void
    {
        $finder = new Finder();
        $finder->files()->in($this->excelFilesPath);
        foreach ($finder as $file) {
            //Export the data.
            $this->exportExcelData($file->getRealPath());
        }
    }

    /**
    *   Function to fetch the data from master json and store as array 
    *   @param null
    *   @return void
    */
    private function getMasterJson():void 
    {
        try {
            //Check file exists or not.
            if (File::exists($this->jsonFilePath)) {
                //Fetch the content
                $contents = File::get($this->jsonFilePath);
                //Decode into php array
                $data = json_decode($contents, true);
                $data = !empty($data) ? $data : [];
                //Append into the exisiting 
                $this->jsonData = array_merge($this->jsonData, $data);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    
    /**
    *   Store the date into master file.
    *   @param null
    *   @return void
    */
    private function putJson():void 
    {
        try {
            $this->jsonData = array_merge($this->jsonData, $this->excelData);
            $updatedContents = json_encode($this->jsonData, JSON_UNESCAPED_UNICODE);
            File::put($this->jsonFilePath, $updatedContents);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
    *   Check the extenstion and add it if not found.
    *   @param string filename
    *   @return string
    */
    private function checkFileExtension(string $filename):string
    {
        // Check if the file has a .xlsx extension
        if (pathinfo($filename, PATHINFO_EXTENSION) !== 'xlsx') {
            // If it doesn't have the .xlsx extension, add it
            $filename .= '.xlsx';
        }
        return $filename;
    }



}
