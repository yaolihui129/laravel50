<?php
namespace App\Services;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
Class zztservice{
    public function add123(){
        $query=DB::select("select log,time,(select chrTaskName from auto_tasks where id='751') as taskName,apisehemesid  from api_logs where apitaskid='751' and apitaskexecid='934'");
        return $query;
    }

    public function getscript()
    {
        $query=DB::select("
select aus.id as id from auto_scripts aus 
inner join  sys_flow_processes sf on sf.chrProcessName=aus.chrScriptName
inner join auto_scheme_relations asr on asr.intFlowID=sf.intFlowID
where asr.intSchemeID='577'
");
        return $query;
    }
}
?>