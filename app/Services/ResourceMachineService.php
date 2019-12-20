<?php

namespace App\Services;

use App\AutoTask;
use App\AutoTaskRelation;
use Illuminate\Support\Facades\DB;
use App\SysMachine;
use Illuminate\Support\Facades\Log;

class ResourceMachineService {

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
        $rows = DB::select ( "select id,chrMachName taskName,chrMacIP projectName,chrBelongTo createUser,updated_at browserNames,
				case when chrNowState=0 then '排队中' when chrNowState=1 then '执行中' when chrNowState=2 then '执行成功' 
				when chrNowState=3 then '执行失败' else '未执行' end state
 from resource_machines where $search ORDER BY id desc  
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
                array_push ( $sql, "chrMachName='$taskName'" );
            if ($creater)
                array_push ( $sql, "chrBelongTo='$creater'" );
            $sql = implode ( " and ", $sql );
        }
        if (empty ( $sql ))
            $sql = "1=1";
        return $sql;
    }

    private function getTaskCount($user, $search, $wl) {
        $rows = DB::select ( "select count(*) allCount from resource_machines" );
        return $rows [0]->allCount;
    }


    public function insert($task, $user) {
        DB::beginTransaction ();
        try {
            $auto_task = new AutoTask ();
            $taskId = $auto_task->id;
            $taskName=$task['taskName'];
            $MacIP=$task['MacIP'];
            $belongTo=$task['belongTo'];
            $hostIp =$task['hostIp'];
            $vmName  =$task['vmName'];
            $userName =$task['userName'];
            $password =$task['password'];

            $nowtime=date('Y-m-d H:i:s');
            $intCreaterID=$user->id;

                DB::select("insert into resource_machines (chrMachName,  chrMacIP, chrMaxCount, chrNowCount, chrNowState, intCreaterID, intModifyID, chrBelongTo,hostIp,vmName,userName,password,created_at, updated_at)
values('$taskName','$MacIP',1,0,2,'$intCreaterID',1,'$belongTo','$hostIp','$vmName','$userName','$password','$nowtime','$nowtime')");

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
                $ifexit=DB::select("select chrNowState from resource_machines where id in ('$id')");
                $newState=$ifexit[0]->chrNowState;
                if($newState==2 || $newState==3){
                    DB::delete ( "delete from resource_machines where id in ('$id')" );
                }
            }
            DB::commit ();
        } catch ( \Exception $e ) {
            DB::rollback ();
            throw $e;
        }
    }


    public function checkAdd($taskName,$MacIP,$belongTo)
    {
        $ifExit_taskName=DB::select("select chrMachName from resource_machines where chrMachName='$taskName' ");
        $ifExit_MacIP=DB::select("select chrMacIp from resource_machines where chrMacIp='$MacIP' ");
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
chrBelongTo as belongTo,
hostIp,vmName,userName,password
		from  resource_machines 
		where id='$id'" );
        $task = array (
            "taskName" => $rows [0]->chrTaskName,
            "MacIP" => $rows [0]->MacIP,
            "belongTo"=>$rows[0]->belongTo,
            "hostIp"=>$rows[0]->hostIp,
            "vmName" =>$rows[0]->vmName,
            "userName"=>$rows[0]->userName,
            "password" =>$rows[0]->password
        );
        return $task;
    }




    public function update($id, $task, $user,$taskName,$MacIP,$belongTo,$hostIp,$vmName,$userName,$password) {
        DB::beginTransaction ();
        try {
            DB::update ( "update resource_machines 
set chrMachName='$taskName',
chrMacIP='$MacIP',
chrBelongTo='$belongTo',
hostIp='$hostIp',
vmName='$vmName',
userName='$userName',
password='$password' 
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

        $ifExit_taskName=DB::select("select * from resource_machines where chrMachName='$taskName'and id not in('$id') ");
        $ifExit_MacIP=DB::select("select * from resource_machines where chrMachName='$MacIP' and id not in('$id') ");
        $ifExit_belongTo=DB::select("select * from resource_machines where chrMachName='$belongTo' and id not in('$id') ");
        if(empty($ifExit_taskName)) {
            if(empty($ifExit_MacIP)){
                return $MacIP;
            }
        }

    }


    public function checkDel($id)
    {

        return DB::select("select chrNowState from resource_machines where id in ('$id') and chrNowState='1'");
    }

    public function checkEdit($id)
    {

        return DB::select("select chrNowState from resource_machines where id in ('$id') and chrNowState='1'");
    }


}

?>