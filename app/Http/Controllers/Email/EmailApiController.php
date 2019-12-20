<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/31
 * Time: 16:04
 */

namespace App\Http\Controllers\Email;


use App\Http\Controllers\Controller;
use App\Services\LookEmailApiService;

class EmailApiController extends  Controller
{
    public function index($taskexecid)
    {
        $query = new LookEmailApiService();
        $params = $query ->lookapi($taskexecid);

        $data['chrTaskName'] = $params['task_name']->chrTaskName;
//            $data['task_list'] = array_get($params,'task_list',[]);
        $data['task_list'] = array_get($params, 'task_list', []);
        $data['scheme_list'] = array_get($params, 'scheme_list', []);
        $data['script_sum'] = array_get($params, 'script_sum', []);
        $data['project_sum'] = array_get($params, 'project_sum', []);
        $data['exec_time'] = array_get($params, 'exec_time');
		$data['script_info'] = array_get($params, 'script_info');

        return  view("emails.lookemailapi")-> with("data",$data);
		//return view("api.report.apiReportNew")->with("data",$data);
    }
}

