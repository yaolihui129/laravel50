<?php
namespace App\Services;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
Class RunService{
    public function add($title,$newtitle,$description,$time,$reportid){
        $query=DB::insert("INSERT into run(title,newtitle,description,time,reportid) VALUES('$title','$newtitle','$description','$time','$reportid')");
        return $query;
    }
}
?>