<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use App\Services\ApiTaskExecService;
use App\Services\ApiTaskRunService;
use App\Services\ApiTaskService;
use Illuminate\Http\Request;
use App\Services\AutoTaskService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\AutoTaskExecService;
use App\Services\ProjectService;
use App\Services\SysDictService;
use App\Utils\FileHelper;
use Illuminate\Support\Facades\DB;

class ApiTaskController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request) {
        //
        $user = $request->user ();
        $proService = new ProjectService ();
        $projects = $proService->getProjectTree ( $user );
        $sdService = new SysDictService ();
        $states = $sdService->getTaskExecState ();
        $pages = array (
            "projects" => $projects,
            "states" => $states
        );
        return view ( "api.web.apitask" )->with ( $pages );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request) {
        //
        $user = $request->user ();
        $task = $request->all ();
        $atService = new ApiTaskService();
        $atService->insert ( $task, $user );
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit(Request $request, $id) {
        //
        $user = $request->user ();
        $atService = new ApiTaskService();
        $task = $atService->getTaskById ( $id, $user );
        return "{success:1,task:" . json_encode ( $task ) . "}";
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id) {
        //
        $task = $request->all ();
        $ateService = new ApiTaskExecService();
        $rows = $ateService->getTaskExecState ( $id );
        if (empty ( $rows )) {
            $user = $request->user ();
            $atService = new ApiTaskService();
            $atService->update ( $id, $task, $user );
        } else
            return "{success:0,error:'任务正在运行，已锁定...'}";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request, $id) {
        //
        $user = $request->user ();
        $atService = new ApiTaskService();
        $atService->delete ( $id, $user );
    }

    /**
     *
     * @param Request $request
     */
    public function getTaskList(Request $request) {
        $proService = new ProjectService ();
        $wl = $proService->getProjectDataAuth ();
        $secho = $request->input ( 'sEcho' );
        $iDisplayStart = $request->input ( 'iDisplayStart' );
        $iDisplayLength = $request->input ( 'iDisplayLength' );
        $search = json_decode ( $request->input ( 'search' ), true );
        $user = $request->user ();
        $atService = new ApiTaskService();
        return $atService->getTaskList ( $secho, $iDisplayStart, $iDisplayLength, $user, $search ,$wl);
    }


    //接口任务运行方法
    public  function apitask(Request $request){
        //特殊方法:在执行案例运行之前必须复制easyTestApi中的SRC文件至新创建的运行任务中,
        //也就是根据案例运行时生成的文件目录,比如案例id为541,
        //关联查询出task表中的taskid为690,
        //690就是案例运行时生成的文件夹名称
        $apitaskid=$request->input('id');
        $row=$request->input('row');
        //$apitaskexecid=$row['id'];
        $query=new ApiTaskRunService();
            if($apitaskid){
                $apirun=$query->taskexecrun($apitaskid);
                $apitaskexecid=$query->apitaskexecid($apitaskid);
                //$apitaskexecid=$apitaskexecid[0]->id;
            }

        exec("cmd /c python D:/ApacheAppServ/www/testworm/database/schemes/$apitaskid/script/runTest.py",$Array,$ret);
//        //根据日期,调用自写日志读取插件获取最新日志
//        $time=date("Ymd");
//        $dir="C:wamp/wamp/www/testworm/database/schemes/$apitaskid/script/report/log/$time/";
//        $files = $this->lastFile($dir,1);
//        //var_export($files);
//        $logname=$files[0];
//        //调用读取日志插件,把最新日志读取至数据库
//        FileHelper::resource_copy ( $dir.$logname, "C:wamp/www/testworm/public/apilog/$apitaskid-$apitaskexecid.log" );
//        //日志生成完毕后需逐条解析,获取运行时间,包名等信息,根据jobID.
//        if (file_exists("D:/ApacheAppServ/www/testworm/public/apilog/$apitaskid-$apitaskexecid.log")) {
//
//
//            //更改任务状态并发送邮件
//            //$this->updateAppExecState($intExecTaskID,2);
//            //$this->sendAppEamil($intExecTaskID, $browserID);
//            $file = fopen("D:/ApacheAppServ/www/testworm/public/apilog/$apitaskid-$apitaskexecid.log", "r", "w");
//            $user = array();
//            $i = 0;
//            //输出文本中所有的行，直到文件结束为止。
//            while (!feof($file)) {
//                $user[$i] = fgets($file);//fgets()函数从文件指针中读取一行
//                $i++;
//            }
//            fclose($file);
//            $user = array_filter($user);
//            $logtime = date("Y_m_d_H_i_s", time());
//            foreach ($user as $log){
//                $query = DB::insert("INSERT into api_logs(apisehemesid,apitaskid,apitaskexecid,apisehemename,log,time) VALUES('','$apitaskid','$apitaskexecid','任务','$log','$logtime')");
//            }
            $thisapitaskexecid=$apitaskexecid[0]->id;
            $checkErrorQuesy=new ApiTaskRunService();
            $checkError=$checkErrorQuesy->checkIfError($thisapitaskexecid);
			$arr=[];
            if(empty($checkError)){
                $apistart=new ApiTaskRunService();
                $apiret=$apistart->taskexecstart($apitaskid);
                if($apiret){
                    $arr['success']=1;
                }
            }else{
                $apiend=new ApiTaskRunService();
                $apiendret=$apiend->taskexecend($apitaskid);
                if($apiendret){
                    $arr['success']=0;
                }
            }
            echo json_encode($arr);

    }
//    public function insetrlogonce(){
//        $query=new ApiSchemeRunService();
//        $res=$query->insertlog();
//        if($res){
//            echo "success";
//        }else{
//            echo "fail";
//        }
//    }

    public function apitasklog(Request $request){
        $apitaskid=$request->input('id');
        $row=$request->input('row');
        $apitaskexecid=$row['taskExecID'];
        $query=new ApiTaskRunService();
        $checklog=$query->checklog($apitaskid,$apitaskexecid);
		$res = array();
        if($checklog){
            $res=$query->apisehemeslog($apitaskid,$apitaskexecid);
        }else{
            $ret=$query->writelog($apitaskid,$apitaskexecid);
            if($ret){
                $res=$query->apisehemeslog($apitaskid,$apitaskexecid);
            }
        }
        if($res){
            foreach ($res as $row){
                $data[]=$row;
                $arr['success']=1;
                $arr['data']=$data;
            }
        }else{
            $arr['success']=0;
            $arr['data']="";
        }
        echo json_encode($arr);

    }
	
	    public function getSchemeLog($taskexecid){
        $query = new ApiTaskRunService();
        $params = $query ->getLog($taskexecid);

        $data['chrTaskName'] = $params['task_name']->chrTaskName;
//            $data['task_list'] = array_get($params,'task_list',[]);
        $data['task_list'] = array_get($params, 'task_list', []);
        $data['scheme_list'] = array_get($params, 'scheme_list', []);
        $data['script_sum'] = array_get($params, 'script_sum', []);
        $data['project_sum'] = array_get($params, 'project_sum', []);
        $data['exec_time'] = array_get($params, 'exec_time');
        $data['script_info'] = array_get($params, 'script_info');

        return  view("api.report.apiTaskReport")-> with("data",$data);


    }

    public function getTaskPies(Request $request)
    {
        $user = $request->user ();
        $taskId=$request->input('taskId');
        $rcService = new ReportChartService ();
        $rows = $rcService->getWebTaskPie_task ( $user,$taskId );
        return "{success:1,data:{web:'" . json_encode ( $rows ) . "'}}";
    }
	
	
	
	
	    //接口测试完成后先发送此邮件，邮件中包含测试报告链接。
    public function getApiReport()
    {
        $data['taskid'] = '763';
        $data['taskexecid'] = '1080';
        $emailInfo = [
            'to' => 'zhengzt@yonyou.com',
            'subject' => '用友云测报告',
            'attach' => "E:/wamp/wamp/www/testworm/public/apilog/693-810.log"
        ];
        EmailHelper::sendEmail('api.report.hrefTobroswer',$data,$emailInfo);
    }

    public function newApiReport(Request $request)
    {
        $taskid=$request->input('taskid');
        $taskexecid=$request->input('taskexecid');
        $query = new ApiTaskRunService();
        $params = $query ->getLogForNew($taskid,$taskexecid);

        $data['chrTaskName'] = $params['task_name']->chrTaskName;
//            $data['task_list'] = array_get($params,'task_list',[]);
        $data['task_list'] = array_get($params, 'task_list', []);
        $data['scheme_list'] = array_get($params, 'scheme_list', []);
        $data['script_sum'] = array_get($params, 'script_sum', []);
        $data['project_sum'] = array_get($params, 'project_sum', []);
        $data['exec_time'] = array_get($params, 'exec_time');
        $data['script_info'] = array_get($params, 'script_info');

        //EmailHelper::sendEmail('api.report.apiReportNew',$data,$emailInfo);
        return view('api.report.apiReportNew')-> with("data",$data);
    }


    public function getAllReportInfo(Request $request)
    {
        $taskId=$request->input('taskId');
        $taskexecid=$request->input('taskexecid');
        $rcService = new ApiTaskRunService ();
        $params = $rcService->getAllInfo ( $taskId,$taskexecid );
        $data['task_list'] = array_get($params, 'task_list', []);
        $data['scheme_list'] = array_get($params, 'scheme_list', []);
        $data['script_info'] = array_get($params, 'script_info');
        return "{success:1,data:{task:'" . json_encode ( $data['task_list'] ) . "',scheme:'" . json_encode ( $data['scheme_list'] ) . "',script:'" . json_encode ( $data['script_info'] ) . "'}}";
    }


    public function getAllTaskInfoSuccess(Request $request)
    {
        $taskId=$request->input('taskId');
        $taskexecid=$request->input('taskexecid');
        $rcService = new ApiTaskRunService ();
        $params = $rcService->getAllInfoSuccess ( $taskId,$taskexecid );
        $data['task_list'] = array_get($params, 'task_list', []);
        $data['scheme_list'] = array_get($params, 'scheme_list', []);
        $data['task_times'] = array_get($params, 'task_times', []);
        $data['scheme_times'] = array_get($params, 'scheme_times', []);
        return "{success:1,data:{task:'" . json_encode ( $data['task_list'] ) . "',scheme:'" . json_encode ( $data['scheme_list'] ) . "',task_times:'" . json_encode ( $data['task_times'] ) . "',scheme_times:'" . json_encode ( $data['scheme_times'] ) . "'}}";
    }
	
	
	
	
	

}
