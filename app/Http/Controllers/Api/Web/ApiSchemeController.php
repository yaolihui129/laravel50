<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\ApiBrowserService;
use App\Services\ApiSchemeRunService;
use App\Services\ApiSchemeService;
use App\Services\ApiTaskExecService;
use Illuminate\Http\Request;
use App\Services\FlowProcessService;
use App\Services\FlowService;
use Illuminate\Support\Facades\Auth;
use App\Services\AutoSchemeService;
use App\Utils\HttpHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\AutoBrowserService;
use App\Services\AutoTaskService;
use App\Services\AutoTaskExecService;
use App\Services\ProjectService;
use App\Services\SysDictService;
use App\Utils\FileHelper;
use App\Services\ApiTaskRunService;
use App\Services\ReportChartService;

class ApiSchemeController extends Controller {
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
        return view ( "api.web.apischeme" )->with ( $pages );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create(Request $request) {
        //
        $ascService = new ApiSchemeService();
        $schemes = array (
            "schemeName" => $request->input ( 'schemeName' )  // "案例" . time ()
        );
        $user = $request->user ();
        $schemeId = $ascService->insert ( $schemes, $user );
        $pages = array (
            "schemeid" => $schemeId,
            "opt" => 1
        );
        return view ( 'api.web.apischeme_edit' )->with ( $pages );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store() {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id) {
        //
        return view ( 'api.web.apischeme_edit' );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id) {
        $pages = array (
            "schemeid" => $id,
            "opt" => 2
        );
        return view ( 'api.web.apischeme_edit' )->with ( $pages );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return Response
     */
    public function update($id) {
        //
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
        $ascService = new ApiSchemeService();
        $ascService->delete ( $id, $user );
    }

    /**
     * 获取案例列表F
     *
     * @param Request $request
     */
    public function getSchemeList(Request $request) {
        $proService = new ProjectService ();
        $wl = $proService->getProjectDataAuth ();
        $secho = $request->input ( 'sEcho' );
        $iDisplayStart = $request->input ( 'iDisplayStart' );
        $iDisplayLength = $request->input ( 'iDisplayLength' );
        $search = json_decode ( $request->input ( 'search' ), true );
        $user = $request->user ();
        $ascService = new ApiSchemeService();
        return $ascService->getSchemeList ( $secho, $iDisplayStart, $iDisplayLength, $user, $search, $wl );
    }

    /**
     * 获取流程脚本
     *
     * @param Request $request
     * @return string
     */
    public function getFlowProcess(Request $request, $id) {
        $user = $request->user ();
        $ascService = new ApiSchemeService();
        $processData = $ascService->getSchemeFlowProcess ( $id, $user );
        return "{success:1,processData:" . json_encode ( $processData ) . "}";
    }

    /**
     * 添加流程单
     *
     * @param Request $request
     */
    public function addFlow(Request $request) {
        $user = $request->user ();
        $flows = $request->all ();
        $flowService = new FlowService ();
        $flowService->addFlow ( $flows, $user );
    }

    /**
     * 保存流程步骤面板的所有信息
     *
     * @param Request $request
     */
    public function saveFlowProcess(Request $request, $id) {
        $user = $request->user ();
        $ascService = new ApiSchemeService();
        $projectId = $request->input ( 'projectId' );
        // 案例相关
        $ascService->update ( $projectId, $id, $user );
        // 流程相关
        $flowprocess = $request->input ( 'flowprocess' );
        $flowprocess = json_decode ( $flowprocess, true );
        $fproService = new FlowProcessService ();
        foreach ( $flowprocess as $processid => $process ) {
            foreach ( $process ['process_to'] as $key => $pro ) {
                if (! array_key_exists ( $pro, $flowprocess )) {
                    unset ( $process ['process_to'] [$key] );
                }
            }
            array_filter ( $process ["process_to"] );
            $process ['processTo'] = implode ( ",", $process ["process_to"] );
            $fproService->updateProcess ( $process, $processid, $user ); // 更新流程步骤的信息
        }
        $selBrowsers = $request->input ( 'selBrowsers' );
        $abService = new ApiBrowserService();
        $allBrowsers = $abService->getBrowsers ();
        $ascService->makeSchemePackage ( $id, $selBrowsers, $allBrowsers, $user );
        return "{success:1}";
    }

    /**
     * 添加步骤
     *
     * @param Request $request
     */
    public function addProcess(Request $request) {
        $process = $request->all ();
        $user = $request->user ();
        $fproService = new FlowProcessService ();
        $processinfo = $fproService->addProcess ( $process, $user );
        return "{success:1,info:" . json_encode ( $processinfo ) . "}";
    }

    /**
     *
     * @param Request $request
     */
    public function delProcess(Request $request) {
        $user = $request->user ();
        $processid = $request->input ( 'processid' );
        $fproService = new FlowProcessService ();
        $fproService->delProcess ( $processid, $user );
    }

    /**
     * 下载案例
     *
     * @param Request $request
     */
    public function downScheme(Request $request, $id) {
        $user = $request->user ();
        $ascService = new ApiSchemeService();
        if (! $_GET ["down"]) { // 是否为单纯的下载 即是否通过表单下载
            $selBrowsers = $request->input ( 'selBrowsers' );
            $abService = new ApiBrowserService();
            $allBrowsers = $abService->getBrowsers ();
            $ascService->makeSchemePackage ( $id, $selBrowsers, $allBrowsers, $user );
        }
        $ascService->makeSchemeZip ( $id, $user );
        $filename = database_path ( 'schemes/zips/' ) . $id . ".zip";
        HttpHelper::download ( $filename );
    }

    /**
     *
     * @param Request $request
     * @param unknown $id
     */
    public function exec(Request $request, $id) {
        $selBrowsers = $request->input ( 'selBrowsers' );
        $ascService = new ApiSchemeService();
        $user = Auth::user ();
        $ateService = new ApiTaskExecService();
        $row = $ateService->getSchemeExecState ( $id, $user ); // 获取是否有正在执行的单独案例
        if (! $row->state) { // 若不存在正在指定的 state=1 正在执行 0 不存在
            $execInfo = array (
                "taskId" => $row->taskID,
                "selBrowsers" => $selBrowsers,
                "emails" => ""
            );
            $ret = $ateService->insert ( $execInfo, $user );
            return "{success:1}";
        } else
            return "{success:0,error:'案例正在运行，已锁定...'}";
    }


    //接口案例运行方法
    public  function apisehemes(Request $request){
        //特殊方法:在执行案例运行之前必须复制easyTestApi中的SRC文件至新创建的运行任务中,
        //也就是根据案例运行时生成的文件目录,比如案例id为541,
        //关联查询出task表中的taskid为690,
        //690就是案例运行时生成的文件夹名称
        $apisehemesid=$request->input('id');
        $query=new ApiSchemeRunService();
        $apisehemename=$query->sehemesname($apisehemesid);
        if($apisehemename){
            $apisehemename=$apisehemename[0]->chrSchemeName;
            $apitaskid=$query->taskid($apisehemename);
            $apitaskid=$apitaskid[0]->id;
            if($apitaskid){
                $apirun=$query->taskexecrun($apitaskid);
                $apitaskexecid=$query->apitaskexecid($apitaskid);
//                $apitaskexecid=$apitaskexecid[0]->id;
            }
        }
        exec("cmd /c python D:/ApacheAppServ/www/testworm/database/schemes/$apitaskid/script/runTest.py",$Array,$ret);

		$thisapitaskexecid=$apitaskexecid[0]->id;
        $checkErrorQuesy=new ApiTaskRunService();
        $checkError=$checkErrorQuesy->checkIfError($thisapitaskexecid);
        if( empty($checkError)){
            $apistart=new ApiSchemeRunService();
            $apiret=$apistart->taskexecstart($apitaskid);
            if($apiret){
                $arr['success']=1;
            }
        }else{
            $apiend=new ApiSchemeRunService();
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

    public function apisehemeslog(Request $request){
        $apisehemesid=$request->input('id');
        $row=$request->input('row');
        $apitaskexecid=$row['taskExecID'];
        $query=new ApiSchemeRunService();
        $apitaskid=$query->apitaskidserch($apitaskexecid);
        $apitaskid=$apitaskid[0]->intTaskID;
        $checklog=$query->checklog($apisehemesid,$apitaskid,$apitaskexecid);
        if($checklog){
            $res=$query->apisehemeslog($apisehemesid,$apitaskid,$apitaskexecid);
        }else{
            $ret=$query->writelog($apisehemesid,$apitaskid,$apitaskexecid);
            if($ret){
                $res=$query->apisehemeslog($apisehemesid,$apitaskid,$apitaskexecid);
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

    //生成日志读取插件
    public function lastFile($dir, $lastNum = 1)
    {
        $lastNum = intval($lastNum);
        if (!is_dir($dir) || $lastNum <= 0) return false;

        $files = scandir($dir);
        $return_files = [];

        foreach ($files as $file) {
            if (preg_match('#^(\d{14})\.log#', $file, $res)) {
                $return_files[] = $res[1];
            }
        }
        if (!$return_files) return false;

        rsort($return_files);

        $return_files = array_slice($return_files, 0, $lastNum);
        foreach ($return_files as &$file) {
            $file .= '.log';
        }
        return $return_files;
    }
	    public function getSchemeLog($taskexecid){
        $query = new ApiSchemeRunService();
        $params = $query ->getLog($taskexecid);

        $data['chrTaskName'] = $params['task_name']->chrTaskName;
//            $data['task_list'] = array_get($params,'task_list',[]);
        $data['task_list'] = array_get($params, 'task_list', []);
        $data['scheme_list'] = array_get($params, 'scheme_list', []);
        $data['script_sum'] = array_get($params, 'script_sum', []);
        $data['project_sum'] = array_get($params, 'project_sum', []);
        $data['exec_time'] = array_get($params, 'exec_time');
        $data['script_info'] = array_get($params, 'script_info');

        return  view("api.report.apiReport")-> with("data",$data);


    }

    public function getTaskPies(Request $request)
    {
        $user = $request->user ();
        $taskId=$request->input('taskId');
        $rcService = new ReportChartService ();
        $rows = $rcService->getWebTaskPie ( $user,$taskId );
        return "{success:1,data:{web:'" . json_encode ( $rows ) . "'}}";
    }



}