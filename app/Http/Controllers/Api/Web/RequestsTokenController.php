<?php
/**
 * Created by PhpStorm.
 * User: zhangfj
 * Date: 2017/10/18
 * Time: 12:57
 */

namespace App\Http\Controllers\Api\Web;


use Illuminate\Support\Facades\Log;
use Requestnew;


class RequestsTokenController
{
    //释放token方法
    public function releaseToken()
    {

        $url = 'http://172.20.18.131:8072/gettoken.php?logout=1&sessionID=aucdeund9fh5i20iuh6nnoinq6';
        $params = [
            'logout' => 1,
            'sessionID' => 'aucdeund9fh5i20iuh6nnoinq6'
        ];

        try {
            $response = Re::request($url, [], [], 'GET', []);
            if ($response->status_code !== 200) {
                throw new \Exception("Error Processing Request", 1);
            }

            // $response = json_decode($response->body);

            return $response->body;
        } catch (\Exception $e) {
            $errorMsg = '调用外部API失败，api地址：'.$url.'参数：'.json_encode($params, JSON_UNESCAPED_UNICODE).'err message:'.$e->getMessage();
            Log::info($errorMsg);
            return false;
        }
    }
    //调用方法
    public function callReleaseToken()
    {
        $result = $this->releaseToken();
        if ($result == 'logout sucess') {
            return "Release Token Sucess";
        } else {
            return "Release Token Error";
        }
    }

}