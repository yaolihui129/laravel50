<?php namespace App\Http\Controllers\Resource\Web;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Services\ApiTaskRunService;
use App\Services\ApiTaskService;
use App\Services\ProjectService;
use App\Services\ResourcePlanService;
use App\Services\SysDictService;
use App\Utils\EmailHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ResourcePlanController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        //
        $user = $request->user();
        $proService = new ResourcePlanService();
        $projects = $proService->getProjectTree($user);
        //$sdService = new SysDictService ();
        $states = $proService->getTaskExecState();
        $pages = array(
            "projects" => $projects,
            "states" => $states
        );
        return view("resource.web.resourcetaskPlan")->with($pages);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //
        $user = $request->user();
        $task = $request->all();
        $atService = new ResourcePlanService();
        $atService->insert($task, $user);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit(Request $request, $id)
    {
        //
        $user = $request->user();
        $atService = new ResourcePlanService();
        $task = $atService->getTaskById($id, $user);
        if($task==0){
            return "{success:0,msg:" . '任务正在运行，无法编辑'. "}";
        }else {
            return "{success:1,task:" . json_encode($task) . "}";
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
        $task = $request->all();
        $ateService = new ResourcePlanService();
        $rows = $ateService->getTaskExecState($id);
        if (empty ($rows)) {
            $user = $request->user();
            $atService = new ResourcePlanService();
            $atService->update($id, $task, $user);
        } else
            return "{success:0,error:'任务正在运行，已锁定...'}";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        //
        $user = $request->user();
        $atService = new ResourcePlanService();
        $atService->delete($id, $user);
    }

    /**
     *
     * @param Request $request
     */
    public function getTaskList(Request $request)
    {
        $proService = new ResourcePlanService ();
        $wl = $proService->getProjectDataAuth();
        $secho = $request->input('sEcho');
        $iDisplayStart = $request->input('iDisplayStart');
        $iDisplayLength = $request->input('iDisplayLength');
        $search = json_decode($request->input('search'), true);
        $user = $request->user();
        //$atService = new ApiTaskService();
        return $proService->getTaskList($secho, $iDisplayStart, $iDisplayLength, $user, $search, $wl);
    }


    //任务运行方法
    public function exec(Request $request, $id)
    {
        $query = new ResourcePlanService();
        $res = $query->checkPlanState($id);
        if ($res == 1) { // 若不存在正在指定的 state=1 正在执行 0 不存在
            return "{success:0,error:'案例正在运行，已锁定...'}";
        } else if ($res >= 3) {
            return "{success:0,error:'资源正在运行，已锁定，请等待资源释放。'}";
        } else if($res == 2) {
            return "{success:1}";
        }


    }

    public function apitask(Request $request)
    {
        $planId = $request->input('id');
        $emails = $request->input('emails');
        $query = new ResourcePlanService();
        $res = $query->planRun($planId, $emails);
        if ($res) {
            $arr['success'] = 1;
        } else {
            $arr['success'] = 0;
        }
        return json_encode($arr);
    }

    public function apitasklog(Request $request)
    {
        $apitaskid = $request->input('id');
        $row = $request->input('row');
        $apitaskexecid = $row['taskExecID'];
        $query = new ResourcePlanService();
        $checklog = $query->checklog($apitaskid, $apitaskexecid);
        if ($checklog) {
            $res = $query->apisehemeslog($apitaskid, $apitaskexecid);
        } else {
            $ret = $query->writelog($apitaskid, $apitaskexecid);
            if ($ret) {
                $res = $query->apisehemeslog($apitaskid, $apitaskexecid);
            }
        }
        if ($res) {
            foreach ($res as $row) {
                $data[] = $row;
                $arr['success'] = 1;
                $arr['data'] = $data;
            }
        } else {
            $arr['success'] = 0;
            $arr['data'] = "";
        }
        echo json_encode($arr);

    }


    public function checkAdd(Request $request)
    {
        $taskName = $request->input('taskName');
        $MacIP = $request->input('MacIP');
        $belongTo = $request->input('belongTo');
        $query = new ResourcePlanService();
        $res = $query->checkAdd($taskName, $MacIP, $belongTo);
        if ($res == 1) {
            $arr['success'] = 1;
            $arr['data'] = '已存在资源名称，请重新填写';
        } else if ($res == 2) {
            $arr['success'] = 2;
            $arr['data'] = '已存在资源IP，请重新填写';
        } else if ($res == 0) {
            $arr['success'] = 0;
        }
        echo json_encode($arr);
    }


    public function checkDel(Request $request)
    {
        $id = $request->input('id');
        $query = new ResourcePlanService();
        $res = $query->checkDel($id);
        if (empty($res)) {
            $arr['success'] = 1;
        } else {
            $arr['success'] = 0;
        }
        echo json_encode($arr);
    }

    public function checkEdit(Request $request)
    {
        $id = $request->input('id');
        $query = new ResourcePlanService();
        $res = $query->checkEdit($id);
        if (empty($res)) {
            $arr['success'] = 1;
        } else {
            $arr['success'] = 0;
        }
        echo json_encode($arr);
    }


    public function getMachineAndStep(Request $request)
    {
        $proService = new ProjectService ();
        $wl = $proService->getProjectDataAuth();
        $secho = $request->input('sEcho');
        $iDisplayStart = $request->input('iDisplayStart');
        $iDisplayLength = $request->input('iDisplayLength');
        $search = json_decode($request->input('search'), true);
        $user = $request->user();
        $ascService = new ResourcePlanService();
        return $ascService->getSchemeList($secho, $iDisplayStart, $iDisplayLength, $user, $search, $wl);
    }


    public function getData()
    {
        $query = new ResourcePlanService();
        $res = $query->getData();
//        $resStepTaskId=$query->getDataStepTaskId();
//        $arrStepTaskId=array();
//        foreach ($resStepTaskId as $resStepTaskIds){
//            foreach ($resStepTaskIds as $vs){
//                $ret=explode(',',$vs);
//                foreach ($ret as $v){
//
//                }
//            }
//        }
//        $resStepTaskName=$query->getDataStepTaskName();
//        $arrStepTaskName=array();
//        foreach ($resStepTaskName as $resStepTaskNames){
//            foreach ($resStepTaskNames as $s){
//                $arrStepTaskName['taskID']=explode(',',$s);
//            }
//        }
//        $resStepTaskMethod=$query->getDataStepTaskMethod();
//        $arrStepTaskMethod=array();
//        foreach ($resStepTaskMethod as $resStepTaskMethods){
//            foreach ($resStepTaskMethods as $s){
//                $arrStepTaskMethod['taskID']=explode(',',$s);
//            }
//        }


        $arr = array();
        if ($res) {
            foreach ($res as $row) {
                //$row->step=$resStepTaskId;
                $data[] = $row;
                $arr['success'] = 1;
                $arr['data'] = $data;

            }
        } else {
            $arr['success'] = 0;
            $arr['data'] = "";
        }
        echo json_encode($arr);

    }


    public function getDataBack(Request $request)
    {
        $planId = $request->input('planId');
        $machineId = $request->input('machineId');
        $state = $request->input('state');
        $error = $request->input('error');
        $final = $request->input('final');
        $pct=$request->input('pct');
        Log::info("返回信息成功——————");
        Log::info("planId：" . $planId);
        Log::info("machineId：" . $machineId);
        Log::info("state：" . $state);
        Log::info("error：" . $error);
        Log::info("final：" . $final);
        Log::info("pct".$pct);
        $query = new ResourcePlanService();
        $res = $query->backForUpdatePlanSteate($planId, $machineId, $state, $error, $final,$pct);

        if($final == 1){
            $data=$this->getLogForEmail($planId);
            $emailData = array(
                "data" => $data,
            );
            $emails=$query->getEmailTo($planId);
            $emailsArray=explode(';',$emails);
            if(count($emailsArray)>1){
                $emailInfo = array(
                    "to" => $emailsArray,
                    "subject" => "调度中心运行报告"
                );
            }else{
                $emailInfo = array(
                    "to" => $emails,
                    "subject" => "调度中心运行报告"
                );
            }

            EmailHelper::sendEmail("resource.web.logInfo",$emailData,$emailInfo);
        }
        if ($res) {
            Log::info("变更资源及任务状态成功——————");
            return 'success';
        }

    }

    public function getLogForEmail($planId)
    {
        $query = new ResourcePlanService();
        $params = $query->getLog($planId);
        $data['chrTaskName'] = $params['task_name'];
        $data['task_list'] = array_get($params, 'task_list', []);
        $data['scheme_list'] = array_get($params, 'scheme_list', []);
        $data['script_sum'] = array_get($params, 'script_sum', []);
        $data['project_sum'] = array_get($params, 'project_sum', []);

        foreach ($data['scheme_list'] as $scheme) {
            Log::info('chrTaskName:' . $scheme->chrTaskName);
            Log::info('schemeName:' . $scheme->schemeName);
            Log::info('projectName:' . $scheme->projectName);
            Log::info('createUser:' . $scheme->createUser);
            Log::info('state:' . $scheme->state);
            Log::info('browserNames:' . $scheme->browserNames);
        }

        return ($data);

    }



    public function logInfo($planId)
    {
        $query = new ResourcePlanService();
        $params = $query->getLog($planId);
        $data['chrTaskName'] = $params['task_name'];
        $data['task_list'] = array_get($params, 'task_list', []);
        $data['scheme_list'] = array_get($params, 'scheme_list', []);
        $data['script_sum'] = array_get($params, 'script_sum', []);
        $data['project_sum'] = array_get($params, 'project_sum', []);

        foreach ($data['scheme_list'] as $scheme) {
            Log::info('chrTaskName:' . $scheme->chrTaskName);
            Log::info('schemeName:' . $scheme->schemeName);
            Log::info('projectName:' . $scheme->projectName);
            Log::info('createUser:' . $scheme->createUser);
            Log::info('state:' . $scheme->state);
            Log::info('browserNames:' . $scheme->browserNames);
        }

        return view("resource.web.logInfo")->with("data", $data);

    }


    public function editUpdate(Request $request)
    {
        $task=$request->all();

        $planName=$task['taskName'];
        $machineIdAll=$task['machineIds'];
        $oldMachineIds=$task['oldMachineIds'];
        $planId=$task['planId'];
        $steps=$task['step'];

        $query=new ResourcePlanService();
        $res=$query->editUpdateQuery($planId,$planName,$machineIdAll,$steps);
        //return 'success';
    }



    public function editDelete(Request $request)
    {
        $planId=$request->input('planId');
        $machineId=$request->input('machineId');
        $query=new ResourcePlanService();
        $res=$query->deletePlanChildren($planId,$machineId,'123');
        if($res){
            $arr['success']=1;
        }else{
            $arr['success']=0;
        }
        return json_encode($arr);


    }

    public function stop(Request $request,$id)
    {
        $planId=$id;
        $query = new ResourcePlanService();
        $res = $query->stopRun($planId);
        if ($res==1) {
            $arr['success'] = 1;
            $arr['msg']='任务已停止';
        } else if($res==2){
            $arr['success'] = 0;
            $arr['msg']='任务停止失败,当前任务未在执行状态，请执行后在停止';
        }
        return json_encode($arr);
    }






}

