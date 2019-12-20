<?php
namespace App\Services;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
Class EmailService{
    public function look($taskexecid){
        $query= DB::select("select intTaskID from auto_task_execs where id='$taskexecid'");
        return $query;
    }
}
?>