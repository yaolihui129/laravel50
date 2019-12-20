<?php

namespace App\Services;

use App\Utils\EmailHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Utils\FileHelper;
use App\SysFlow;
use App\AutoScheme;
use App\Utils\XMLHelper;
use App\Utils\StringUtil;
use App\Utils\ZipArchiveHelper;
use App\AutoTask;
use App\AutoTaskRelation;
use App\Services\LookEmailApiService;
use Requests as Re ;


class ApiTaskRunService
{

    public function sehemesname($apisehemesid)
    {
        $apisehemename = DB::select("select chrSchemeName from auto_schemes where id='$apisehemesid' ");
        return $apisehemename;
    }

    public function taskid($apisehemename)
    {
        $apitaskid = DB::select("select id from auto_tasks where chrTaskName='$apisehemename'");
        return $apitaskid;
    }

    public function taskexecrun($apitaskid)
    {
        $apitaskid = DB::update("UPDATE auto_task_execs SET intState=1 where intTaskID='$apitaskid'");
        return $apitaskid;
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

    public function insertlog()
    {
        //日志生成完毕后需逐条解析,获取monkey运行时间,包名等信息,根据jobID.
        if (file_exists("D:/ApacheAppServ/www/testworm/database/schemes/690/script/report/log/20170720/20170720165117report.log")) {

            //更改任务状态并发送邮件
            $file = fopen("D:/ApacheAppServ/www/testworm/database/schemes/690/script/report/log/20170720/20170720165117report.log", "r", "w");
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
                $query = DB::insert("INSERT into api_logs(apisehemesid,apitaskid,apisehemename,log,time) VALUES('541','690','success8686','$log','$logtime')");
            }
            return true;


            //文件读入数据库后删除
            //unlink("$root1$intExecTaskID-$browserID.log");
        }
    }

    public function apisehemeslog($apitaskid,$apitaskexecid){
        $query=DB::select("select * from api_logs where apitaskid='$apitaskid' and apitaskexecid='$apitaskexecid'");
        return $query;
    }

    public function apitaskexecid($apitaskid){
        $query=DB::select("select id from auto_task_execs where intTaskID='$apitaskid' ORDER BY id DESC LIMIT 1 ");
        return $query;
    }

    public function apitaskidserch($apitaskexecid){
        $query=DB::select("select intTaskID from auto_task_execs where id='$apitaskexecid' ");
        return $query;
    }

