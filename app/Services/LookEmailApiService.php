<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/31
 * Time: 16:12
 */

namespace App\Services;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LookEmailApiService
{
    public function lookapi($taskexecid)
    {
		Log::info('ID:'.$taskexecid);
        $result = [];
        $execTaskId=DB::select("SELECT id FROM auto_task_execs WHERE intTaskID=$taskexecid  ORDER BY updated_at desc ");
        $execTaskId = $execTaskId[0]->id;
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
CASE WHEN ate.intState = 0 THEN'排队中'WHEN ate.intState = 1 THEN'执行中'WHEN ate.intState = 2 THEN'执行成功'WHEN ate.intState = 3 THEN'执行失败'ELSE'未执行'END state,
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
//        $result['scheme_list'] = DB::select("SELECT DISTINCT aus.id,
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
				ats.chrTaskName, chrSchemeName schemeName,apro.chrProjectName projectName,u.chrUserName createUser, case when ate.intState=0 then '排队中' when ate.intState=1 then '执行中' when ate.intState=2 then '执行成功'
              when ate.intState=3 then '执行失败' else '未执行' end state,ate.chrBrowserNames browserNames,ats.intProjectID projectId ,ate.id taskExecID
              from auto_schemes aus
              INNER JOIN users u on u.id=aus.intCreaterID
              INNER JOIN auto_task_relations atr on atr.intSchemeID=aus.id
              INNER JOIN auto_tasks ats on ats.id=atr.intTaskID
              LEFT JOIN auto_task_execs ate on ate.intTaskID=ats.id
              LEFT JOIN auto_projects apro on apro.id=ats.intProjectID
              INNER JOIN api_logs als on als.apitaskexecid=ate.id
              where ats.id in (SELECT DISTINCT intTaskID from auto_task_execs where id=$execTaskId) and ate.id=$execTaskId  AND als.log like '%场景统计结果%'  ORDER BY ats.chrTaskName desc");

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
chrSchemeName schemeName,
substring(als.log,27) as script,
 case when ate.intState=0 then '排队中' when ate.intState=1 then '执行中' when ate.intState=2 then '执行成功'
              when ate.intState=3 then '执行失败' else '未执行' end state
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
}