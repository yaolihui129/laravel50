<?php
namespace App\Services;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
Class WormAppService{
    public function install(){
        $query=DB::select("select chrFile from
app_information appi
join  auto_scripts autos ON autos.id=appi.scriptid
join  auto_script_relations  autosr ON autosr.intScriptID=autos.id
JOIN  sys_attachments sysa ON sysa.id=autosr.intAttID
join  auto_tasks att ON att.intProjectID=appi.scriptid
join auto_task_execs ate ON ate.intTaskID=att.id
join auto_jobs aj ON aj.intExecTaskID=ate.id
where aj.intExecTaskID='713'");
        return $query;
    }

    public  function apprun(){
        $query=DB::select("select appi.log from
app_information appi
join  auto_scripts autos ON autos.id=appi.scriptid
join  auto_script_relations  autosr ON autosr.intScriptID=autos.id
JOIN  sys_attachments sysa ON sysa.id=autosr.intAttID
join  auto_tasks att ON att.intProjectID=appi.scriptid
join auto_task_execs ate ON ate.intTaskID=att.id
join auto_jobs aj ON aj.intExecTaskID=ate.id
where aj.intExecTaskID='713'");
        return $query;
    }

    public function uninstall(){
        $query=DB::select("select appi.remarks from
//app_information appi
//join  auto_scripts autos ON autos.id=appi.scriptid
//join  auto_script_relations  autosr ON autosr.intScriptID=autos.id
//JOIN  sys_attachments sysa ON sysa.id=autosr.intAttID
//join  auto_tasks att ON att.intProjectID=appi.scriptid
//join auto_task_execs ate ON ate.intTaskID=att.id
//join auto_jobs aj ON aj.intExecTaskID=ate.id
//where aj.intExecTaskID='713'");
        return $query;
    }
}
?>