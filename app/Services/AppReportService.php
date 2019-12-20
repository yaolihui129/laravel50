<?php
namespace App\Services;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
Class AppReportService{
    public function add($intExecTaskID,$browserID){
        $query=DB::select("select * from app_logs where intExecTaskID='$intExecTaskID' and browserID='$browserID'");
        return $query;
    }
}
?>