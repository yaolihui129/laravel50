<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Utils\FileHelper;
use App\Utils\EmailHelper;
use App\User;
use App\AutoReport;
use Illuminate\Support\Facades\Auth;

class WormQueueService
{

    /**
     * 获取队列中的所有任务
     */
    public function getQueueJobs()
    {
        return DB::select("select aeb.id as browserID,aeb.chrBrowserENName,aj.id jobID,aj.intExecTaskID,
				aj.tPayload from auto_exec_browsers aeb
				INNER JOIN auto_jobs aj on aj.intBrowserID=aeb.id and aj.tintState=0
join auto_task_execs ate ON ate.id=aj.intExecTaskID
join auto_tasks att on att.id=ate.intTaskID
join auto_projects ap ON ap.id=att.intProjectID
where ap.intType=0 or ap.intType is NULL
				ORDER BY aj.id");

//        select aeb.id as browserID,aeb.chrBrowserENName,aj.id jobID,aj.intExecTaskID,
//        aj.tPayload from auto_exec_browsers aeb
//				INNER JOIN auto_jobs aj on aj.intBrowserID=aeb.id and aj.tintState=0
//				ORDER BY aj.id
    }

    /**
     *
     * @return unknown
     */
    private function getTimerRates()
    {
        $rows = DB::select("select id,chrDictName,chrDictValue from sys_dicts where chrDictName='execrate'");
        // 存入缓存
        return $rows;
    }

    /**
     *
     * @param unknown $payload
     * @return boolean
     */
    private function containsDate($timer)
    {
        $now_date = strtotime(date('Y-m-d'));
        $lastDate = $timer ["lastDate"];
        if ($now_date == $lastDate) // 当前天已执行过
            return false;
        $execBeginDate = strtotime($timer ["execBeginDate"]);
        $execEndDate = strtotime($timer ["execEndDate"]);
        // 还需要继续循环执行该定时任务
        if ($now_date >= $execBeginDate && $now_date <= $execEndDate) {
            if (empty ($lastDate)) // 第一次执行
                return true;
            $execRate = $timer ["execRate"];
            $rates = $this->getTimerRates();
            $dictValue = "";
            foreach ($rates as $rate) {
                if ($rate->id == $execRate) {
                    $dictValue == $rate->chrDictName;
                    break;
                }
            }
            switch ($dictValue) {
                case "每周" :
                    $nextDate = strtotime("+1 week", strtotime($lastDate));
                    break;
                case "每月" :
                    $nextDate = strtotime("+1 month", strtotime($lastDate));
                    break;
                case "每年" :
                    $nextDate = strtotime("+1 year", strtotime($lastDate));
                    break;
                default :
                    $nextDate = strtotime("+1 day", strtotime($lastDate));
                    break;
            }
            if ($now_date == $nextDate)
                return true;
            return false;
        }
        return false;
    }

    /**
     *
     * @param unknown $tiTaskId
     */
    private function updateReport($tiTaskId, $taskId)
    {
        $taskType = 1;
        $tId = $taskId;
        if ($tiTaskId) {
            $taskType = 2; // 定时任务
            $tId = $tiTaskId;
        }
        $rows = DB::select("select id from auto_reports 
				where intTaskID=$tId and intTaskType=$taskType and intState=1 and intFlag=0");
        if (empty ($rows)) {
            $report_model = new AutoReport ();
            $report_model->intTaskID = $tId;
            $report_model->intTaskType = $taskType;
            $report_model->intState = 1;
            $report_model->save();
            DB::update("update auto_reports set intFlag=1 where intTaskID=$tId and intTaskType=$taskType 
			and intState in (2,3) and intFlag=0");
            return $report_model->id;
        }
        return $rows [0]->id;
    }

    /**
     * 分发自动化任务
     *
     * @param unknown $connection
     * @param unknown $queue
     * @param unknown $delay
     * @param unknown $memory
     * @param unknown $daemon
     * @param unknown $tries
     */
    public function dispachAutoTask(&$jobs, $connection, $queue, $delay, $memory, $daemon, $tries)
    {
        $ret = true;
        try {
            foreach ($jobs as $key => $job) {
                $payload = json_decode($job->tPayload, true);
                $tiTaskId = $payload ['tiTaskId'];
                if ($tiTaskId) {
                    $timer = $payload ["timer"];
                    // 表示该定时任务在可执行日期内
                    if ($this->containsDate($timer)) {
                        $execTime = $timer ["execTime"];
                        $now_hour = date("H");
                        $execTime = date("H", strtotime($execTime));
                        // 该定时任务在执行时间点中
                        if ($now_hour != $execTime) { // 若该定时任务在该hour上不执行 则继续下条记录
                            $ret = false;
                            //print_r("this job is timer not time\r\n");
                            continue;
                        }
                    } else
                        continue;
                }
                $browserId = $job->browserID;
                $machines = DB::select("select aeb.chrBrowserENName,smac.id machID,smac.chrMachName,smac.chrIp,
						smr.id mconfID,smr.chrHub,smr.intHubPort from auto_exec_browsers aeb
						INNER JOIN sys_machine_configs smc on smc.intBrowserID=aeb.id
						INNER JOIN sys_machines smac on smac.id=smc.intMachineID
						INNER JOIN sys_machine_relations smr on smr.intMachineID=smc.intMachineID and smr.intHubNowCount<smr.intHubMaxCount
						where aeb.id=$browserId and smc.intType=0 ORDER BY (smr.intHubMaxCount-smr.intHubNowCount) desc limit 1");
                if (!empty ($machines)) { // 有机器空闲
                    $machine = $machines [0];
                    $hubUrl = "http://" . $machine->chrIp . ":" . $machine->intHubPort . $machine->chrHub;
                    $machId = $machine->machID;
                    $args = array(
                        'jobId' => $job->jobID,
                        'payload' => $payload,
                        'browsers' => array(
                            'browserId' => $browserId,
                            'machineId' => $machId,
                            $machine->chrBrowserENName => $hubUrl
                        ),
                        'requestURL' => 'http://localhost:80/ext/log?token=q8E89zRdp'
                    );
                    $root = database_path("schemes") . DIRECTORY_SEPARATOR;
                    $taskId = $payload ["taskId"];

                    // $runService = new RunTaskPthreadService ( $run );
                    // $runService->start ();
                    // popen ( "cmd /c $run &", 'r' ); // 必须放到最后
                    $status = 0;
                    if ($status === 0) { // 发送命令成功
                        DB::beginTransaction();
                        try {
							echo 'web:exectaskid:' .  $job->jobID;

                            $reportId = $this->updateReport($tiTaskId, $taskId); // 任务开启前 插入一条报告记录
                            DB::update("update auto_jobs set tintState=1 where id=$job->jobID");
                            $execTaskId = $payload ['execTaskId'];
                            DB::update("update auto_task_execs set intState=1 where id=$execTaskId");
                            if ($tiTaskId) {
                                DB::update("update auto_timer_task_relations set intState=1 where intTiTaskID=$tiTaskId and intState=0");
                            }
                            DB::update("update sys_machine_relations set intHubNowCount=intHubNowCount+1 where intMachineID=$machId");
                            /* $redis = RedisHelper::getInstance (); */
                            DB::commit();
                            $args ['reportId'] = $reportId;
                            $json = str_replace("\"", "\\\"", json_encode($args));
                            $run = $root . $taskId;
                            // $run .= "/script/runTest.py " . $json;
                            $run .= "/script/run.py " . $json;
                            // Log::info ( $run );
                            exec("cmd /c $run", $ret, $status);
                        } catch (\Exception $e) {
                            DB::rollback();
                            throw $e;
                        }
                    } else {
                        $ret = false;
                        print_r("worm send dos order error:" . json_encode($args) . "\r\n");
                    }
                }
            }
        } catch (\Exception $e) {
            $ret = false;
            print_r("worm exec error:" . $e->getMessage() . "\r\n");
        }
        return $ret;
    }

    /**
     *
     * @param unknown $logs
     * @param unknown $jobId
     */
    public function moveLogImage(&$logs, $jobId)
    {
        $resource = database_path('schemes/') . $logs ["image"];
        $screenShot = "/schemes/report/images/";
        $relativeDir = $screenShot . date('Y-m-d') . "/" . $jobId . "/";
        $dest = public_path() . $relativeDir;
        @mkdir($dest, 0777, true);
        if (is_file($resource)) {
            rename($resource, $dest . basename($logs ["image"]));
            $logs ["image"] = $relativeDir . basename($logs ["image"]);
        } else
            $logs ["image"] = $screenShot . "shot_default.jpg";
        /*
         * else $logs ["image"] = "no";
         */
    }

    /**
     * 获取指定执行任务下的队列任务
     *
     * @param unknown $jobId
     */
    private function getQueueJobsByExecId($jobId, $execTaskId)
    {
        return DB::select("select id from auto_jobs 
				where intExecTaskID=$execTaskId and id not in ($jobId) LIMIT 1");
    }

    /**
     * 删除队列中的任务
     *
     * @param unknown $jobId
     */
    private function deleteQueue($jobId)
    {
        DB::delete("delete from auto_jobs where id=$jobId");
    }

    /**
     * 释放被占用的机器
     *
     * @param unknown $browsers
     */
    private function releaseMachine($browsers)
    {
        $machineId = $browsers ["machineId"];
        DB::update("update sys_machine_relations set intHubNowCount=intHubNowCount-1 where intMachineID=$machineId and intHubNowCount>0");
    }

    /**
     *
     * @param unknown $execTaskId
     */
    private function sendEmail($execTaskId, $state, $reportId, $taskName)
    {
        try {
            switch ($state) {
                case 3 :
                    $stateinfo = "失败";
                    break;
                default :
                    $stateinfo = "成功";
                    break;
            }
            $execInfos = DB::select("select ate.id,ate.intCompanyID,ate.intCreaterID,chrEmails,chrUserName 
					from auto_task_execs ate
					LEFT JOIN users u on u.chrEmail=REPLACE(ate.chrEmails,';','') 
					where ate.id =$execTaskId");
            $emails = $execInfos [0]->chrEmails;
            if (!empty ($emails)) {
                $slogs = DB::select("select arep.created_at,arep.updated_at,a.allCount,b.*,c.stepErrCount from (
						select count(intScriptID) allCount,intScriptID from (
						select intScriptID from auto_logs
						GROUP BY intScriptID,intReportID HAVING intReportID=$reportId and intScriptID<>0)b)a
						LEFT JOIN
						(select asch.chrSchemeName,asri.chrScriptName,alog.intSchemeID,alog.intScriptID,
						alog.intOrderNo,alog.chrCmd,alog.chrCmdParam,alog.chrDescription,alog.chrErrorMessage 
						from auto_logs alog
						left JOIN auto_scripts asri on asri.id=alog.intScriptID
						left JOIN auto_schemes asch on asch.id=alog.intSchemeID
						where intReportID=$reportId and chrResult ='error')b on 1=1
						LEFT JOIN(
						select COUNT(*) stepErrCount,intScriptID from auto_logs alog 
						GROUP BY intScriptID,intReportID,chrResult
						HAVING intReportID=$reportId and chrResult ='error')c on c.intScriptID=a.intScriptID
						left JOIN auto_reports arep on arep.id=$reportId");
                $errLists = array();
                $errSCount = 0;
                $stepErrCount = 0;
                $scriptId = 0;
                $schemeId = 0;
                foreach ($slogs as $slog) {
                    $intSchemeId = $slog->intSchemeID;
                    $intScriptId = $slog->intScriptID;
                    if (!empty ($intSchemeId)) {
                        $stepErrCount++;
                        if ($schemeId != $intSchemeId) {
                            $errList = array(
                                "schemeName" => $slog->chrSchemeName,
                                "scriptName" => "",
                                "optName" => "",
                                "msg" => ""
                            );
                            $schemeId = $intSchemeId;
                            array_push($errLists, $errList); // 案例
                        }
                        if ($scriptId != $intScriptId) {
                            $errList = array(
                                "schemeName" => "",
                                "scriptName" => $slog->chrScriptName,
                                "optName" => "步骤：" . $slog->stepErrCount,
                                "msg" => "错误详情"
                            );
                            array_push($errLists, $errList); // 脚本
                            $scriptId = $intScriptId;
                            $errSCount++;
                        }
                        $errList = array(
                            "schemeName" => "",
                            "scriptName" => "",
                            "optName" => "失败步骤：" . $slog->intOrderNo . $slog->chrDescription,
                            "msg" => $slog->chrErrorMessage
                        );
                        array_push($errLists, $errList); // 步骤
                    }
                }
                $allCount = $slogs [0]->allCount; //
                $sucSCount = ($allCount - $errSCount);
                $sucSPercent = intval(($sucSCount / $allCount) * 100);
                $scripts = array(
                    "sCount" => $allCount,
                    "errSCount" => $errSCount,
                    "sucSCount" => $sucSCount,
                    "sucSPercent" => $sucSPercent . "%"
                );
                $emails = explode(";", $emails);
                $name = count($emails) > 1 ? "各位" : ($execInfos [0]->chrUserName);
                $beginTime = $slogs [0]->created_at;
                $endTime = $slogs [0]->updated_at;
                $date = floor((strtotime($endTime) - strtotime($beginTime)) / 86400);
                $hour = floor((strtotime($endTime) - strtotime($beginTime)) % 86400 / 3600);
                $minute = floor((strtotime($endTime) - strtotime($beginTime)) % 86400 / 60);
                $second = floor((strtotime($endTime) - strtotime($beginTime))) % 86400 % 60;
                $times = $date . "天" . $hour . "小时" . $minute . "分" . $second . "秒";

                $parse = $this->parseEmailData($execTaskId);

                $pageData = array(
                    "user" => array(
                        "name" => $name
                    ),
                    "state" => $stateinfo,
                    "sysUrl" => "http://10.10.12.163:8088/",
                    "rlist" => array(
                        "taskName" => $taskName,
                        "beginTime" => $beginTime,
                        "endTime" => $endTime,
                        "times" => $times,
                        "script" => $scripts
                    ),
                    "errors" => $errLists,
                    "task_list" => $parse['task_list'],
                    "scheme_list" => $parse['scheme_list'],
                    "script_sum" => $parse['script_sum'],
                    "project_sum" => $parse['project_sum']
                );
                $emailInfo = array(
                    "to" => $emails,
                    "subject" => "用友云测试报告"
                );
                EmailHelper::sendEmail("emails.reportplus", $pageData, $emailInfo);
            }
        } catch (\Exception $e) {
            Log::info("send email error:" . $e->getMessage());
        }
    }

    /**
     *
     * @param unknown $payload
     * @return boolean
     */
    private function containsExecDate($timer)
    {
        $now_date = strtotime(date('Y-m-d'));
        $execBeginDate = strtotime($timer ["execBeginDate"]);
        $execEndDate = strtotime($timer ["execEndDate"]);
        // 还需要继续循环执行该定时任务
        if ($now_date > $execBeginDate && $now_date < $execEndDate) {
            return true;
        }
        return false;
    }

    /**
     *
     * @param unknown $execs
     * @param unknown $payload
     */
    private function resetTimerTask($execs, $timer)
    {
        // 还需要继续循环执行该定时任务
        if ($this->containsExecDate($timer)) {
            $user = new User ();
            foreach ($execs as $exec) {
                $user->id = $exec->intCreaterID;
                $tiTaskId = $exec->intTimerTaskID;
                $taskIds [] = $exec->intTaskID;
                $selBrowsers = $exec->chrBrowserIDs;
                $emails = $exec->chrEmails;
            }
            $execInfo = array(
                "taskId" => implode(";", $taskIds),
                "selBrowsers" => explode(";", $selBrowsers),
                "emails" => $emails
            );
            $timer ["lastDate"] = date('Y-m-d');
            // 重新生成队列任务
            $ateService = new AutoTaskExecService ();
            $ateService->insert($execInfo, $user, $tiTaskId, $timer);
        }
    }

    /**
     *
     * @param unknown $tiTaskState
     * @param unknown $payload
     */
    private function updateReportState($tiTaskState, $reportId)
    {
        DB::update("update auto_reports set intState=$tiTaskState,updated_at=now() where id=$reportId");
    }

    /**
     * 更改任务执行状态
     *
     * @param unknown $execTaskId
     */
    private function updateExecState($jobId, $execTaskId, $payload, $reportId, $taskId, $tiTaskId)
    {
        $rows = $this->getQueueJobsByExecId($jobId, $execTaskId);
        if (empty ($rows)) {
			//0608增加约束
            $rows = DB::select("select id from auto_logs
				where intExecTaskID=$execTaskId and chrResult in ('error','fail') limit 1");
            if (empty ($rows)) // 没有失败的操作
                $state = 2; // 成功
            else
                $state = 3;
            // 更改任务的执行状态
            DB::update("update auto_task_execs set intState=$state where id=$execTaskId");
            // 若$tiTaskId!=0 则为定时任务
            if ($tiTaskId) {
                // 判断定时任务下的任务是否执行完毕
                $execs = DB::select("select ate.id,ate.intTimerTaskID,ate.intTaskID,ate.intState,
						ate.chrBrowserIDs,ate.chrEmails,ate.intCreaterID,atit.chrTiTaskName taskName
						from auto_task_execs ate
						INNER JOIN auto_timer_tasks atit on atit.id=ate.intTimerTaskID
						where intTimerTaskID=$tiTaskId order by intState");
                $tiTaskState = 2; // 成功
                foreach ($execs as $exec) {
                    if ($exec->intState == 1)
                        return;
                    else if ($exec->intState == 3) {
                        $tiTaskState = 3;
                        break;
                    }
                }
                // 更新定时任务的执行状态和执行次数
                DB::update("update auto_timer_task_relations set intState=$tiTaskState,intExecCount=intExecCount+1,
				dtLastTime=NOW() where intTiTaskID=$tiTaskId");
                $this->updateReportState($tiTaskState, $reportId);
                $this->sendEmail($execTaskId, $tiTaskState, $reportId, $execs [0]->taskName); // 发送邮件
                $timer = $payload ["timer"];
                $this->resetTimerTask($execs, $timer);
            } else {
                $this->updateReportState($state, $reportId);
                $execs = DB::select("select chrTaskName taskName from auto_tasks where id=$taskId");
                $this->sendEmail($execTaskId, $state, $reportId, $execs [0]->taskName); // 发送邮件
            }
        }
    }

    /**
     *
     * @param unknown $jobId
     */
    private function getJobById($jobId)
    {
        return DB::select("select id from auto_jobs where id=$jobId");
    }

    /**
     * 根据回传的日志 更新队列任务以及任务状态
     *
     * @param unknown $jobId
     * @param unknown $payload
     * @param unknown $browsers
     * @param unknown $schemeId
     * @param unknown $status
     */
    public function receive($jobId, $reportId, $payload, $browsers, $schemeId, $status)
    {
        try {
            $execTaskId = $payload ["execTaskId"];
            $taskId = $payload ["taskId"];
            $tiTaskId = $payload ["tiTaskId"];
            if ($status == "END") { // 案例运行完毕
                $rows = DB::select("select COUNT(*) AllSchemeCount from auto_task_relations atr
                        INNER JOIN auto_schemes asch on asch.id=atr.intSchemeID
						where atr.intTaskID=$taskId
						UNION all
						select COUNT(*) AllSchemeCount from (
						select DISTINCT intSchemeID FROM auto_logs alo
						where alo.intJobID=$jobId and alo.chrStatus='END')a");
                if ($rows [0]->AllSchemeCount == $rows [1]->AllSchemeCount) {
                    $job = $this->getJobById($jobId);
                    if (!empty ($job)) { // 说明第一次结束
                        DB::beginTransaction();
                        try {
                            $this->releaseMachine($browsers); // 释放被占用的机器
                            $this->deleteQueue($jobId); // 删除队列中的任务
                            DB::commit();
                            $this->updateExecState($jobId, $execTaskId, $payload, $reportId, $taskId, $tiTaskId); // 更改任务执行状态
                        } catch (\Exception $e) {
                            DB::rollback();
                            Log::info("update report state error" . $e->getMessage());
                            throw $e;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function parseEmailData($execTaskId)
    {
        $result = [];
        //任务详情
        $result['task_list'] = DB::select("SELECT distinct ats.id, chrTaskName,apro.chrProjectName, u.chrUserName,ats.updated_at,ate.created_at exectime, case when ate.intState=0 then '排队中' when ate.intState=1 then '执行中' when ate.intState=2 then '执行成功' when ate.intState=3 then '执行失败' else '未执行' end state,ate.chrBrowserNames, ate.id taskExecID,ats.intProjectID projectId
              from auto_tasks ats INNER JOIN users u on u.id=ats.intCreaterID LEFT JOIN auto_task_execs ate on ate.intTaskID=ats.id INNER JOIN auto_projects apro on apro.id=ats.intProjectID where  ats.id in (SELECT DISTINCT intTaskID from auto_task_execs where id=$execTaskId) and ate.id=$execTaskId ORDER BY ats.chrTaskName desc
			");
        //案例详情
        //4-20修改了SQL
		$result['scheme_list'] = DB::select("select 
ats.chrTaskName chrTaskName, 
s.chrSchemeName schemeName,
apro.chrProjectName projectName,
u.chrUserName createUser,
eb.chrBrowserName browserNames ,
CASE WHEN l.allcount = l.successcount THEN '执行成功' ELSE '执行失败'end state,
l.allcount,l.successcount
from (select id,intTaskID,substring(chrBrowserIDs,1,1) intBrowserID from auto_task_execs where id=$execTaskId
union select id,intTaskID,substring(chrBrowserIDs,3,1) intBrowserID from auto_task_execs where id=$execTaskId
union select id,intTaskID,substring(chrBrowserIDs,5,1) intBrowserID from auto_task_execs where id=$execTaskId
)te join auto_exec_browsers eb on eb.id like te.intBrowserID 
join auto_task_relations tr on te.intTaskID=tr.intTaskID 
join auto_schemes s on tr.intSchemeID=s.id
join auto_tasks ats on ats.id=te.intTaskID
LEFT JOIN auto_projects apro ON apro.id = ats.intProjectID
INNER JOIN users u ON u.id = s.intCreaterID
left join (select intBrowserID,intSchemeID,count(*) allcount,sum(case when chrResult='PASS' or chrResult='WARNING' then 1 else 0 end) successcount 
from auto_logs where intExecTaskID=$execTaskId group by intBrowserID,intSchemeID) l on tr.intSchemeID=l.intSchemeID and eb.id=l.intBrowserID
order by s.id,eb.id,l.intSchemeID");
        //脚本日志统计分析
        $result['script_sum'] = DB::select("SELECT count(*) count, l.chrDescription,COUNT(CASE WHEN l.chrResult in ('PASS','WARNING') THEN '成功' END)/count(*)*100 passlv from auto_logs l LEFT JOIN auto_scripts s on l.intScriptID=s.id	where l.intExecTaskID=$execTaskId and chrDescription!='' GROUP BY l.chrDescription");
        // dd($result['script_sum']);
        //功能模块覆盖详情
        $result['project_sum'] = DB::select("SELECT ppid,ppchrProjectName,count(1) allscripts,sum(case when execScriptID is not null then 1 else 0 end) execs,
			sum(case when execPass=1 then 1 else 0 end ) execPass, 	sum(CASE WHEN execScriptID IS NOT NULL THEN 1	ELSE 0 END)/count(1)*100 execlv,
			sum(CASE WHEN execPass = 1 THEN	1	ELSE 0 END)/sum(CASE WHEN execScriptID IS NOT NULL THEN	1	ELSE 0 END)*100 passlv
			from
    		(select p.ppid,p.ppchrProjectName,s.id ppScriptID,s.chrScriptName ppScriptName,l.execScriptID,
    		(case when l.scriptAct=scriptActPass then 1 else 0 end) execPass
    		from
    		(select c3.id ppid,c3.chrProjectName ppchrProjectName from auto_projects c1 join auto_projects c2 on c1.id=c2.intParentID
			join auto_projects c3 on c2.id=c3.intParentID where c3.id in (SELECT intProjectID from auto_scripts where id in (SELECT DISTINCT intScriptID from auto_logs where intExecTaskID = $execTaskId))) p
    		join auto_scripts s on s.intProjectID=p.ppid
    		left join (select intScriptID execScriptID,count(*) scriptAct,sum(case when chrresult in ('PASS','WARNING') then 1 else 0 end) scriptActPass
          	from auto_logs where intExecTaskID=$execTaskId group by intScriptID) l on l.execScriptID=s.id
			) tmp group by ppid,ppchrProjectName order by ppchrProjectName");

        return $result;
    }


    //新增App任务方法
    public function getQueueJobs_app()
    {
        return DB::select("select aeb.id as browserID,aeb.chrBrowserENName,aj.id jobID,aj.intExecTaskID,
				aj.tPayload from app_exec_phone aeb
				INNER JOIN auto_jobs aj on aj.intBrowserID=aeb.id and aj.tintState=0
				join auto_task_execs ate ON ate.id=aj.intExecTaskID
				join auto_tasks ata ON ata.id=ate.intTaskID
join auto_scripts ats ON ats.id=ata.intProjectID
join auto_projects ap ON ap.id=ats.intTopProjectID
where ap.intType=1
				ORDER BY aj.id");
    }

    //App应用apk文件下载方法

    public function getFile($url, $save_dir = '', $filename = '', $type = 0) {
        if (trim($url) == '') {
            return false;
        }
        if (trim($save_dir) == '') {
            $save_dir = './';
        }
        if (0 !== strrpos($save_dir, '/')) {
            $save_dir.= '/';
        }
        //创建保存目录
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
            return false;
        }
        //获取远程文件所采用的方法
        if ($type) {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $content = curl_exec($ch);
            curl_close($ch);
        } else {
            ob_start();
            readfile($url);
            $content = ob_get_contents();
            ob_end_clean();
        }
        //echo $content;
        $size = strlen($content);
        //文件大小
        $fp2 = @fopen($save_dir . $filename, 'a');
        fwrite($fp2, $content);
        fclose($fp2);
        unset($content, $url);
        return array(
            'file_name' => $filename,
            'save_path' => $save_dir . $filename,
            'file_size' => $size
        );
    }


    public function dispachAutoTask_app(&$jobs, $connection, $queue, $delay, $memory, $daemon, $tries)
    {
        echo "begin";
        $ret = true;
        try {
            foreach ($jobs as $key => $job) {
                echo "ddd";
                $payload = json_decode($job->tPayload, true);
                $tiTaskId = $payload ['tiTaskId'];
                echo $tiTaskId;
                if ($tiTaskId) {
                    echo "vvv";
                    $timer = $payload ["timer"];
                    // 表示该定时任务在可执行日期内
                    if ($this->containsDate($timer)) {
                        $execTime = $timer ["execTime"];
                        $now_hour = date("H");
                        $execTime = date("H", strtotime($execTime));
                        // 该定时任务在执行时间点中
                        if ($now_hour != $execTime) { // 若该定时任务在该hour上不执行 则继续下条记录
                            $ret = false;
                            print_r("this job is timer not time\r\n");
                            continue;
                        }
                    } else
                        continue;
                }
                echo "eee";
                $browserId = $job->browserID;
                $machines = DB::select("select aeb.chrBrowserENName,smac.id machID,smac.chrMachName,smac.chrIp,
						smr.id mconfID,smr.chrHub,smr.intHubPort from app_exec_phone aeb
						INNER JOIN sys_machine_configs smc on smc.intBrowserID=aeb.id
						INNER JOIN sys_machines smac on smac.id=smc.intMachineID
						INNER JOIN sys_machine_relations smr on smr.intMachineID=smc.intMachineID and smr.intHubNowCount<smr.intHubMaxCount
						where aeb.id=1 and smc.intType=1 ORDER BY (smr.intHubMaxCount-smr.intHubNowCount) desc limit 1");
                echo "ccc";
                if (!empty ($machines)) { // 有机器空闲,暂时改为判断为空时也走下边的代码,以后优化为多个手机的时候改为判断不为空则走
                    echo "sss";
                    $machine = $machines [0];
                    echo ">>s1";
                    $hubUrl = "http://" . $machine->chrIp . ":" . $machine->intHubPort . $machine->chrHub;
                    echo ">>s1";
                    $machId = $machine->machID;
                    echo ">>s1";
                    $args = array(
                        'jobId' => $job->jobID,
                        'payload' => $payload,
                        'browsers' => array(
                            'browserId' => $browserId,
                            'machineId' => $machId,
                            $machine->chrBrowserENName => $hubUrl
                        ),
                        'requestURL' => 'http://localhost:8088/ext/log?token=q8E89zRdp'
                    );
                    echo ">>s1";
                    $root = database_path("schemes") . DIRECTORY_SEPARATOR;
                    $taskId = $payload ["taskId"];





                    //App任务获取所需参数
                    //获取报告的唯一id,按jobID获取
                    echo "xxx";

                    $intExecTaskID = $job->intExecTaskID;
                    $browserID = $job->browserID;
                    $jobID = $job->jobID;
                    $browserName=$job->chrBrowserENName;
                    echo "install";
                    echo $intExecTaskID;
                    //App任务获取参数sql
                    $appsql=DB::select("select chrFile road,appi.log run,appi.remarks uninstall, appi.name from
app_information appi
join  auto_scripts autos ON autos.id=appi.scriptid
join  auto_script_relations  autosr ON autosr.intScriptID=autos.id
JOIN  sys_attachments sysa ON sysa.id=autosr.intAttID
join  auto_tasks att ON att.intProjectID=appi.scriptid
join auto_task_execs ate ON ate.intTaskID=att.id
join auto_jobs aj ON aj.intExecTaskID=ate.id
where aj.intExecTaskID='$intExecTaskID'");





//                    $road = DB::select("select chrFile from
//                            app_information appi
//                            join  auto_scripts autos ON autos.id=appi.scriptid
//                            join  auto_script_relations  autosr ON autosr.intScriptID=autos.id
//                            JOIN  sys_attachments sysa ON sysa.id=autosr.intAttID
//                            join  auto_tasks att ON att.intProjectID=appi.scriptid
//                            join auto_task_execs ate ON ate.intTaskID=att.id
//                            join auto_jobs aj ON aj.intExecTaskID=ate.id
//                            where aj.intExecTaskID='713'");
//
//                     foreach ($road as $valroad){
//                         echo $valroad->chrFile;
//                     }
//
//                     //App任务获取monkey命令sql
//                    $run = DB::select("select appi.log run from
//app_information appi
//join  auto_scripts autos ON autos.id=appi.scriptid
//join  auto_script_relations  autosr ON autosr.intScriptID=autos.id
//JOIN  sys_attachments sysa ON sysa.id=autosr.intAttID
//join  auto_tasks att ON att.intProjectID=appi.scriptid
//join auto_task_execs ate ON ate.intTaskID=att.id
//join auto_jobs aj ON aj.intExecTaskID=ate.id
//where aj.intExecTaskID='713'");
//                    foreach ($run as $valrun){
//                        echo $valrun->run;
//                    }
//
//                    //App任务获取monkey卸载时所需参数(安装后包名)
//                    $uninstall = DB::select("select appi.remarks remarks from
//app_information appi
//join  auto_scripts autos ON autos.id=appi.scriptid
//join  auto_script_relations  autosr ON autosr.intScriptID=autos.id
//JOIN  sys_attachments sysa ON sysa.id=autosr.intAttID
//join  auto_tasks att ON att.intProjectID=appi.scriptid
//join auto_task_execs ate ON ate.intTaskID=att.id
//join auto_jobs aj ON aj.intExecTaskID=ate.id
//where aj.intExecTaskID='713'");
//                    foreach ($uninstall as $valuninstall){
//                        echo $uninstall->remarks;
//                    }





                    // $runService = new RunTaskPthreadService ( $run );
                    // $runService->start ();
                    // popen ( "cmd /c $run &", 'r' ); // 必须放到最后
                    $status = 0;
                    if ($status === 0) { // 发送命令成功
                        DB::beginTransaction();
                        try {
                            $reportId = $this->updateReport($tiTaskId, $taskId); // 任务开启前 插入一条报告记录
                            DB::update("update auto_jobs set tintState=1 where id=$job->jobID");
                            $execTaskId = $payload ['execTaskId'];
                            DB::update("update auto_task_execs set intState=1 where id=$execTaskId");
                            if ($tiTaskId) {
                                DB::update("update auto_timer_task_relations set intState=1 where intTiTaskID=$tiTaskId and intState=0");
                            }
                            DB::update("update sys_machine_relations set intHubNowCount=intHubNowCount+1 where intMachineID=$machId");
                            /* $redis = RedisHelper::getInstance (); */
                            DB::commit();
                            $args ['reportId'] = $reportId;
                            $json = str_replace("\"", "\\\"", json_encode($args));
                            //$run = $root . $taskId;
                            // $run .= "/script/runTest.py " . $json;
                            //$run .= "/script/run.py " . $json;
                            // Log::info ( $run );



                            //App任务守护进程
                            foreach ($appsql as $val){
                                $road=$val->road;
                                $run=$val->run;
                                $uninstall=$val->uninstall;
                                $name=$val->name;
                                echo "down>>";
                                echo $name;

                                $url="http://10.1.208.177/".urlencode(iconv("GB2312","UTF-8","$name.apk"));
                                $save_dir = "public/down";
                                $filename ="$name.apk";
                                $res = $this->getFile($url, $save_dir, $filename,1);//0  1 都是好使的
                                echo "downsuccess>>";

                                //App应用安装方法
                                if($res){
                                    sleep(20);
                                    $road1="C:/wamp/www/testworm/public/down/$name.apk";
                                    echo $road1;
                                    echo ">>ing";
                                    exec("cmd /c adb install $road1");
                                    echo ">>install_end";
                                }



                                //App应用执行monkey命令方法
                                echo ">>apprun";
                                //本机测试方法,已成功
                                $root1 = 'C:\wamp\www\testworm\public\log\\';
//                            $run = "ping www.baidu.com";
                                echo ">>$run";
                                $logtime = date("Y_m_d_H_i_s", time());
                                exec("cmd /c $run>$root1" . "$intExecTaskID-$browserID.log");
								echo ">>run_1";

                                //日志生成完毕后需逐条解析,获取monkey运行时间,包名等信息,根据jobID.
                                if (file_exists("$root1$intExecTaskID-$browserID.log")) {
									echo ">>run_10";
                                    //更改任务状态并发送邮件
                                    $this->updateAppExecState($intExecTaskID,2);
									echo ">>run_2";
                                    $this->sendAppEamil($intExecTaskID, $browserID);
									echo ">>run_3";
                                    $file = fopen("$root1" . "$intExecTaskID-$browserID" . ".log", "r", "w");
									echo ">>run_4";
                                    $user = array();
                                    $i = 0;
                                    //输出文本中所有的行，直到文件结束为止。
                                    while (!feof($file)) {
                                        $user[$i] = fgets($file);//fgets()函数从文件指针中读取一行
                                        $i++;
                                    }
									echo ">>run_5";
                                    fclose($file);
                                    $user = array_filter($user);
									echo ">>run_6";
                                    foreach ($user as $log){
                                        $query=DB::insert("INSERT into app_logs(intExecTaskID,browserID,browserName,log,time) VALUES('$intExecTaskID','$browserID','$browserName','$log','$logtime')");
                                    }
									echo ">>run_7";


                                    //文件读入数据库后删除
                                    //unlink("$root1$intExecTaskID-$browserID.log");
                                }else{
                                    $this->updateAppExecState($intExecTaskID,3);
                                }

                                echo ">>apprun_end";
                                //执行卸载应用命令方法
                                echo ">>$uninstall";
                                //本机测试方法,已成功
                                exec("cmd /c adb uninstall $uninstall");
                                echo ">>appuninstall_end";
                                unlink("$road1");
                                echo ">>delete_success";

                            }

                        } catch (\Exception $e) {
                            DB::rollback();
                            throw $e;
                        }
                    } else {
                        $ret = false;
                        print_r("worm send dos order error:" . json_encode($args) . "\r\n");
                    }
                }
            }
        } catch (\Exception $e) {
            $ret = false;
            print_r("worm exec error:" . $e->getMessage() . "\r\n");
        }
        return $ret;
    }
    // 发送app邮件
    public function sendAppEamil($intExecTaskID, $browserID)
    {
        $sql = "select ate.id,ate.intCompanyID,ate.intCreaterID,chrEmails,chrUserName ";
        $sql .= "from auto_task_execs ate LEFT JOIN users u on u.chrEmail=REPLACE(ate.chrEmails,';','') ";
        $sql .= "where ate.id=$intExecTaskID";

        $execInfos = DB::select($sql);
        $emails = $execInfos[0]->chrEmails;
        $root = public_path('log').'/';

        if (!empty($emails)) {
            $scriptId = DB::select("select ats.id from auto_scripts ats left join auto_tasks at on ats.id=at.intProjectID left join auto_task_execs ate on at.id=ate.intTaskID where ate.id=$intExecTaskID");
            $appInfo = DB::table('app_information')->where('scriptid', $scriptId[0]->id)->first();
            $pageData = [
                'user' => $execInfos[0]->chrUserName,
                'task_info' => $appInfo
            ];

            $emails = explode(';', $emails);
            $emailInfo = [
                'to' => $emails,
                'subject' => '用友云测试报告',
                'attach' => "$root$intExecTaskID-$browserID.log"
            ];
            // dd($pageData);
            EmailHelper::sendEmail('emails.reportapp', $pageData, $emailInfo);
        }
    }
    public function updateAppExecState($intExecTaskID,$status){
        $res = DB::update("UPDATE auto_task_execs set intState=2 where id='$intExecTaskID'");
        if($res){
            return true;
        }
        return false;
    }


}

?>