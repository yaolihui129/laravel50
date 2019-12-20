<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\SysMachine;
use Illuminate\Support\Facades\Log;

class ExamService {
    public function login()
    {

    }

    public function getAllUser()
    {
        $res=DB::select("select id,userName from exam_user where ifAdmin in('1')");
        return $res;
    }

    public function checkLogin($user,$pwd,$choose)
    {
        $msg='';
        $ifUser=DB::select("select * from exam_user where userName='$user'");
        if(!empty($ifUser)){
            $ifPwd=DB::select("select * from exam_user where userName='$user' and passwprd='$pwd'");
            if(!empty($ifPwd)){
                $msg=0;
            }else{
                $msg=2;
            }
        }else{
            $msg=1;
        }

        return $msg;
    }

    public function getQuestion($fieldCode,$productCode,$choose)
    {
        $sql="select * from exam_question where field='FAG' and productcode='GL' and examtype='1'  LIMIT 0,20";
        $res=DB::select($sql);
        return $res;

    }



}

?>