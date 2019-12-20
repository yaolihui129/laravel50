<!DOCTYPE html >
<html>
<head>
<title>UpCAT</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></meta>
<style type="text/css">
table {
    font-family: verdana, arial, sans-serif;
    font-size: 14px;
    color: #333333;
    border-width: 1px;
    border-color: #666666;
    border-collapse: collapse;
    valign: middle;
    width:100%;
}

th {
    border-width: 1px;
    padding: 8px;
    border-style: solid;
    border-color: #666666;
    background-color: #dedede;
}

td {
    border-width: 1px;
    padding: 8px;
    border-style: solid;
    border-color: #666666;
    background-color: #ffffff;
}
</style>
</head>
<body>
    <table class="gridtable">
        <tr>
            {{--<th colspan="12">[{{$chrTaskName}}]自动化测试报告</th>--}}
            <th colspan="12">[{{$chrTaskName}}]API 测试报告</th>

        </tr>
        <tr>
            <td colspan="12">1.运行平台</td>
        </tr>
        <tr>
            <th colspan="12">云测平台 【web API测试】  <a>http://upcat.yonyou.com/</a> </th>
        </tr>

    </table>
    @if(!empty($task_list))
    <table class="task">
        <tr>
            <td colspan="12">2.任务详情</td>
        </tr>
        <tr>
            <th colspan="2">任务名称</th>
            <th colspan="2">所属项目</th>
            <th colspan="2">创建人</th>
            <th colspan="2">任务状态</th>
            <th colspan="2">执行时间</th>
        </tr>
    @foreach($task_list as $task)
        <tr>
            <td colspan="2">{{$task->chrTaskName}}</td>
            <td colspan="2">{{$task->chrProjectName}}</td>
            <td colspan="2">{{$task->chrUserName}}</td>
            <td colspan="2">{{$task->state}}</td>
            <td colspan="2">{{$task->exectime}}</td>
{{--            <td colspan="2">{{$task->updated_at}}</td>--}}

        </tr>
    @endforeach
    </table>
    @endif

    @if(!empty($scheme_list))
    <table class="scheme">
        <tr>
            <td colspan="12">3.案例详情</td>
        </tr>
        <tr>
            <th colspan="2">任务名称</th>
            <th colspan="2">案例名称</th>
            <th colspan="2">所属项目</th>
            <th colspan="2">创建人</th>
            <th colspan="2">执行状态</th>
            <th colspan="2">结果统计</th>
        </tr>
    @foreach($scheme_list as $scheme)
        <tr>
            <td colspan="2">{{$scheme->chrTaskName}}</td>
            <td colspan="2">{{$scheme->schemeName}}</td>
            <td colspan="2">{{$scheme->projectName}}</td>
            <td colspan="2">{{$scheme->createUser}}</td>
            <td colspan="2">{{$scheme->state}}</td>
            <td colspan="2">{{$scheme->apilog}}</td>
        </tr>
    @endforeach
    </table>
    @endif
	
	    @if(!empty($script_info))
    <table class="scheme">
        <tr>
            <td colspan="12">4.脚本详情</td>
        </tr>
        <tr>
                <th colspan="2">案例名称</th>
                <th colspan="2">接口名称</th>
                <th colspan="2">执行状态</th>
        </tr>
    @foreach($script_info as $script)
        <tr>
                    <td colspan="2">{{$script->schemeName}}</td>
                    <td colspan="2">{{$script->script}}</td>
                    <td colspan="2">{{$script->state}}</td>
        </tr>
    @endforeach
    </table>
    @endif
</body>
</html>
