<?php

namespace App\Http\Controllers\App;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\AppReportService;
use App\Services\BugService;
use App\Services\RunService;
use Illuminate\Http\Request;
use App\Services\AutoExecReportService;
use Illuminate\Support\Facades\Auth;
use App\Services\ProjectService;
use App\Services\SysDictService;
use Illuminate\Support\Facades\DB;

class AppReportController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    //运行分析
    public function index($taskExecID,$browserId,$browserName)
{
    return view("app.appreport")->with("taskExecID", $taskExecID)->with("browserId", $browserId)->with("browserName", $browserName);
}

    public function add(Request $request)
    {
        $intExecTaskID=$request->input('intExecTaskID');
        $browserID=$request->input('browserID');
        $query=new AppReportService();
        $res=$query->add($intExecTaskID,$browserID);
        if($res){
            foreach ($res as $rows) {
                $date[] = $rows;
                $arr['success'] = 1;
                $arr['date'] = $date;
            }
        }else{
            $arr['success']=0;
        }
        echo json_encode($arr);
    }



}
