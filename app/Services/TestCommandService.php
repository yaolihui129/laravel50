<?php
namespace App\Services;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

Class TestCommandService
{
    public function getTimeTaskJob()
    {
        $titask=DB::select('select 
atrt.intTaskID as taskid,
attr.intTiTaskID as tiTaskIds,
attr.intState as state,
attr.chrExecBrowserIds as selBrowsers,
attr.chrEmails as emails,
attr.intExecRateID as execRate,
attr.chrExecTime as execTime,
attr.dtExecBeginDate as execBeginDate,
attr.dtExecEndDate as execEndDate,
att.intCreaterID as userid,
attr.intCompanyID as companyID,
attr.chrEmails as emails,
att.chrTiTaskName as titaskName,
att.intTiTaskTypeID as titaskType,
att.intProjectID as projectId,
atrt.intTaskID as oldTaskIds
from auto_timer_task_relations as attr
INNER JOIN auto_timer_tasks as att on attr.intTiTaskID=att.id
INNER JOIN auto_timer_relate_tasks as atrt on attr.intTiTaskID=atrt.intTiTaskID
 where dtExecEndDate > dtExecBeginDate+1 and attr.intState !=1 ');
        return $titask;

    }


    public function getReportIdByTaskId($taskID)
    {
        return DB::select("select id from auto_reports where intTaskID='$taskID'");
    }

    public function getintExecCount($taskID)
    {
        return DB::select("select intExecCount from auto_timer_task_relations where id='$taskID'");
    }


    public function update($execTime,$taskID,$count)
    {
        DB::beginTransaction();
        try {
            Log::info('开始update');
            DB::update("UPDATE auto_reports SET created_at='$execTime' where intTaskID='$taskID'");

            DB::update("UPDATE auto_timer_task_relations SET intExecCount='$count' where id='$taskID'");

            DB::commit ();
            Log::info('结束update');
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }

    }
}