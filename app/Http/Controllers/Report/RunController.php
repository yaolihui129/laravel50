<?php

namespace App\Http\Controllers\Report;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\BugService;
use App\Services\RunService;
use Illuminate\Http\Request;
use App\Services\AutoExecReportService;
use Illuminate\Support\Facades\Auth;
use App\Services\ProjectService;
use App\Services\SysDictService;
use Illuminate\Support\Facades\DB;

class RunController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    //运行分析
    public function index(Request $request)
{
    return view("report.run");
}

    public function add(Request $request)
    {
        $title=$request->input('title');
        $newtitle=$request->input('newtitle');
        $description=$request->input('description');
        $time=date('Y-m-d H:i:s',time());
        $reportid=$request->input('reportid');
        $query=new RunService();
        $res=$query->add($title,$newtitle,$description,$time,$reportid);
        if($res){
            $arr['success']=1;
        }else{
            $arr['success']=0;
        }
        echo json_encode($arr);
    }



    //bug分析
    public function bugindex(Request $request)
    {
        return view("report.bug");
    }

    public function bugadd(Request $request)
    {
        $title=$request->input('title');
        $newtitle=$request->input('newtitle');
        $description=$request->input('description');
        $time=date('Y-m-d H:i:s',time());
        $reportid=$request->input('reportid');
        $query=new BugService();
        $res=$query->add($title,$newtitle,$description,$time,$reportid);
        if($res){
            $arr['success']=1;
        }else{
            $arr['success']=0;
        }
        echo json_encode($arr);
    }

}