    public function writelog($apitaskid,$apitaskexecid){
        //根据日期,调用自写日志读取插件获取最新日志
        $time=date("Ymd");
        $dir="D:/ApacheAppServ/www/testworm/database/schemes/$apitaskid/script/report/log/$time/";
        $files = $this->lastFile($dir,1);
        //var_export($files);
        $logname=$files[0];
        //调用读取日志插件,把最新日志读取至数据库
        FileHelper::resource_copy ( $dir.$logname, "D:/ApacheAppServ/www/testworm/public/apilog/$apitaskid-$apitaskexecid.log" );
        //日志生成完毕后需逐条解析,获取运行时间,包名等信息,根据jobID.
        if (file_exists("D:/ApacheAppServ/www/testworm/public/apilog/$apitaskid-$apitaskexecid.log")) {



            //更改任务状态并发送邮件
            //$this->updateAppExecState($intExecTaskID,2);
            //$this->sendAppEamil($intExecTaskID, $browserID);
            $file = fopen("D:/ApacheAppServ/www/testworm/public/apilog/$apitaskid-$apitaskexecid.log", "r", "w");
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
            if($query){
                //发送API邮件
                $this ->sendApiEmail($apitaskid,$apitaskexecid);
                //发送邮件后调用释放token的方法
//                $result = $this->releaseToken();
//                if ($result == 'logout sucess') {
//                    Log::info("任务释放token成功".date('Y-m-d-H:i:s'));
//                    return "Release Token Sucess";
//                } else {
//                    Log:info("任务释放token失败".date('Y-m-d-H:i:s'));
//                    return "Release Token Error";
//                }
//                return true;
            }
        }

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
    public function checklog($apitaskid,$apitaskexecid){
        $query=DB::select("select log from api_logs where  apitaskid=$apitaskid  and apitaskexecid=$apitaskexecid");
        return $query;
    }

    public function logdelete($apitaskid,$apitaskexecid){
        $query=DB::select("delete  from api_logs where apitaskid=$apitaskid  and apitaskexecid=$apitaskexecid");
        return $query;
    }
    //7-28发送API邮件
    public function sendApiEmail($apitaskid,$apitaskexecid)
    {
        //根据$apitaskexecid找到对应的当前任务，确定要发送的邮件地址
        $sql = "SELECT ate.id,ate.intCompanyID,ate.intCreaterID,chrEmails,chrUserName ";
        $sql.= "from auto_task_execs ate LEFT JOIN users u on u.chrEmail = REPLACE(ate.chrEmails,':','')";
        $sql.= "WHERE ate.id =$apitaskexecid AND ate.intTaskID = $apitaskid";

        $execInfos = DB::select($sql);
        $emails = $execInfos[0] -> chrEmails;
        $root = "D:/ApacheAppServ/www/testworm/public/log";
        if(!empty($emails)){

            //组织报告页面需要返回的数据
            // $execApiInfo = DB::select("SELECT ats.chrTaskName chrTaskName , CASE WHEN intState =0 THEN '未执行' WHEN intState = 1 THEN '执行中'WHEN intState = 2 THEN '执行成功'ELSE '执行失败' END  intState ,ate.created_at created_at 
            //                 FROM auto_task_execs ate
            //                 JOIN auto_tasks ats ON ate.intTaskID = ats.id
            //                 WHERE ate.id = $apitaskexecid");
            // $execApiInfoList = array(
            //     "apiName" => $execApiInfo[0] -> chrTaskName,
            //     "apiState" => $execApiInfo[0] ->intState,
            //     "apiTime" => $execApiInfo[0] ->created_at
            // );
            // $pageDate = [
            //     "user" => $execInfos[0] -> chrUserName,
            //     "task_info" => $execApiInfoList
            // ];
//            //执行的任务名称，状态，时间
//            $apiName = $execApiInfo[0] -> chrTaskName;
//            $apiState = $execApiInfo[0] ->intState;
//            $apiTime = $execApiInfo[0] ->created_at;
//            //这步没有用
//            $query = array($apiName,$apiState,$apiTime);

            $ser = new LookEmailApiService();
            $params = $ser->lookapi($apitaskid);
            $data['chrTaskName'] = $params['task_name']->chrTaskName;
            $data['task_list'] = array_get($params, 'task_list', []);
            $data['scheme_list'] = array_get($params, 'scheme_list', []);
            $data['script_sum'] = array_get($params, 'script_sum', []);
            $data['project_sum'] = array_get($params, 'project_sum', []);
            $data['exec_time'] = array_get($params, 'exec_time');
			$data['script_info'] = array_get($params, 'script_info');
            $emails = explode(';',$emails);
            $emailInfo = [
                'to' => $emails,
                'subject' => '用友云测报告',
                'attach' => "D:/ApacheAppServ/www/testworm/public/apilog/$apitaskid-$apitaskexecid.log"
            ];
            EmailHelper::sendEmail('report.reportapi',$data,$emailInfo);
			//EmailHelper::sendEmail('api.report.apiReportNew',$data,$emailInfo);
			
			//$data['taskid'] = $apitaskid;
            //$data['taskexecid'] = $apitaskexecid;
            //$emailInfo = [
                //'to' => 'zhengzt@yonyou.com',
                //'subject' => '用友云测报告'
                //'attach' => "E:/wamp/wamp/www/testworm/public/apilog/$apitaskid-$apitaskexecid.log"
            //];
            //EmailHelper::sendEmail('api.report.hrefTobroswer',$data,$emailInfo);
        }else{
            echo "ERROR:SEND EMAIL ";
        }
    }
    public function releaseToken()
    {

        $url = 'http://172.20.18.131:8072/gettoken.php?logout=1&sessionID=aucdeund9fh5i20iuh6nnoinq6';
        $params = [
            'logout' => 1,
            'sessionID' => 'aucdeund9fh5i20iuh6nnoinq6'
        ];

        try {
            $response = Re::request($url, [], [], 'GET', []);
            if ($response->status_code !== 200) {
                throw new \Exception("Error Processing Request", 1);
            }

            // $response = json_decode($response->body);

            return $response->body;
        } catch (\Exception $e) {
            $errorMsg = '调用外部API失败，api地址：'.$url.'参数：'.json_encode($params, JSON_UNESCAPED_UNICODE).'err message:'.$e->getMessage();
            Log($errorMsg);
            return false;
        }
    }
	
	
	public function checkIfError($thisapitaskexecid)
    {
        $res=DB::select("select id from api_logs
				where apitaskexecid='$thisapitaskexecid' and log in ('error') limit 1");
        return $res;
    }
	
	    public function getLog($taskexecid)
    {
        $result = [];
        //$execTaskId=DB::select("SELECT id FROM auto_task_execs WHERE intTaskID=$taskexecid");
        $execTaskId = $taskexecid;
//        $execApiTaskId = DB::select("SELECT id FROM api_logs WHERE apitaskexecid=$taskexecid AND apisehemesid=$apisehemesid AND apitaskid=$apitaskid");
//        $execApiTaskId = $execApiTaskId[0] ->id;
        //任务名称查询
        $task_name = DB::select("SELECT ats.id,chrTaskName FROM auto_tasks ats 
JOIN auto_task_execs ate ON ate.intTaskID=ats.id
WHERE ate.id=$execTaskId");
        $result['task_name'] = $task_name[0];
        //任务详情
        //4.18--为了少引用其他的SQL,这里修丰富了SQL内容
        //修改之后 又改变了查询逻辑,基于auto_task_exec表
        $result['task_list'] = DB::select("SELECT DISTINCT ats.id,chrTaskName,apro.chrProjectName,u.chrUserName,
ats.updated_at,
arp.updated_at endtime,
ate.updated_at exectime,
CASE WHEN ate.intState = 0 THEN'排队中'WHEN ate.intState = 1 THEN'执行中'WHEN ate.intState = 2 THEN'PASS'WHEN ate.intState = 3 THEN'Error'ELSE'未执行'END state,
ate.chrBrowserNames,ate.id taskExecID
,ats.intProjectID projectId
FROM auto_task_execs ate
JOIN auto_tasks ats ON ate.intTaskID=ats.id
INNER JOIN users u ON u.id = ats.intCreaterID
INNER JOIN auto_projects apro ON apro.id = ats.intProjectID
LEFT JOIN auto_logs al ON al.intExecTaskID = ate.id
LEFT JOIN auto_reports arp ON arp.id = al.intReportID
WHERE ats.id IN (SELECT DISTINCT intTaskID FROM auto_task_execs WHERE id =$execTaskId)AND ate.id =$execTaskId
ORDER BY ats.chrTaskName DESC");

        //案例详情
        //将API的结果统计加入到这里
//        $result[''] = DB::select("SELECT DISTINCT aus.id,
//				ats.chrTaskName, chrSchemeName schemeName,apro.chrProjectName projectName,u.chrUserName createUser, case when ate.intState=0 then '排队中' when ate.intState=1 then '执行中' when ate.intState=2 then '执行成功'
//              when ate.intState=3 then '执行失败' else '未执行' end state,ate.chrBrowserNames browserNames,ats.intProjectID projectId ,ate.id taskExecID
//              from auto_schemes aus
//              INNER JOIN users u on u.id=aus.intCreaterID
//              INNER JOIN auto_task_relations atr on atr.intSchemeID=aus.id
//              INNER JOIN auto_tasks ats on ats.id=atr.intTaskID
//              LEFT JOIN auto_task_execs ate on ate.intTaskID=ats.id
//              LEFT JOIN auto_projects apro on apro.id=ats.intProjectID
//              where ats.id in (SELECT DISTINCT intTaskID from auto_task_execs where id=$execTaskId) and ate.id=$execTaskId ORDER BY ats.chrTaskName desc");

        $result[''] = DB::select("SELECT DISTINCT aus.id,als.log apilog ,
				ats.chrTaskName, chrSchemeName schemeName,apro.chrProjectName projectName,u.chrUserName createUser, case when ate.intState=0 then '排队中' when ate.intState=1 then '执行中' when ate.intState=2 then 'PASS'
              when ate.intState=3 then 'Error' else '未执行' end state,ate.chrBrowserNames browserNames,ats.intProjectID projectId ,ate.id taskExecID
              from auto_schemes aus
              INNER JOIN users u on u.id=aus.intCreaterID
              INNER JOIN auto_task_relations atr on atr.intSchemeID=aus.id
              INNER JOIN auto_tasks ats on ats.id=atr.intTaskID
              LEFT JOIN auto_task_execs ate on ate.intTaskID=ats.id
              LEFT JOIN auto_projects apro on apro.id=ats.intProjectID
              INNER JOIN api_logs als on als.apitaskexecid=ate.id
              where ats.id in (SELECT DISTINCT intTaskID from auto_task_execs where id=$execTaskId) and ate.id=$execTaskId  AND als.log like \"%场景统计结果%\"  ORDER BY ats.chrTaskName desc");

        if ($result['']) {
            foreach ($result[''] as $list) {
                $result['exec_time'] = substr($list->apilog, 0, 19);
                $list->apilog = substr($list->apilog, 41);
            }
        }

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

//        //API结果统计
//        $result['api_list'] = DB::select("SELECT
//            als.id ,ats.chrTaskName,als.log
//            FROM
//            api_logs als
//            INNER JOIN auto_tasks ats ON ats.id = als.apitaskid
//            WHERE
//            als.id=$execApiTaskId and als.log  LIKE \"%场景统计结果%\"" );

        $result['script_info']=DB::select("SELECT 
chrSchemeName schemeName,
substring(als.log,27) as script,
 case when ate.intState=0 then '排队中' when ate.intState=1 then '执行中' when ate.intState=2 then 'PASS'
              when ate.intState=3 then 'Error' else '未执行' end state
              from auto_schemes aus
              INNER JOIN users u on u.id=aus.intCreaterID
              INNER JOIN auto_task_relations atr on atr.intSchemeID=aus.id
              INNER JOIN auto_tasks ats on ats.id=atr.intTaskID
              LEFT JOIN auto_task_execs ate on ate.intTaskID=ats.id
              LEFT JOIN auto_projects apro on apro.id=ats.intProjectID
              INNER JOIN api_logs als on als.apitaskexecid=ate.id
              where ats.id in (SELECT DISTINCT intTaskID from auto_task_execs where id='$execTaskId') 
and ate.id='$execTaskId'  AND als.log like '%待测试%'   
ORDER BY ats.chrTaskName desc");
        return $result;
    }
	
	
	
	
	
    public function getLogForNew($taskid,$taskexecid)
    {
        $result = [];
        //$execTaskId=DB::select("SELECT id FROM auto_task_execs WHERE intTaskID=$taskexecid");
        $execTaskId = $taskexecid;
//        $execApiTaskId = DB::select("SELECT id FROM api_logs WHERE apitaskexecid=$taskexecid AND apisehemesid=$apisehemesid AND apitaskid=$apitaskid");
//        $execApiTaskId = $execApiTaskId[0] ->id;
        //任务名称查询
        $task_name = DB::select("SELECT ats.id,chrTaskName FROM auto_tasks ats 
JOIN auto_task_execs ate ON ate.intTaskID=ats.id
WHERE ate.id=$execTaskId");
        $result['task_name'] = $task_name[0];
        //任务详情
        //4.18--为了少引用其他的SQL,这里修丰富了SQL内容
        //修改之后 又改变了查询逻辑,基于auto_task_exec表
        $result['task_list'] = DB::select("SELECT DISTINCT ats.id,chrTaskName,apro.chrProjectName,u.chrUserName,
ats.updated_at,
arp.updated_at endtime,
ate.updated_at exectime,
CASE WHEN ate.intState = 0 THEN'排队中'WHEN ate.intState = 1 THEN'执行中'WHEN ate.intState = 2 THEN'PASS'WHEN ate.intState = 3 THEN'ERROR' ELSE'未执行'END state,
ate.chrBrowserNames,ate.id taskExecID
,ats.intProjectID projectId
FROM auto_task_execs ate
JOIN auto_tasks ats ON ate.intTaskID=ats.id
INNER JOIN users u ON u.id = ats.intCreaterID
INNER JOIN auto_projects apro ON apro.id = ats.intProjectID
LEFT JOIN auto_logs al ON al.intExecTaskID = ate.id
LEFT JOIN auto_reports arp ON arp.id = al.intReportID
WHERE ats.id IN (SELECT DISTINCT intTaskID FROM auto_task_execs WHERE id =$execTaskId)AND ate.id =$execTaskId
ORDER BY ats.chrTaskName DESC");

        //案例详情
        //将API的结果统计加入到这里
//        $result[''] = DB::select("SELECT DISTINCT aus.id,
//				ats.chrTaskName, chrSchemeName schemeName,apro.chrProjectName projectName,u.chrUserName createUser, case when ate.intState=0 then '排队中' when ate.intState=1 then '执行中' when ate.intState=2 then '执行成功'
//              when ate.intState=3 then '执行失败' else '未执行' end state,ate.chrBrowserNames browserNames,ats.intProjectID projectId ,ate.id taskExecID
//              from auto_schemes aus
//              INNER JOIN users u on u.id=aus.intCreaterID
//              INNER JOIN auto_task_relations atr on atr.intSchemeID=aus.id
//              INNER JOIN auto_tasks ats on ats.id=atr.intTaskID
//              LEFT JOIN auto_task_execs ate on ate.intTaskID=ats.id
//              LEFT JOIN auto_projects apro on apro.id=ats.intProjectID
//              where ats.id in (SELECT DISTINCT intTaskID from auto_task_execs where id=$execTaskId) and ate.id=$execTaskId ORDER BY ats.chrTaskName desc");

        $result['scheme_list'] = DB::select("SELECT DISTINCT aus.id,als.log apilog ,
				ats.chrTaskName, chrSchemeName schemeName,apro.chrProjectName projectName,apro.chrProjectName projectName,aus.updated_at as updated_at,
ate.updated_at as exectime,u.chrUserName createUser, case when ate.intState=0 then '排队中' when ate.intState=1 then '执行中' when ate.intState=2 then 'PASS'
              when ate.intState=3 then 'ERROR' else '未执行' end state,ate.chrBrowserNames browserNames,ats.intProjectID projectId ,ate.id taskExecID
              from auto_schemes aus
              INNER JOIN users u on u.id=aus.intCreaterID
              INNER JOIN auto_task_relations atr on atr.intSchemeID=aus.id
              INNER JOIN auto_tasks ats on ats.id=atr.intTaskID
              LEFT JOIN auto_task_execs ate on ate.intTaskID=ats.id
              LEFT JOIN auto_projects apro on apro.id=ats.intProjectID
              INNER JOIN api_logs als on als.apitaskexecid=ate.id
              where ats.id in (SELECT DISTINCT intTaskID from auto_task_execs where id=$execTaskId) and ate.id=$execTaskId  AND als.log like \"%场景统计结果%\"  ORDER BY ats.chrTaskName desc");

        if ($result['scheme_list']) {
            foreach ($result['scheme_list'] as $list) {
                $result['exec_time'] = substr($list->apilog, 0, 19);
                $list->apilog = substr($list->apilog, 41);
            }
        }

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

//        //API结果统计
//        $result['api_list'] = DB::select("SELECT
//            als.id ,ats.chrTaskName,als.log
//            FROM
//            api_logs als
//            INNER JOIN auto_tasks ats ON ats.id = als.apitaskid
//            WHERE
//            als.id=$execApiTaskId and als.log  LIKE \"%场景统计结果%\"" );

        $result['script_info']=DB::select("SELECT 
als.id as id,
chrSchemeName schemeName,
substring(als.log,27) as script,
 case when ate.intState=0 then '排队中' when ate.intState=1 then '执行中' when ate.intState=2 then 'PASS'
              when ate.intState=3 then 'Error' else '未执行' end state
              from auto_schemes aus
              INNER JOIN users u on u.id=aus.intCreaterID
              INNER JOIN auto_task_relations atr on atr.intSchemeID=aus.id
              INNER JOIN auto_tasks ats on ats.id=atr.intTaskID
              LEFT JOIN auto_task_execs ate on ate.intTaskID=ats.id
              LEFT JOIN auto_projects apro on apro.id=ats.intProjectID
              INNER JOIN api_logs als on als.apitaskexecid=ate.id
              where ats.id in (SELECT DISTINCT intTaskID from auto_task_execs where id='$execTaskId') 
and ate.id='$execTaskId'  AND als.log like '%待测试%'   
ORDER BY ats.chrTaskName desc");
        return $result;
    }





    public function getAllInfo($taskid,$taskexecid)
    {
        $result = [];
        //任务统计
        $result['task_list'] = DB::select("SELECT 
COUNT(ats.id) as count,
CASE WHEN ate.intState = 0 THEN'排队中'WHEN ate.intState = 1 THEN'执行中'WHEN ate.intState = 2 THEN'PASS'WHEN ate.intState = 3 THEN'ERROR' ELSE'未执行'END name,
case when ate.intState=0 then 'not' when ate.intState=1 then 'ing' when ate.intState=2 then 'success' else 'fail' end state
FROM auto_task_execs ate
JOIN auto_tasks ats ON ate.intTaskID=ats.id
INNER JOIN users u ON u.id = ats.intCreaterID
INNER JOIN auto_projects apro ON apro.id = ats.intProjectID
LEFT JOIN auto_logs al ON al.intExecTaskID = ate.id
LEFT JOIN auto_reports arp ON arp.id = al.intReportID
WHERE ats.id IN (SELECT DISTINCT intTaskID FROM auto_task_execs WHERE id =$taskexecid)AND ate.id =$taskexecid
ORDER BY ats.chrTaskName DESC");

        //案例统计
        $result['scheme_list'] = DB::select("SELECT COUNT(ats.id) as count,
case when ate.intState=0 then '排队中' when ate.intState=1 then '执行中' when ate.intState=2 then 'PASS'
              when ate.intState=3 then 'ERROR' else '未执行' end name,
case when ate.intState=0 then 'not' when ate.intState=1 then 'ing' when ate.intState=2 then 'success' else 'fail' end state
              from auto_schemes aus
              INNER JOIN users u on u.id=aus.intCreaterID
              INNER JOIN auto_task_relations atr on atr.intSchemeID=aus.id
              INNER JOIN auto_tasks ats on ats.id=atr.intTaskID
              LEFT JOIN auto_task_execs ate on ate.intTaskID=ats.id
              LEFT JOIN auto_projects apro on apro.id=ats.intProjectID
              INNER JOIN api_logs als on als.apitaskexecid=ate.id
              where ats.id in (SELECT DISTINCT intTaskID from auto_task_execs where id=$taskexecid) and ate.id=$taskexecid  AND als.log like \"%场景统计结果%\"  ORDER BY ats.chrTaskName desc");

        //脚本统计
        $result['script_info']=DB::select("SELECT 
COUNT(ats.id) as count,
case when ate.intState=0 then '排队中' when ate.intState=1 then '执行中' when ate.intState=2 then 'PASS'
              when ate.intState=3 then 'ERROR' else '未执行' end name,
case when ate.intState=0 then 'not' when ate.intState=1 then 'ing' when ate.intState=2 then 'success' else 'fail' end state
              from auto_schemes aus
              INNER JOIN users u on u.id=aus.intCreaterID
              INNER JOIN auto_task_relations atr on atr.intSchemeID=aus.id
              INNER JOIN auto_tasks ats on ats.id=atr.intTaskID
              LEFT JOIN auto_task_execs ate on ate.intTaskID=ats.id
              LEFT JOIN auto_projects apro on apro.id=ats.intProjectID
              INNER JOIN api_logs als on als.apitaskexecid=ate.id
              where ats.id in (SELECT DISTINCT intTaskID from auto_task_execs where id='$taskexecid') 
and ate.id='$taskexecid'  AND als.log like '%待测试%'   
ORDER BY ats.chrTaskName desc");
        return $result;
    }



    public function getAllInfoSuccess($taskid,$taskexecid)
    {
        $result = [];
        //任务执行结果统计
        $result['task_list'] = DB::select("SELECT 
COUNT(ats.id) as count,
CASE WHEN ate.intState = 0 THEN'排队中'WHEN ate.intState = 1 THEN'执行中'WHEN ate.intState = 2 THEN'PASS'WHEN ate.intState = 3 THEN'ERROR' ELSE'未执行'END name,
case when ate.intState=0 then 'not' when ate.intState=1 then 'ing' when ate.intState=2 then 'success' else 'fail' end state
FROM auto_task_execs ate
JOIN auto_tasks ats ON ate.intTaskID=ats.id
INNER JOIN users u ON u.id = ats.intCreaterID
INNER JOIN auto_projects apro ON apro.id = ats.intProjectID
LEFT JOIN auto_logs al ON al.intExecTaskID = ate.id
LEFT JOIN auto_reports arp ON arp.id = al.intReportID
WHERE ats.id IN (SELECT DISTINCT intTaskID FROM auto_task_execs WHERE id =$taskexecid)AND ate.id =$taskexecid
ORDER BY ats.chrTaskName DESC");

        //任务执行次数统计
        $result['task_times'] = DB::select("SELECT DATE_FORMAT(updated_at,'%Y-%m-%d') as time,
sum(intState) times 
from auto_task_execs where intTaskID='$taskid'  GROUP BY  time ");

        //案例执行结果统计
        $result['scheme_list'] = DB::select("SELECT COUNT(ats.id) as count,
case when ate.intState=0 then '排队中' when ate.intState=1 then '执行中' when ate.intState=2 then 'PASS'
              when ate.intState=3 then 'ERROR' else '未执行' end name,
case when ate.intState=0 then 'not' when ate.intState=1 then 'ing' when ate.intState=2 then 'success' else 'fail' end state
              from auto_schemes aus
              INNER JOIN users u on u.id=aus.intCreaterID
              INNER JOIN auto_task_relations atr on atr.intSchemeID=aus.id
              INNER JOIN auto_tasks ats on ats.id=atr.intTaskID
              LEFT JOIN auto_task_execs ate on ate.intTaskID=ats.id
              LEFT JOIN auto_projects apro on apro.id=ats.intProjectID
              INNER JOIN api_logs als on als.apitaskexecid=ate.id
              where ats.id in (SELECT DISTINCT intTaskID from auto_task_execs where id=$taskexecid) and ate.id=$taskexecid  AND als.log like \"%场景统计结果%\"  ORDER BY ats.chrTaskName desc");


        $result['scheme_times'] = DB::select("SELECT DATE_FORMAT(updated_at,'%Y-%m-%d') as time,
sum(intState) times 
from auto_task_execs where intTaskID='$taskid'  GROUP BY  time ");

        return $result;
    }
	
	
	
	
	
	
	


}

?>