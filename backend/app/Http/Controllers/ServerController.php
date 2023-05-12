<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services\ServerService;
use Illuminate\Http\Response;

class ServerController extends Controller
{
    protected $serverService;
    
    /**
    * Create a new controller instance.
    *
    * @return void
    */
    public function __construct(ServerService $serverService)
    {
        $this->serverService = $serverService;
    }

    /**
    *   Retrieve the server list based on the filters
    *
    * @param  Request
    * @return Response
    */
    public function getFilteredServers(Request $request)
    {
        try {
            $filteredServers = $this->serverService->getFilteredServers($request->all());
        } catch (\Exception $e) {
            return response()->json([
                'data'  => [],
                'error' => $e->getMessage(),
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return response()->json($filteredServers,Response::HTTP_OK,[], JSON_UNESCAPED_UNICODE);
    }
    
    /**
    *   Load excel data into JSON file
    *   @param  int  $fileName
    *   @return string
    */
    public function loadDataIntoJson($fileName = NULL)
    {
        $filteredServers = $this->serverService->loadDataIntoJsonFromExcel($fileName);
        return response()->json([
            "data"      => $filteredServers,
            "message"   => "Successfully Triggered.",
            "status"    => "Success"
        ]);
    }
}
