<?php
function getFile1($url, $save_dir = '', $filename = '', $type = 0) {
    if (trim($url) == '') {
        return false;
    }
    if (trim($save_dir) == '') {
        $save_dir = './';
    }
    if (0 !== strrpos($save_dir, '/')) {
        $save_dir.= '/';
    }
    //创建保存目录
    if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
        return false;
    }
    //获取远程文件所采用的方法
    if ($type) {
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $content = curl_exec($ch);
        curl_close($ch);
    } else {
        ob_start();
        readfile($url);
        $content = ob_get_contents();
        ob_end_clean();
    }
    //echo $content;
    $size = strlen($content);
    //文件大小
    $fp2 = @fopen($save_dir . $filename, 'a');
    fwrite($fp2, $content);
    fclose($fp2);
    unset($content, $url);
    return array(
        'file_name' => $filename,
        'save_path' => $save_dir . $filename,
        'file_size' => $size
    );
}
//$url = "http://www.baidu.com/img/baidu_jgylogo3.gif";
//$url="http://192.168.1.212/aaa.doc";
$url="http://10.1.42.52/".urlencode(iconv("GB2312","UTF-8","90.apk"));
$save_dir = "down";
//$filename = "baidu_jgylog1o31.gif";
$filename ="90.apk";
$res = getFile1($url, $save_dir, $filename,1);//0  1 都是好使的







//$root = 'E:\wamp\wamp\www\testworm\public\log\\';
//$logtime = date("Y_m_d_H_i_s", time());
//$run = "ping www.baidu.com";
//exec("cmd /c $run>$root" . "$logtime.log");
//$res = $logtime . '.log';
//if (file_exists("$root$logtime.log")) {
//    echo "success";
//} else {
//    echo "$root.$logtime.log";
//}
?>

<!--//App日志生成完毕后读取,写入数据库中方法-->
<!--$root = 'E:\wamp\wamp\www\testworm\public\log\\';-->
<!--$logtime = date("Y_m_d_H_i_s", time());-->
<!--$run = "ping www.baidu.com";-->
<!--$res = exec("cmd /c $run>$root" . "$logtime.log");-->
<!--//                            exec("cmd /c ping www.baidu.com > E:/20170323.log");-->
<!--//                            $file = fopen("$root" . "$logtime" . ".log", "r", "w");-->
<!--//                            $user = array();-->
<!--//                            $i = 0;-->
<!--////输出文本中所有的行，直到文件结束为止。-->
<!--//                            while (!feof($file)) {-->
<!--//                                $user[$i] = fgets($file);//fgets()函数从文件指针中读取一行-->
<!--//                                $i++;-->
<!--//                            }-->
<!--//                            fclose($file);-->
<!--//                            $user = array_filter($user);-->
<!--echo "<a href='/log/$logtime.log' >report</a><br/>";-->
<!--//                            foreach ($user as $key => $val) {-->
<!--//                                echo "$key:" . "$val" . "<br/>";-->
<!--//                            }-->



<!--                            if (file_exists("$root$intExecTaskID-$browserID.log")) {-->
<!--//                                $file = fopen("$root" . "$intExecTaskID-$browserID" . ".log", "r", "w");-->
<!--//                                $user = array();-->
<!--//                                $i = 0;-->
<!--//                                //输出文本中所有的行，直到文件结束为止。-->
<!--//                                while (!feof($file)) {-->
<!--//                                    $user[$i] = fgets($file);//fgets()函数从文件指针中读取一行-->
<!--//                                    $i++;-->
<!--//                                }-->
<!--//                                fclose($file);-->
<!--//                                $user = array_filter($user);-->
<!--//                                echo $user;-->
<!--//                                //foreach ($user as $log){-->
<!--//                                    //$query=DB::insert("INSERT into app_logs(intExecTaskID,browserID,browserName,log,time) VALUES('$intExecTaskID','$browserID','$browserName','$user','$logtime')");-->
<!--//                                //}-->
<!--//-->
<!--//                            }






















