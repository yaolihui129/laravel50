<?php

namespace App\Services;

use App\AutoTask;
use App\AutoTaskRelation;
use Illuminate\Support\Facades\DB;
use App\SysMachine;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ResourcePlanService
{

    /**
     * 获取任务总数
     */
    private function getTaskCount($user, $search, $wl)
    {
        $rows = DB::select("select COUNT(*) allCount  from resource_plans where  $search");
        return $rows [0]->allCount;
    }

    /**
     *
     * @param unknown $search
     */
    private function structureSearchSQL($search)
    {
        if (!empty ($search)) {
            $sql = array();
//            $projectId = $search ["projectId"];
            $state = $search ["state"];
            $taskName = trim($search ["taskName"]);
//            $creater = trim($search ["creater"]);
//            if ($projectId) {
//                array_push($sql, "FIND_IN_SET('$projectId', chrMachineId)");
//            }
//            if ($creater) {
//                array_push($sql, "chrMachineMacIP like '%$creater%'");
//            }
            if ($state === "") {
                array_push($sql, "chrNowState is NULL");
            } else if ($state >= 0)
                array_push($sql, "chrNowState=$state");
            if ($taskName)
                array_push($sql, "chrTaskName='$taskName'");
            $sql = implode(" and ", $sql);
        }
        if (empty ($sql))
            $sql = "1=1";
        return $sql;
    }

    /**
     * 获取任务列表信息
     *
     * @param unknown $secho
     * @param unknown $iDisplayStart
     * @param unknown $iDisplayLength
     */
    public function getTaskList($secho, $iDisplayStart, $iDisplayLength, $user, $search, $wl)
    {
        $search = $this->structureSearchSQL($search);
        $allcount = $this->getTaskCount($user, $search, $wl);
        $tasks = "{'sEcho': " . $secho . ",'iTotalRecords': " . $allcount . ",'iTotalDisplayRecords':" . $allcount . ",'aaData': ";
        $rows = DB::select("select 
id,
chrTaskName,
case when chrNowState=0 then '排队中' when chrNowState=1 then '执行中' when chrNowState=2 then '执行成功' 
				when chrNowState=3 then '执行失败' else '未执行' end state,
updated_at
from resource_plans
				where  $search ORDER BY updated_at desc  
				limit ?,?", [
            $iDisplayStart,
            $iDisplayLength
        ]);
        $tasks .= json_encode($rows);
        $tasks .= "}";
        return $tasks;
    }

    /**
     * 获取任务
     *
     * @param unknown $id
     */
    public function getTaskById($id, $user)
    {
//        $rows = DB::select("select ats.id,ats.chrTaskName,atr.intSchemeID,aus.chrSchemeName,
//		ats.intProjectID projectId,apro.chrProjectName projectName from  auto_tasks ats
//		INNER JOIN auto_task_relations atr on atr.intTaskID=ats.id
//		INNER JOIN auto_schemes aus on aus.id=atr.intSchemeID
//		INNER JOIN auto_projects apro on apro.id=ats.intProjectID
//		where ats.id='759' ");
        $run=DB::select("select chrNowState from resource_plans where id='$id'");
        $ifRun=$run[0]->chrNowState;
        if($ifRun==1){
            return 0;
        }else{
            $rows=DB::select("select 
a.id ,
a.chrTaskName,
b.chrMachineId as intSchemeID,
b.chrMachineName as chrSchemeName,
b.chrStepIdForY as projectId,
b.chrStepName as projectName
from resource_plans a
LEFT JOIN resource_plans_children  b ON b.chrTaskId=a.id
where a.id='$id'");
            $task = array(
                "taskName" => $rows [0]->chrTaskName,
                "projectId" => $rows [0]->projectId
            );
            foreach ($rows as $row) {
                $scheme = array(
                    "id" => $row->intSchemeID,
                    "chrMachName" => $row->chrSchemeName,
                    "projectId" => $row->projectId
                );
                $task ["schemes"] [] = $scheme;
            }
        }

        return $task;
    }

    /**
     *
     * @param unknown $task
     */
    public function insert($task, $user)
    {
        DB::beginTransaction();
        try {
            $taskName = $task['taskName'];
            $machineId = $task['machineId'];
            $stepId = $task['stepId'];
            $time = date('Y-m-d H:i:s');
            $checkMachineId = implode(',', $machineId);
            //$checkStepId=implode(',',$stepId);
            //resource_plans去重
            $check = DB::select("select chrTaskName,chrMachineId,chrNowState,created_at,updated_at from resource_plans
where chrTaskName='$taskName'
and chrMachineId ='$checkMachineId'
and chrNowState =2
");
            //insert into resource_plans
            if (empty($check)) {
                $insertPlan = DB::insert("insert into resource_plans 
(chrTaskName,chrMachineId,chrNowState,created_at,updated_at) 
VALUES('$taskName','$checkMachineId',2,'$time','$time')");
            }


            //准备insertPlanChildren 数据
            $chrTask = DB::select("select id from resource_plans where chrTaskName='$taskName'");
            $chrTaskId = $chrTask[0]->id;


            //第一种逻辑，双层循环获取数据
//            foreach ($machineId as $machineIds){
//                $machine=DB::select("select id,chrMachName,chrMacIp from resource_machines where id= '$machineIds'");
//                $chrMachineId=$machine[0]->id;
//                $chrMachineName=$machine[0]->chrMachName;
//                $chrMachineMacIP=$machine[0]->chrMacIp;
//
//                //循环获取step数据
//                foreach ($stepId as $stepIds) {
//                    $v = implode(',', $stepIds);
//                    $step = DB::select("select
//GROUP_CONCAT(id) as id,
//GROUP_CONCAT(chrStepId) as chrStepId,
//GROUP_CONCAT(chrStepName) as chrStepName,
//GROUP_CONCAT(chrStepFun) as chrStepFun
//from resource_steps where chrStepId in ($v)");
//                    $chrStepId = $step[0]->id;
//                    $chrStepIdForY = $step[0]->chrStepId;
//                    $chrStepName = $step[0]->chrStepName;
//                    $chrStepFun = $step[0]->chrStepFun;
//
//                }
//                $insertPlanChildren = DB::insert("insert into resource_plans_children
//(chrTaskId,chrMachineId,chrMachineName,chrMachineMacIP,chrStepId,chrStepIdForY,chrStepName,chrStepFun,created_at,updated_at)
//VALUES('$chrTaskId','$chrMachineId','$chrMachineName','$chrMachineMacIP','$chrStepId','$chrStepIdForY','$chrStepName','$chrStepFun','$time','$time')");
//
//            }


            //第二种逻辑，两个数组，单独循环获取，之后合并，一个当键，一个当值
            //获取资源数据
            $arrayMachineKey = array();
            $arrayMachine = array();
            $arrayMachineAll = array();
            $arrayStep = array();
            $arrayStepAll = array();
            foreach ($machineId as $machineIds) {
                $machine = DB::select("select id,chrMachName,chrMacIp,hostIp,vmName,userName,password from resource_machines where id= '$machineIds'");
                $chrMachineId = $machine[0]->id;
                $chrMachineName = $machine[0]->chrMachName;
                $chrMachineMacIP = $machine[0]->chrMacIp;
                $chrhostIp=$machine[0]->hostIp;
                $chrvmName=$machine[0]->vmName;
                $chruserName=$machine[0]->userName;
                $chrpassword=$machine[0]->password;
                array_push($arrayMachineKey, $machineIds);
                $arrayMachine['chrMachineId'] = $chrMachineId;
                $arrayMachine['chrMachineName'] = $chrMachineName;
                $arrayMachine['chrMachineMacIP'] = $chrMachineMacIP;

                $arrayMachine['chrhostIp'] = $chrhostIp;
                $arrayMachine['chrvmName'] = $chrvmName;
                $arrayMachine['chruserName'] = $chruserName;
                $arrayMachine['chrpassword'] = $chrpassword;
                //array_push($arrayMachineKey,$arrayMachine);

                array_push($arrayMachineAll, $arrayMachine);
            }
            //循环获取step数据

            foreach ($stepId as $stepIds) {
//                $v = implode(',', $stepIds);
//                $step = DB::select("select
//GROUP_CONCAT(id) as id,
//GROUP_CONCAT(chrStepId) as chrStepId,
//GROUP_CONCAT(chrStepName) as chrStepName,
//GROUP_CONCAT(chrStepFun) as chrStepFun
//from resource_steps where chrStepId in ($v)");
//                $chrStepId = $step[0]->id;
//                $chrStepIdForY = $step[0]->chrStepId;
//                $chrStepName = $step[0]->chrStepName;
//                $chrStepFun = $step[0]->chrStepFun;
                $chrStepIds = '';
                $chrStepIdForYs = '';
                $chrStepNames = '';
                $chrStepFuns = '';
                foreach ($stepIds as $stepIdss){
                    $step = DB::select("select
id as id,
chrStepId as chrStepId,
chrStepName as chrStepName,
chrStepFun as chrStepFun
from resource_steps where chrStepId = $stepIdss");
                    $chrStepId = $step[0]->id;
                    $chrStepIds = $chrStepIds.$chrStepId.',';
                    $chrStepIdForY = $step[0]->chrStepId;
                    $chrStepIdForYs = $chrStepIdForYs.$chrStepIdForY.',';
                    $chrStepName = $step[0]->chrStepName;
                    $chrStepNames = $chrStepNames.$chrStepName.',';
                    $chrStepFun = $step[0]->chrStepFun;
                    $chrStepFuns = $chrStepFuns.$chrStepFun.',';
                }


                $arrayStep['chrStepId'] = $chrStepIds;
                $arrayStep['chrStepIdForY'] = $chrStepIdForYs;
                $arrayStep['chrStepName'] = $chrStepNames;
                $arrayStep['chrStepFun'] = $chrStepFuns;

                array_push($arrayStepAll, $arrayStep);
            }

            $all = array_combine($arrayMachineKey, $arrayMachineAll);


            $arrayPlan = array_combine($arrayMachineKey, $arrayStepAll);


            foreach ($all as $k1 => $v1) {
                foreach ($arrayPlan as $k2 => $v2) {
                    if ($k2 == $k1) {
                        foreach ($v2 as $k3 => $v3) {
                            $v1[$k3] = $v3;
                            $all[$k1] = $v1;

                        }

                    }
                }
            }


            foreach ($all as $k => $v) {
                $cTaskId = $chrTaskId;
                $cMachineId = $v['chrMachineId'];
                $cMachineName = $v['chrMachineName'];
                $cMachineMacIP = $v['chrMachineMacIP'];
                $chostIp=$v['chrhostIp'];
                $cvmName=$v['chrvmName'];
                $cuserName=$v['chruserName'];
                $cpassword=$v['chrpassword'];
                $cStepId = $v['chrStepId'];
                $cStepIdForY = $v['chrStepIdForY'];
                $cStepName = $v['chrStepName'];
                $cStepFun = $v['chrStepFun'];
                $insertPlanChildren = DB::insert("insert into resource_plans_children
(chrTaskId,
chrMachineId,
chrMachineName,
chrMachineMacIP,
chrhostIp,
chrvmName,
chruserName,
chrpassword,
chrStepId,
chrStepIdForY,
chrStepName,
chrStepFun,
created_at,updated_at)
VALUES('$cTaskId',
'$cMachineId',
'$cMachineName',
'$cMachineMacIP',
'$chostIp',
'$cvmName',
'$cuserName',
'$cpassword',
'$cStepId',
'$cStepIdForY',
'$cStepName',
'$cStepFun'
,'$time','$time')");

            }


            DB::commit();
            return $taskName;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     *
     * @param unknown $ids
     */
    public function delete($ids, $user)
    {
        DB::beginTransaction();
        try {
            $ids = explode(",", $ids);
            foreach ($ids as $id) {
                $res = DB::select("select chrNowState from resource_plans where id='$id'");
                $state = $res[0]->chrNowState;
                if ($state != 1) {
                    //删除plan主表数据
                    DB::delete("DELETE from resource_plans  where id in ('$id')");
                    //删除plan子表数据
                    DB::delete("DELETE FROM resource_plans_children where chrTaskId in ('$id')");
                }

            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    /**
     *
     * @param unknown $task
     * @param unknown $user
     */
    public function update($taskId, $task, $user)
    {
        DB::beginTransaction();
        try {
            DB::update("update auto_tasks set chrTaskName=?,intProjectID=?,intModifyID=? 
			where id=? and intCompanyID=$user->intCompanyID", [
                $task ["taskName"],
                $task ["projectId"],
                $user->id,
                $taskId
            ]);
            $schemes = $task ["schemes"];
            $oldSchemeIds = $task ["oldSchemeIds"];
            foreach ($schemes as $idx => $scheme) {
                if (($key = array_search($scheme, $oldSchemeIds)) === FALSE) {
                    $auto_task_rel = new AutoTaskRelation ();
                    $auto_task_rel->intTaskID = $taskId;
                    $auto_task_rel->intExecOrder = $idx;
                    $auto_task_rel->intSchemeID = $scheme;
                    $auto_task_rel->intCreaterID = $auto_task_rel->intModifyID = $user->id;
                    $auto_task_rel->intCompanyID = $user->intCompanyID;
                    $auto_task_rel->save();
                } else
                    array_splice($oldSchemeIds, $key, 1); // 删除存在的
            }
            if (!empty ($oldSchemeIds)) {
                $delIds = implode(',', $oldSchemeIds);
                DB::delete("delete from auto_task_relations where intTaskID=$taskId and intSchemeID in ($delIds) and intCompanyID=$user->intCompanyID");
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }


    public function getProjectTree($user)
    {
        //$wl = $this->getProjectDataAuth ();
        return DB::select("select id,chrMachName from resource_machines ");
    }

    public function getProjectDataAuth()
    {
        $userAuth = Session::get("auth");
        $wl = "";
        $dataAuths = $userAuth->dataAuths;
        if (!empty ($dataAuths ["PROJECT"])) {
            $wl = " and apro.id in (" . $dataAuths ["PROJECT"] . ")";
        }
        return $wl;
    }


    /**
     *
     * @return multitype:multitype:string
     */
    public function getTaskExecState()
    {
        $states = array(
            array(
                "id" => "",
                "state" => "未执行"
            ),
            array(
                "id" => "0",
                "state" => "排队中"
            ),
            array(
                "id" => "1",
                "state" => "执行中"
            ),
            array(
                "id" => "2",
                "state" => "执行成功"
            ),
            array(
                "id" => "3",
                "state" => "执行失败"
            )
        );
        return $states;
    }

    public function apitaskexecid($apitaskid)
    {
        $query = DB::select("select id from auto_task_execs where intTaskID='$apitaskid' ORDER BY id DESC LIMIT 1 ");
        return $query;
    }

    public function taskexecrun($apitaskid)
    {
        $apitaskid = DB::update("UPDATE auto_task_execs SET intState=1 where intTaskID='$apitaskid'");
        return $apitaskid;
    }

    public function checkIfError($execTaskId)
    {
        $res = DB::select("select id from api_logs
				where apitaskexecid='$execTaskId' and chrResult in ('error') limit 1");
        return $res;
    }


    public function taskexecstart($apitaskid)
    {
        $apitaskid = DB::update("UPDATE auto_task_execs SET intState=2 where intTaskID='$apitaskid'");
        return $apitaskid;
    }

    public function taskexecend($apitaskid)
    {
        $apitaskid = DB::update("UPDATE auto_task_execs SET intState=3 where intTaskID='$apitaskid'");
        return $apitaskid;
    }

    public function checklog($apitaskid, $apitaskexecid)
    {
        $query = DB::select("select log from api_logs where  apitaskid=$apitaskid  and apitaskexecid=$apitaskexecid");
        return $query;
    }

    public function apisehemeslog($apitaskid, $apitaskexecid)
    {
        $query = DB::select("select * from api_logs where apitaskid='$apitaskid' and apitaskexecid='$apitaskexecid'");
        return $query;
    }


    public function writelog($apitaskid, $apitaskexecid)
    {
        //根据日期,调用自写日志读取插件获取最新日志
        $time = date("Ymd");
        $dir = "E:/wamp/wamp/www/testworm/database/schemes/$apitaskid/script/report/log/$time/";
        $files = $this->lastFile($dir, 1);
        //var_export($files);
        $logname = $files[0];
        //调用读取日志插件,把最新日志读取至数据库
        FileHelper::resource_copy($dir . $logname, "E:/wamp/wamp/www/testworm/public/apilog/$apitaskid-$apitaskexecid.log");
        //日志生成完毕后需逐条解析,获取运行时间,包名等信息,根据jobID.
        if (file_exists("E:/wamp/wamp/www/testworm/public/apilog/$apitaskid-$apitaskexecid.log")) {


            //更改任务状态并发送邮件
            //$this->updateAppExecState($intExecTaskID,2);
            //$this->sendAppEamil($intExecTaskID, $browserID);
            $file = fopen("E:/wamp/wamp/www/testworm/public/apilog/$apitaskid-$apitaskexecid.log", "r", "w");
            $user = array();
            $i = 0;
            //输出文本中所有的行，直到文件结束为止。
            while (!feof($file)) {
                $user[$i] = fgets($file);//fgets()函数从文件指针中读取一行
                $i++;
            }
            fclose($file);
            $user = array_filter($user);
            $logtime = date("Y_m_d_H_i_s", time());
            foreach ($user as $log) {
                $query = DB::insert("INSERT into api_logs(apisehemesid,apitaskid,apitaskexecid,apisehemename,log,time) VALUES('','$apitaskid','$apitaskexecid','任务','$log','$logtime')");
            }
            if ($query) {
                return true;
            }
        }

    }


    public function checkAdd($taskName, $MacIP, $belongTo)
    {
        $ifExit_taskName = DB::select("select chrMachName from resource_machines where chrMachName='$taskName' ");
        $ifExit_MacIP = DB::select("select chrMacIp from resource_machines where chrMacIp='$MacIP' ");
        if (!empty($ifExit_taskName)) {
            $info = 1;
            return $info;
        } else if (!empty($ifExit_MacIP)) {
            $info = 2;
            return $info;
        } else {
            $info = 0;
            return $info;
        }

    }


    public function checkDel($id)
    {

        return DB::select("select chrNowState from resource_plans where id in ('$id') and chrNowState='1' or  chrNowState='0'");
    }

    public function checkEdit($id)
    {

        return DB::select("select chrNowState from resource_machines where id in ('$id') and chrNowState='1'");
    }


    public function getSchemeList($secho, $iDisplayStart, $iDisplayLength, $user, $search, $wl)
    {
//        return DB::select("select id,chrMachName,chrMacIP,
//(select GROUP_CONCAT(chrStepName) from resource_steps ) as step
//from resource_machines
//ORDER BY updated_at desc ");


        //$search = $this->structureSearchSQLNew ( $search );
        $allcount = $this->getSchemeCountNew($user, $search, $wl);
        $schemes = "{'sEcho': " . $secho . ",'iTotalRecords': " . $allcount . ",'iTotalDisplayRecords':" . $allcount . ",'aaData': ";
        $rows = DB::select("select id,chrMachName,chrMacIP,
 '1,2,3' as step,
case when chrNowState=0 then '排队中' when chrNowState=1 then '执行中' when chrNowState=2 then '执行成功' 
				when chrNowState=3 then '执行失败' else '未执行' end state
from resource_machines
ORDER BY updated_at desc limit ?,?", [
            $iDisplayStart,
            $iDisplayLength
        ]);
        $schemes .= json_encode($rows);
        $schemes .= "}";
        return $schemes;


    }

    private function structureSearchSQLNew($search)
    {
        if (!empty ($search)) {
            $sql = array();
            $projectId = $search ["projectId"];
            $state = $search ["state"];
            $schemeName = trim($search ["schemeName"]);
            $creater = trim($search ["creater"]);
            if ($projectId) {
                array_push($sql, "ats.intProjectID=$projectId");
            }
            if ($state === "") {
                array_push($sql, "ate.intState is NULL");
            } else if ($state >= 0)
                array_push($sql, "ate.intState=$state");
            if ($schemeName)
                array_push($sql, "aus.chrSchemeName='$schemeName'");
            if ($creater)
                array_push($sql, "u.chrUserName='$creater'");
            $sql = implode(" and ", $sql);
        }
        if (empty ($sql))
            $sql = "1=1";
        return $sql;
    }

    private function getSchemeCountNew($user, $search, $wl)
    {
        $res = DB::select("select COUNT(*) allcount from resource_machines");
        return $res [0]->allcount;
    }


    public function getData()
    {
        //return DB::select("select
//a.id as planId,
//chrTaskName as planName,
//b.chrMachineId as machineId,
//b.chrMachineMacIp as mac,
//b.chrhostIp as hostIp,
//b.chrvmName as vmName,
//b.chruserName as userName,
//b.chrpassword as password,
//b.chrStepIdForY as stepID,
//chrStepName as stepName,
//chrStepFun as stepMethod
//from resource_plans a
//LEFT JOIN resource_plans_children b on b.chrTaskId=a.id
//where a.chrNowState='1'");

		        return DB::select("select
a.id as planId,
chrTaskName as planName,
b.chrMachineId as machineId,
b.chrMachineMacIp as mac,
b.chrhostIp as hostIp,
b.chrvmName as vmName,
b.chruserName as userName,
b.chrpassword as password,
b.chrStepIdForY as stepID,
chrStepName as stepName,
chrStepFun as stepMethod
from resource_plans a
LEFT JOIN resource_plans_children b on b.chrTaskId=a.id
LEFT JOIN resource_machines c on c.id=b.chrMachineId
where a.chrNowState='1' and c.chrNowState ='1'");

//        return DB::select("select
//chrTaskName as planname,
//b.chrMachineMacIp as mac
//from resource_plans a
//LEFT JOIN resource_plans_children b on b.chrTaskId=a.id");
    }


    public function getDataStepTaskId()
    {
        return DB::select("select 
b.chrStepIdForY as taskID,
chrStepName as taskName,
chrStepFun as taskMethod
from resource_plans a
LEFT JOIN resource_plans_children b on b.chrTaskId=a.id");
    }

    public function getDataStepTaskName()
    {
        return DB::select("select 
chrStepName as steptaskName
from resource_plans a
LEFT JOIN resource_plans_children b on b.chrTaskId=a.id");
    }

    public function getDataStepTaskMethod()
    {
        return DB::select("select 
chrStepFun as steptaskMethod
from resource_plans a
LEFT JOIN resource_plans_children b on b.chrTaskId=a.id");
    }


    public function planRun($planId, $emails)
    {
        DB::beginTransaction();
        try {

            $chechState = DB::select("select chrNowState from resource_plans where id='$planId'");
            $chechState = $chechState[0]->chrNowState;
            $time=date('Y-m-d H:i:s');
            if ($chechState != 0) {
                //更新任务状态
                $res1 = DB::update("UPDATE resource_plans SET chrNowState ='1',chrEmails='$emails',updated_at='$time' where id='$planId'");
                //更新机器状态
                $machineIds = DB::select("select chrMachineId from resource_plans_children where chrTaskId='$planId'");
                foreach ($machineIds as $machineId) {
                    $id = $machineId->chrMachineId;
                    $checkMachineState = DB::select("select chrNowState from resource_machines where id='$id'");
                    if ($checkMachineState[0]->chrNowState != 1) {
                        $res = DB::update("UPDATE resource_machines SET chrNowState ='1' where id='$id'");
                    }
                    //再次执行任务前清空子表error及pct信息
                    $res = DB::update("UPDATE resource_plans_children 
SET chrError ='',chrPct='0'  
where chrTaskId='$planId' and chrMachineId='$id'");
                }
            }
            DB::commit();
            return $res;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }


    }

    public function checkPlanState($planId)
    {
        $machines=DB::select("select group_concat(b.chrMachineId) as machines
from 
resource_plans a
LEFT JOIN resource_plans_children b on b.chrTaskId=a.id
where a.id='$planId' ");
        $arraymachine=explode(',',$machines[0]->machines);
        $msg=2;
        foreach ($arraymachine as $machine){
            $ifMachineCanRun=DB::select("select chrNowState from resource_machines where id='$machine'");
            if($ifMachineCanRun[0]->chrNowState==1){
                $msg=3;
                $msg++;
            }
        }
        $chechState = DB::select("select chrNowState from resource_plans where id='$planId'");
        if($chechState[0]->chrNowState==1){
            $msg=1;
        }
        return $msg;
    }

    public function updatePlanEmails($planId, $emails)
    {
        $chechState = DB::select("UPDATE resource_plans SET chrEmails ='$emails' where id='$planId'");
        return $chechState;
    }


    public function backForUpdatePlanSteate($planId, $machineId, $state, $error, $final,$pct)
    {
        DB::beginTransaction();
        try {

            $chechState = DB::select("select chrNowState from resource_plans where id='$planId'");
            $chechState = $chechState[0]->chrNowState;
            $time=date('Y-m-d H:i:s');
            if ($chechState == 1) {

                //根据$final值进行判断，
                //如果为1则显示当前资源已跑完step，获取所有error信息，判断是否有失败/成功字符，之后更新表中资源状态
                //如果为0则显示当前资源step未跑完，只更新error信息。
                if ($final == 1) {
                    $ifUpdate = DB::select("select chrError from resource_plans_children where chrTaskId='$planId' and chrMachineId='$machineId'");
                    $word = $ifUpdate[0]->chrError;
                    if (strpos($word, '失败') > 0) {
                        //更新机器状态为执行失败
                        $checkMachineState = DB::select("select chrNowState from resource_machines where id='$machineId'");
                        if ($checkMachineState[0]->chrNowState == 1) {
                            DB::update("UPDATE resource_machines SET chrNowState ='3',updated_at='$time' where id='$machineId'");
                        }
                    } else {
                        //更新机器状态执行成功
                        $checkMachineState = DB::select("select chrNowState from resource_machines where id='$machineId'");
                        if ($checkMachineState[0]->chrNowState == 1) {
                            DB::update("UPDATE resource_machines SET chrNowState ='2',updated_at='$time' where id='$machineId'");
                        }
                    }
                    //更新子表返回进度信息
                    $resSql="UPDATE resource_plans_children SET chrPct='$pct',updated_at='$time'   where chrTaskId='$planId' and chrMachineId='$machineId'";
                    DB::update($resSql);

                } else if ($final == 0) {
                    //更新子表返回error和进度信息
                    $resSql="UPDATE resource_plans_children SET chrError =concat_ws(',',chrError,'$error'),chrPct='$pct',updated_at='$time'   where chrTaskId='$planId' and chrMachineId='$machineId'";
                    $update=DB::update($resSql);
                }


            }
            DB::commit();
            //获取machine状态信息
            $getMachineStates = DB::select("select chrNowState from resource_machines 
where id in (select chrMachineId from resource_plans_children where chrTaskId='$planId')");
            //自定义变量，方便判断当前任务下所有资源是否都是非执行中状态，如果是则更新任务状态，如果不是则不进行操作
            $if = 1;
            if ($getMachineStates) {
                foreach ($getMachineStates as $getMachineState) {
                    if ($getMachineState->chrNowState == 1) {
                        $if = ++$if;
                    }
                }
                if ($if == 1) {
                    //更新主表任务状态
                    $ret = DB::update("UPDATE resource_plans SET chrNowState ='2',updated_at='$time' where id='$planId'");
                }

            }
            return true;
        } catch (\Exception $e) {
            Log::info("错误信息：".$e);
            DB::rollback();
            throw $e;
        }
    }


    public function getLog($planId)
    {
        $result = [];
        //任务名称查询
        $plan_name = DB::select("select chrTaskName from resource_plans where id='$planId'");
        $result['task_name'] = $plan_name[0]->chrTaskName;
        //任务详情
        //4.18--为了少引用其他的SQL,这里修丰富了SQL内容
        //修改之后 又改变了查询逻辑,基于auto_task_exec表
        $result['task_list'] = DB::select("select 
id,
chrTaskName as chrTaskName,
chrTaskName as  chrProjectName,
'管理员' as chrUserName, 
chrMachineId ,
CASE  WHEN chrNowState = 1 THEN'执行中'WHEN chrNowState = 2 THEN'执行完毕'WHEN chrNowState = 3 THEN'执行失败'ELSE'未执行'END state, 
chrMachineId as chrBrowserNames,
chrMachineId as taskExecID,
chrMachineId as projectId,
chrEmails,
updated_at,
updated_at as endtime
from resource_plans 
where id='$planId'");
//        $result['task_list'] = $task_list[0];
//        dd($task_list);
        //资源详情
        $result['scheme_list'] = DB::select("SELECT 
a.id as id,
a.chrTaskName as  chrTaskName, 
b.chrMachineName as  schemeName,
a.chrTaskName as  projectName,
'管理员' as  createUser, 
case  when c.chrNowState=1 then '执行中' when c.chrNowState=2 then '执行成功' when c.chrNowState=3 then '执行失败' else '未执行' end state,
b.chrMachineName as  browserNames,
b.chrMachineId as  projectId ,
b.id as  taskExecID
from resource_plans a
LEFT JOIN resource_plans_children b on a.id=b.chrTaskId
LEFT JOIN resource_machines c on c.id=b.chrMachineId
where a.id='$planId'");

        //日志统计分析
        $result['script_sum'] = DB::select("SELECT 
chrMachineName count,
chrError as chrDescription ,
chrPct as  passlv
from resource_plans_children a
where chrTaskId='$planId'
GROUP BY a.chrMachineName");
        // dd($result['script_sum']);
        //覆盖详情
        $result['project_sum'] = DB::select("select 
count(*) as allscripts,
count(*) execs,
sum(CASE WHEN b.chrNowState=2 THEN 1 else 0  END) as execPass,
sum(CASE WHEN b.chrNowState=2 THEN 1 else 0  END)/count(*)*100 execlv,
sum(CASE WHEN b.chrNowState=2 THEN 1 else 0 END)/count(*)*100 passlv
from 
resource_plans_children a 
LEFT JOIN resource_machines b on a.chrMachineId=b.id
where a.chrTaskId='$planId'");


        //获取当前资源步骤总数
        $stepAllCount="SELECT
c.uid,
COUNT(c.wid) as wcount
FROM
(SELECT
chrMachineName as uid,
chrTaskId as taskId,
chrError as error,
substring_index(substring_index(a.chrError,',',b.help_topic_id + 1),',' ,- 1) as wid
FROM resource_plans_children a
JOIN mysql.help_topic b ON b.help_topic_id < (length(a.chrError) - length(REPLACE(a.chrError, ',', '')) + 1
)
) c
where c.taskId='91'
GROUP BY c.uid ";

        //error信息单独处理
//        $machineArray=array();
//        $machineRes=DB::select("SELECT
//chrMachineName,
//chrError
//from resource_plans_children a
//where chrTaskId='$planId'
//GROUP BY a.chrMachineName");
//        foreach ($machineRes as $machineRess){
//            $machineArray[$machineRess->chrMachineName]=$machineRess->chrMachineName;
//        }
//
//        $stepArray=array();
//        $stepRes=DB::select("SELECT
//chrMachineName,
//chrError
//from resource_plans_children a
//where chrTaskId='$planId'
//GROUP BY a.chrMachineName");
//        foreach ($stepRes as $stepRess){
//            $errors=$stepRess->chrError;
//            $error=explode(',',$errors);
//            foreach ($error as $v){
//                array_push($stepArray,$errors);
//            }
//        }
//
//
//
//        $stepAll=array_push();
        return $result;

    }

    public function editUpdateQuery($planId,$planName,$machineIdAll,$steps)
    {
        DB::beginTransaction();
        try {
            $taskName = $planName;
            $machineId = $machineIdAll;
            $stepId = $steps;
            $time = date('Y-m-d H:i:s');
            $checkMachineId = implode(',', $machineId);
            //$checkStepId=implode(',',$stepId);
            //resource_plans去重
//            $check = DB::select("select chrTaskName,chrMachineId,chrNowState,created_at,updated_at from resource_plans
//where chrTaskName='$taskName'
//and chrMachineId ='$checkMachineId'
//and chrNowState =2
//");
            //insert into resource_plans
//            if (empty($check)) {
                $insertPlan = DB::update("UPDATE resource_plans 
SET chrTaskName ='$taskName',
chrMachineId='$checkMachineId',
chrNowState='2',
updated_at='$time' 
where id='$planId'
");
//            }


            //准备insertPlanChildren 数据
            //$chrTask = DB::select("select id from resource_plans where chrTaskName='$taskName'");
            $chrTaskId = $planId;


            //第二种逻辑，两个数组，单独循环获取，之后合并，一个当键，一个当值
            //获取资源数据
            $arrayMachineKey = array();
            $arrayMachine = array();
            $arrayMachineAll = array();
            $arrayStep = array();
            $arrayStepAll = array();
            foreach ($machineId as $machineIds) {
                $machine = DB::select("select id,chrMachName,chrMacIp from resource_machines where id= '$machineIds'");
                $chrMachineId = $machine[0]->id;
                $chrMachineName = $machine[0]->chrMachName;
                $chrMachineMacIP = $machine[0]->chrMacIp;
                array_push($arrayMachineKey, $machineIds);
                $arrayMachine['chrMachineId'] = $chrMachineId;
                $arrayMachine['chrMachineName'] = $chrMachineName;
                $arrayMachine['chrMachineMacIP'] = $chrMachineMacIP;
                //array_push($arrayMachineKey,$arrayMachine);

                array_push($arrayMachineAll, $arrayMachine);
            }
            //循环获取step数据

            foreach ($stepId as $stepIds) {

                $chrStepIds = '';
                $chrStepIdForYs = '';
                $chrStepNames = '';
                $chrStepFuns = '';
                foreach ($stepIds as $stepIdss){

                    if($stepIdss !=""){
                        $step = DB::select("select
id as id,
chrStepId as chrStepId,
chrStepName as chrStepName,
chrStepFun as chrStepFun
from resource_steps where chrStepId = '$stepIdss'");
                        $chrStepId = $step[0]->id;
                        $chrStepIds = $chrStepIds.$chrStepId.',';
                        $chrStepIdForY = $step[0]->chrStepId;
                        $chrStepIdForYs = $chrStepIdForYs.$chrStepIdForY.',';
                        $chrStepName = $step[0]->chrStepName;
                        $chrStepNames = $chrStepNames.$chrStepName.',';
                        $chrStepFun = $step[0]->chrStepFun;
                        $chrStepFuns = $chrStepFuns.$chrStepFun.',';
                    }
                }


                $arrayStep['chrStepId'] = $chrStepIds;
                $arrayStep['chrStepIdForY'] = $chrStepIdForYs;
                $arrayStep['chrStepName'] = $chrStepNames;
                $arrayStep['chrStepFun'] = $chrStepFuns;

                array_push($arrayStepAll, $arrayStep);
            }

            $all = array_combine($arrayMachineKey, $arrayMachineAll);


            $arrayPlan = array_combine($arrayMachineKey, $arrayStepAll);


            foreach ($all as $k1 => $v1) {
                foreach ($arrayPlan as $k2 => $v2) {
                    if ($k2 == $k1) {
                        foreach ($v2 as $k3 => $v3) {
                            $v1[$k3] = $v3;
                            $all[$k1] = $v1;

                        }

                    }
                }
            }


            foreach ($all as $k => $v) {
                $cTaskId = $chrTaskId;
                $cMachineId = $v['chrMachineId'];
                $cMachineName = $v['chrMachineName'];
                $cMachineMacIP = $v['chrMachineMacIP'];
                $cStepId = $v['chrStepId'];
                $cStepIdForY = $v['chrStepIdForY'];
                $cStepName = $v['chrStepName'];
                $cStepFun = $v['chrStepFun'];
                $checkIfHave=DB::select("select * from resource_plans_children where chrTaskId='$cTaskId'and
chrMachineId='$cMachineId'");
                if(empty($checkIfHave)){
                    $insertSql="insert into resource_plans_children
(chrTaskId,
chrMachineId,
chrMachineName,
chrMachineMacIP,
chrStepId,
chrStepIdForY,
chrStepName,
chrStepFun,
created_at,
updated_at)
VALUES('$cTaskId',
'$cMachineId',
'$cMachineName',
'$cMachineMacIP',
'$cStepId',
'$cStepIdForY',
'$cStepName',
'$cStepFun',
'$time',
'$time')";
                    $insertPlanChildren = DB::insert($insertSql);

                }else{
                    $updateSql="UPDATE resource_plans_children 
set chrTaskId='$cTaskId',
chrMachineId='$cMachineId',
chrMachineName='$cMachineName',
chrMachineMacIP='$cMachineMacIP',
chrStepId='$cStepId',
chrStepIdForY='$cStepIdForY',
chrStepName='$cStepName',
chrStepFun='$cStepFun',
updated_at='$time'
where chrTaskId='$cTaskId'and
chrMachineId='$cMachineId'";
                    $insertPlanChildren = DB::update($updateSql);

                }

            }
            DB::commit();
            return $taskName;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }


    public function deletePlanChildren($planId,$machineId)
    {
        DB::beginTransaction();
        try {
            $checkIfExit=DB::select("select * from resource_plans_children where chrTaskId='$planId' and chrMachineId ='$machineId'");

            if($checkIfExit){
                $deleteSql="delete from resource_plans_children where chrTaskId='$planId' and chrMachineId ='$machineId'";
                $res=DB::delete($deleteSql);
                DB::commit();
                return $res;
            }else{
                return false;
            }

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

    }


    public function stopRun($planId)
    {
        DB::beginTransaction();
        try {

            $chechState = DB::select("select chrNowState from resource_plans where id='$planId'");
            $chechState = $chechState[0]->chrNowState;
            $msg=0;
            if ($chechState == 1) {
                //更新任务状态
                $res1 = DB::update("UPDATE resource_plans SET chrNowState ='2' where id='$planId'");
                //更新机器状态
                $machineIds = DB::select("select chrMachineId from resource_plans_children where chrTaskId='$planId'");
                foreach ($machineIds as $machineId) {
                    $id = $machineId->chrMachineId;
                    $checkMachineState = DB::select("select chrNowState from resource_machines where id='$id'");
                    if ($checkMachineState[0]->chrNowState == 1) {
                        $res = DB::update("UPDATE resource_machines SET chrNowState ='2' where id='$id'");
                        $msg=1;
                    }
                    //停止任务后清空子表error及pct信息
                    $res1 = DB::update("UPDATE resource_plans_children 
SET chrError ='',chrPct='0'  
where chrTaskId='$planId' and chrMachineId='$id'");
                }
            }else{
                $msg=2;
            }
            DB::commit();
            return $msg;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }


    public function getEmailTo($planId)
    {
        $res= DB::select("select chrEmails from resource_plans where id='$planId'");
        return $res[0]->chrEmails;
    }


}

?>