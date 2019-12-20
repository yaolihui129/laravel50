<?php

namespace App\Http\Controllers\App;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\AppReportService;
use App\Services\BugService;
use App\Services\RunService;
use App\Services\zztservice;
use Illuminate\Http\Request;
use App\Services\AutoExecReportService;
use Illuminate\Support\Facades\Auth;
use App\Services\ProjectService;
use App\Services\SysDictService;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
class TestReportController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */


# 郑梓涛分页插件 #
    public function userList(Request $request) {
        $query=new zztservice();
        $users=$query->add123();
        $perPage = 3;
        if ($request->has('page')) {
            $current_page = $request->input('page');
            $current_page = $current_page <= 0 ? 1 :$current_page;
        } else {
            $current_page = 1;
        }

        $item = array_slice($users, ($current_page-1)*$perPage, $perPage); //注释1
        $total = count($users);

        $paginator =new LengthAwarePaginator($item, $total, $perPage, $currentPage=null, [
            'path' => Paginator::resolveCurrentPath(),  //注释2
            'pageName' => 'page',
        ]);

        $userlist = $paginator->toArray()['data'];

        return view('app.test', compact('userlist', 'paginator'));








    }





//    //运行分析
//    public function add()
//    {
////        $posts=DB::table("auto_tasks as att")
////            ->select("id as kkk","intProjectID","chrTaskName")
////            ->lists('taskName', 'att.chrTaskName')
////            ->lists('atp.chrProjectName', ' projectName')
////            ->lists('us.chrUserName', 'createUser')
////            ->lists('ats.id', 'projectId')
////            ->lists('ate.id', 'taskExecID')
////            ->lists('ate.chrBrowserNames', 'browserNames')
////            ->lists('ate.chrBrowserIDs', 'browserId')
////            ->join('users as us', 'us.id', '=', 'att.intCreaterID')
////            ->join('auto_scripts  as ats', 'ats.id', '=', 'att.intProjectID')
////            ->join('auto_projects as atp', 'atp.id', '=', 'ats.intProjectID')
////            ->leftJoin('auto_task_execs as ate', 'ate.intTaskID', '=', 'att.id ')
////            ->where('ats.intTopProjectID', '336')
////            ->orderBy('att.id', 'desc')
////            ->paginate(5);
////        //$posts = DB::table("auto_tasks as att")->paginate(5);
////        return view('app.zzt',['posts'=>$posts]);
//
//
//
//        $posts = DB::select ( "select
//case when ate.intState=0 then '排队中' when ate.intState=1 then '执行中' when ate.intState=2 then '执行成功'
//				when ate.intState=3 then '执行失败' else '未执行' end state,
//att.id id,
//att.chrTaskName taskName,
//atp.chrProjectName projectName,
//us.chrUserName createUser,
//ats.id projectId,
//ate.id taskExecID,
//ate.chrBrowserNames browserNames,
//ate.chrBrowserIDs browserId
//from auto_tasks att
//join users us ON us.id=att.intCreaterID
//join auto_scripts ats ON ats.id=att.intProjectID
//
//join auto_projects atp ON atp.id=ats.intProjectID
//
//LEFT JOIN auto_task_execs ate on ate.intTaskID=att.id
//
//where ats.intTopProjectID='336'
//order by id desc" );
//
//
//    }




}
