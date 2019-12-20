<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/18
 * Time: 10:02
 */

namespace App\Services;


use Illuminate\Support\Facades\DB;

class LookEmailService
{
    public function look($taskexecid){
//        $query= DB::select("select id from auto_task_execs where intTaskID='$taskexecid'");
//        return $query;
//
        //wormqueueservice----------------------------------------引用過來的
        $result = [];
        $execTaskId=DB::select("SELECT id FROM auto_task_execs WHERE intTaskID=$taskexecid");
        $execTaskId = $execTaskId[0]->id;
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
//        $result['task_list'] = $task_list[0];
//        dd($task_list);
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
        //---------------------------------------------

    }

}