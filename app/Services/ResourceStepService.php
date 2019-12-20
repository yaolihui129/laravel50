<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\SysMachine;
use Illuminate\Support\Facades\Log;

class ResourceStepService {

    /**
     *
     * @param unknown $secho
     * @param unknown $iDisplayStart
     * @param unknown $iDisplayLength
     * @return string
     */
    public function getTaskList($secho, $iDisplayStart, $iDisplayLength, $user, $search, $wl) {
        $search = $this->structureSearchSQL ( $search );
        $allcount = $this->getTaskCount ( $user, $search, $wl );
        $tasks = "{'sEcho': " . $secho . ",'iTotalRecords': " . $allcount . ",'iTotalDisplayRecords':" . $allcount . ",'aaData': ";
        $rows = DB::select ( "select 
id,
chrStepId,
chrStepName,
updated_at
 from resource_steps  
 where $search ORDER BY id desc  
				limit ?,?", [
            $iDisplayStart,
            $iDisplayLength
        ] );
        $tasks .= json_encode ( $rows );
        $tasks .= "}";
        return $tasks;
    }

    private function structureSearchSQL($search) {
        if (! empty ( $search )) {
            $sql = array ();
            $taskName = trim ( $search ["taskName"] );
            $creater = trim ( $search ["creater"] );
            if ($taskName)
                array_push ( $sql, "chrStepId='$taskName'" );
            if ($creater)
                array_push ( $sql, "chrStepName='$creater'" );
            $sql = implode ( " and ", $sql );
        }
        if (empty ( $sql ))
            $sql = "1=1";
        return $sql;
    }

    private function getTaskCount($user, $search, $wl) {
        $rows = DB::select ( "select count(*) allCount from resource_steps" );
        return $rows [0]->allCount;
    }


    public function insert($task, $user) {
        DB::beginTransaction ();
        try {
            //$auto_task = new AutoTask ();
            //$taskId = $auto_task->id;
            $taskName=$task['taskName'];
            $MacIP=$task['MacIP'];
            $belongTo=$task['belongTo'];
            $nowtime=date('Y-m-d H:i:s');
            $intCreaterID=$user->id;

            DB::select("insert into resource_steps (chrStepId,  chrStepName, created_at, updated_at)
values('$taskName','$MacIP','$nowtime','$nowtime')");

            DB::commit ();
            return $MacIP;
        } catch ( \Exception $e ) {
            DB::rollback ();
            throw $e;
        }
    }


    public function delete($ids, $user) {
        DB::beginTransaction ();
        try {
            $arr=explode(',',$ids);
            foreach ($arr as $id){
                //$ifexit=DB::select("select chrNowState from resource_steps where id in ('$id')");
                //$newState=$ifexit[0]->chrNowState;
                //if($newState==2 || $newState==3){
                    DB::delete ( "delete from resource_steps where id in ('$id')" );
                //}
            }
            DB::commit ();
        } catch ( \Exception $e ) {
            DB::rollback ();
            throw $e;
        }
    }


    public function checkAdd($taskName,$MacIP,$belongTo)
    {
        $ifExit_taskName=DB::select("select chrStepId from resource_steps where chrStepId='$taskName' ");
        $ifExit_MacIP=DB::select("select chrStepName from resource_steps where chrStepName='$MacIP' ");
        if(!empty($ifExit_taskName)){
            $info=1;
            return $info;
        } else if(!empty($ifExit_MacIP)) {
            $info=2;
            return $info;
        }else{
            $info=0;
            return $info;
        }

    }


    public function getTaskById($id, $user) {
        $rows = DB::select ( "select 
chrMachName as chrTaskName,
chrMacIP as MacIP,
chrBelongTo as belongTo
		from  resource_steps 
		where id='$id'" );
        $task = array (
            "taskName" => $rows [0]->chrTaskName,
            "MacIP" => $rows [0]->MacIP,
            "belongTo"=>$rows[0]->belongTo
        );
        return $task;
    }




    public function update($id, $task, $user,$taskName,$MacIP,$belongTo) {
        DB::beginTransaction ();
        try {
            DB::update ( "update resource_steps 
set chrMachName='$taskName',
chrMacIP='$MacIP',
chrBelongTo='$belongTo' 
where id='$id' ");
            DB::commit ();
        } catch ( \Exception $e ) {
            DB::rollback ();
            throw $e;
        }
    }

    public function getTaskExecState($id,$taskName,$MacIP,$belongTo) {
//        return DB::select ( "select id,intTaskID taskId,chrBrowserIDs browserIds,chrEmails emails,
//				case when chrEmails='' then 0 else 1 end sendEmail
//				from auto_task_execs where intFlag=0 and intTimerTaskID=0
//				and intTaskID=$taskId and intState in (0,1)" );

        $ifExit_taskName=DB::select("select * from resource_steps where chrMachName='$taskName'and id not in('$id') ");
        $ifExit_MacIP=DB::select("select * from resource_steps where chrMachName='$MacIP' and id not in('$id') ");
        $ifExit_belongTo=DB::select("select * from resource_steps where chrMachName='$belongTo' and id not in('$id') ");
        if(empty($ifExit_taskName)) {
            if(empty($ifExit_MacIP)){
                return $MacIP;
            }
        }

    }


    public function checkDel($id)
    {

        return DB::select("select chrStepId from resource_steps where id in ('$id') and chrStepId=''");
    }

    public function checkEdit($id)
    {

        return DB::select("select chrNowState from resource_steps where id in ('$id') and chrNowState='1'");
    }
}

?>