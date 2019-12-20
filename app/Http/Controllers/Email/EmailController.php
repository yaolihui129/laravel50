<?php
namespace App\Http\Controllers\Email;
use App\Http\Controllers\Controller;
use App\Services\LookEmailService;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/17
 * Time: 14:48
 */
//4.18写的
class EmailController extends Controller{
    public  function index($taskexecid)
    {
            $query=new LookEmailService();
            $params=$query->look($taskexecid);
            $data['chrTaskName'] = $params['task_name']->chrTaskName;
//            $data['task_list'] = array_get($params,'task_list',[]);
            $data['task_list'] = array_get($params, 'task_list', []);
            $data['scheme_list'] = array_get($params, 'scheme_list', []);
            $data['script_sum'] = array_get($params, 'script_sum', []);
            $data['project_sum'] = array_get($params, 'project_sum', []);

            return view("emails.lookemail")->with("data",$data);



    }



}

//4.17
//class EmailsController extends Controller{
//    //以下是报告blade中相关的所有的SQL;
//    public function LookEmails($id){
//        $result = [];
//        $execTaskId=DB::select("SELECT id FROM auto_task_execs WHERE intTaskID=$id");
//        //任务详情
//        $result['task_list'] = DB::select("SELECT distinct ats.id, chrTaskName,apro.chrProjectName, u.chrUserName,ats.updated_at, case when ate.intState=0 then '排队中' when ate.intState=1 then '执行中' when ate.intState=2 then '执行成功' when ate.intState=3 then '执行失败' else '未执行' end state,ate.chrBrowserNames, ate.id taskExecID,ats.intProjectID projectId
//              from auto_tasks ats INNER JOIN users u on u.id=ats.intCreaterID LEFT JOIN auto_task_execs ate on ate.intTaskID=ats.id INNER JOIN auto_projects apro on apro.id=ats.intProjectID where  ats.id in (SELECT DISTINCT intTaskID from auto_task_execs where id=$execTaskId) and ate.id=$execTaskId ORDER BY ats.chrTaskName desc
//			");
//        //案例详情
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
//        //脚本日志统计分析
//        $result['script_sum'] = DB::select("SELECT count(*) count, l.chrDescription,COUNT(CASE WHEN l.chrResult in ('PASS','WARNING') THEN '成功' END)/count(*)*100 passlv from auto_logs l LEFT JOIN auto_scripts s on l.intScriptID=s.id	where l.intExecTaskID=$execTaskId and chrDescription!='' GROUP BY l.chrDescription");
//        // dd($result['script_sum']);
//        //功能模块覆盖详情
//        $result['project_sum'] = DB::select("SELECT ppid,ppchrProjectName,count(1) allscripts,sum(case when execScriptID is not null then 1 else 0 end) execs,
//			sum(case when execPass=1 then 1 else 0 end ) execPass, 	sum(CASE WHEN execScriptID IS NOT NULL THEN 1	ELSE 0 END)/count(1)*100 execlv,
//			sum(CASE WHEN execPass = 1 THEN	1	ELSE 0 END)/sum(CASE WHEN execScriptID IS NOT NULL THEN	1	ELSE 0 END)*100 passlv
//			from
//    		(select p.ppid,p.ppchrProjectName,s.id ppScriptID,s.chrScriptName ppScriptName,l.execScriptID,
//    		(case when l.scriptAct=scriptActPass then 1 else 0 end) execPass
//    		from
//    		(select c3.id ppid,c3.chrProjectName ppchrProjectName from auto_projects c1 join auto_projects c2 on c1.id=c2.intParentID
//			join auto_projects c3 on c2.id=c3.intParentID where c3.id in (SELECT intProjectID from auto_scripts where id in (SELECT DISTINCT intScriptID from auto_logs where intExecTaskID = $execTaskId))) p
//    		join auto_scripts s on s.intProjectID=p.ppid
//    		left join (select intScriptID execScriptID,count(*) scriptAct,sum(case when chrresult in ('PASS','WARNING') then 1 else 0 end) scriptActPass
//          	from auto_logs where intExecTaskID=$execTaskId group by intScriptID) l on l.execScriptID=s.id
//			) tmp group by ppid,ppchrProjectName order by ppchrProjectName");
//
//        return $result;
//
//    }
//}
