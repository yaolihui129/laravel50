<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ReportChartService {
	function __construct() {
	}
	
	/**
	 *
	 * @param unknown $user        	
	 */
	public function getWebTaskPie($taskId) {
		//$companyId = $user->intCompanyID;
		$taskExec=DB::select("select id from auto_task_execs where intTaskId='$taskId' ORDER BY updated_at desc LIMIT 0,1");
        $taskExecId=$taskExec[0]->id;
//		$rows = DB::select ( "SELECT case when intState=0 then '未执行'
//				when intState=1 then '执行中' when intState=2 then 'PASS' else 'Error' end name,
//				count(id) as value,case when intState=0 then 'not' when intState=1 then 'ing'
//				when intState=2 then 'success' else 'fail' end state from (
//				select ats.id,IFNULL(ate.intState,0) intState from auto_tasks ats
//				left join auto_task_execs ate on ate.intTaskID=ats.id and ate.intTimerTaskID=0
//				where ats.intTaskType=0 and ats.intCompanyID=$companyId
//				union ALL
//				select att.id,IFNULL(ate.intState,0) intState from auto_timer_tasks att
//				left join auto_task_execs ate on ate.intTaskID=att.id and ate.intTimerTaskID<>0
//				where att.intCompanyID=$companyId
//				)a GROUP BY intState" );
        //新逻辑
        $rows=DB::select("SELECT
case when state='TRUE' then '未执行' when state='FAIL' then '执行中' when state='PASS' then 'PASS' else 'Error' end name,
count(script) as value,
case when state='TRUE' then 'not' when state='FAIL' then 'ing' when state='PASS' then 'success' else 'fail' end state
from (
SELECT
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
              where ats.id in (SELECT DISTINCT intTaskID from auto_task_execs where id='$taskExecId')
and ate.id='$taskExecId'  AND als.log like '%待测试%'
ORDER BY ats.chrTaskName desc
				)a
GROUP BY state");
		return $rows;
	}
	
	/**
	 *
	 * @param unknown $user        	
	 */
	public function getWebTaskLine($user, $cycle) {
		$companyId = $user->intCompanyID;
		$df = "";
		switch ($cycle) {
			case "month" :
				$df = "%Y-%m";
				break;
			case "year" :
				$df = "%Y";
				break;
			case "day" :
				$df = "%Y-%m-%d";
				break;
			case "week" :
				$df = "%U";
				break;
		}
		$rows = DB::select ( "select name,COUNT(*) value from (
				SELECT arep.id,DATE_FORMAT(arep.created_at,'$df') name 
				from auto_timer_tasks att
				INNER JOIN auto_reports arep on arep.intTaskID=att.id and arep.intTaskType=2
				where att.intCompanyID=$companyId
				UNION ALL
				SELECT arep.id,DATE_FORMAT(arep.created_at,'$df') name from auto_tasks ats
				INNER JOIN auto_reports arep on arep.intTaskID=ats.id and arep.intTaskType=1 
				where ats.intTaskType=0 and ats.intCompanyID=$companyId)a GROUP BY name" );
		return $rows;
	}
	
	/**
	 *
	 * @param unknown $user        	
	 */
	public function getSchemeChart($user, $cycle) {
		$companyId = $user->intCompanyID;
		$df = "";
		switch ($cycle) {
			case "month" :
				$df = "%Y-%m";
				break;
			case "year" :
				$df = "%Y";
				break;
			case "day" :
				$df = "%Y-%m-%d";
				break;
			case "week" :
				$df = "%U";
				break;
		}
		$rows = DB::select ( "select name,count(id) value from (
				select DATE_FORMAT(created_at,'$df') name,id 
				from auto_schemes asch where intFlag=0 and asch.intCompanyID=$companyId
				)a GROUP BY name" );
		return $rows;
	}

    /**
     *
     * @param unknown $user
     * @return
     */
	public function getScriptChart($user) {
		$companyId = $user->intCompanyID;
//		$rows = DB::select ( "select COUNT(*) value from auto_scripts asci
//				where intFlag=0 and asci.intCompanyID=$companyId" );

		$rows = DB::select("
		    select 'ui',count(*) value 
                from auto_scripts a
                LEFT JOIN auto_projects b on b.id=a.intTopProjectID
                where b.chrProjectName NOT like '%API项目%' 
                and b.chrProjectName NOT like '%App%'
            union all 
            select 'api', count(*) 
                from auto_scripts a
                LEFT JOIN auto_projects b on b.id=a.intTopProjectID
                where b.chrProjectName like '%API项目%'
            union all
            select 'app', count(*) value 
                from  app_information
            union all 
            select 'control', count(*) value 
                from resource_machines
		");

		return $rows;
	}




    public function getWebTaskPie_task($user,$taskId) {
        $companyId = $user->intCompanyID;
        $taskExec=DB::select("select id from auto_task_execs where intTaskId='$taskId' ORDER BY updated_at desc LIMIT 0,1");
        $taskExecId=$taskExec[0]->id;
//		$rows = DB::select ( "SELECT case when intState=0 then '未执行'
//				when intState=1 then '执行中' when intState=2 then 'PASS' else 'Error' end name,
//				count(id) as value,case when intState=0 then 'not' when intState=1 then 'ing'
//				when intState=2 then 'success' else 'fail' end state from (
//				select ats.id,IFNULL(ate.intState,0) intState from auto_tasks ats
//				left join auto_task_execs ate on ate.intTaskID=ats.id and ate.intTimerTaskID=0
//				where ats.intTaskType=0 and ats.intCompanyID=$companyId
//				union ALL
//				select att.id,IFNULL(ate.intState,0) intState from auto_timer_tasks att
//				left join auto_task_execs ate on ate.intTaskID=att.id and ate.intTimerTaskID<>0
//				where att.intCompanyID=$companyId
//				)a GROUP BY intState" );
        //新逻辑
        $rows=DB::select("SELECT
case when state='TRUE' then '未执行' when state='FAIL' then '执行中' when state='PASS' then 'PASS' else 'Error' end name,
count(script) as value,
case when state='TRUE' then 'not' when state='FAIL' then 'ing' when state='PASS' then 'success' else 'fail' end state
from (
SELECT
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
              where ats.id in (SELECT DISTINCT intTaskID from auto_task_execs where id='$taskExecId')
and ate.id='$taskExecId'  AND als.log like '%待测试%'
ORDER BY ats.chrTaskName desc
				)a
GROUP BY state");
        return $rows;
    }

    /**
     * @param $user
     * @return mixed
     */
    public function getWebTaskPieForIndex($user) {
        $companyId = $user->intCompanyID;
		$rows = DB::select ( "SELECT case when intState=0 then '未执行'
				when intState=1 then '执行中' when intState=2 then 'PASS' else 'Error' end name,
				count(id) as value,case when intState=0 then 'not' when intState=1 then 'ing'
				when intState=2 then 'success' else 'fail' end state from (
				select ats.id,IFNULL(ate.intState,0) intState from auto_tasks ats
				left join auto_task_execs ate on ate.intTaskID=ats.id and ate.intTimerTaskID=0
				where ats.intTaskType=0 and ats.intCompanyID=$companyId
				union ALL
				select att.id,IFNULL(ate.intState,0) intState from auto_timer_tasks att
				left join auto_task_execs ate on ate.intTaskID=att.id and ate.intTimerTaskID<>0
				where att.intCompanyID=$companyId
				)a GROUP BY intState" );
        return $rows;
    }

    /**
     * @param $user
     * @return mixed
     */
    public function getApiTaskPieForIndex($user){
        $companyId = $user->intCompanyID;
        $rows = DB::select ("
                SELECT 
					case 
						when intState=0  then '未执行'
						when intState=1 then '执行中' 
						when intState=2 then '执行成功' 
						else 'Error' end name,
						count(id) as value,
					case 
						when intState=0  then 'not' 
						when intState=1 then 'ing'
						when intState=2 then 'success' 
						else 'fail' end state 
				from (
					select 
						ats.id as id,
						if(IFNULL(ate.intState,'')='','0',ate.intState) intState
					from auto_tasks ats
					INNER JOIN users u on u.id=ats.intCreaterID
					LEFT JOIN auto_task_execs ate 
						on ate.intTaskID=ats.id 
						and ate.intFlag=0 
						and ate.intTimerTaskID=0 
					INNER JOIN auto_projects apro 
						on apro.id=ats.intProjectID
					where 
						ats.intFlag=0  
						and ats.intProjectID ='502'  
						or ats.intProjectID ='503' 
						or ats.intProjectID ='505'
						or ats.intProjectID ='506' 
						or ats.intProjectID ='533'
						or ats.intProjectID ='650' 
						or ats.intProjectID ='651'
						or ats.intProjectID ='652' 
						or ats.intProjectID ='653'
						or ats.intProjectID ='654' 
						or ats.intProjectID ='660'
						and ats.intTaskType=0 
				)a   GROUP BY intState
            " );
        return $rows;
    }


    /**
     * @param $user
     * @return mixed
     */
    public function getAppTaskPieForIndex($user){
        $companyId = $user->intCompanyID;
        $rows = DB::select ("
            SELECT 
                    case 
                        when intState=0 then '未执行'
                        when intState=1 then '执行中' 
                        when intState=2 then '执行成功' 
                        else 'Error' end name,
                    count(id) as value,
                    case  
                        when intState=0 then 'not' 
                        when intState=1 then 'ing'
                        when intState=2 then 'success' 
                        else 'fail' end state 
                from (
                    select 
                    att.id id,
                    ate.intState
                    from 
                    auto_tasks att
                    join users us ON us.id=att.intCreaterID
                    join auto_scripts ats ON ats.id=att.intProjectID
                    join auto_projects apro ON apro.id=ats.intProjectID
                    LEFT JOIN auto_task_execs ate on ate.intTaskID=att.id  
                    where ats.intTopProjectID='454'
               )a GROUP BY intState
            " );
        return $rows;
    }

    /**
     * @param $user
     * @return mixed
     */
    public function getDispatchTaskPieForIndex($user){
        $companyId = $user->intCompanyID;
        $rows = DB::select ("
                SELECT 
                    case 
                        when intState=0 then '未执行'
                        when intState=1 then '执行中' 
                        when intState=2 then '执行成功' 
                        else 'Error' end name,
                    count(id) as value,
                    case  
                        when intState=0 then 'not' 
                        when intState=1 then 'ing'
                        when intState=2 then 'success' 
                        else 'fail' end state 
                from (
                    select id,chrNowState as intState from resource_plans
               )a GROUP BY intState
            " );
        return $rows;
    }


}

?>