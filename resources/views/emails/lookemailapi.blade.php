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
            <th colspan="12">[{{$data['chrTaskName']}}]API 测试报告</th>
            {{--<th colspan="12">API 测试报告</th>--}}


        </tr>
        <tr>
            <td colspan="12">1.运行平台</td>
        </tr>
        <tr>
            <th colspan="12">云测平台 【web API测试】  <a>http://upcat.yonyou.com/</a> </th>
        </tr>

    </table>
    @if(!empty($data['task_list']))
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
    @foreach($data['task_list'] as $task)
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

    @if(!empty($data['scheme_list']))
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
    @foreach($data['scheme_list'] as $scheme)
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
	
	
	
	    @if(!empty($data['script_info']))
        <table class="scheme">
            <tr>
                <td colspan="12">4.脚本详情</td>
            </tr>
            <tr>
                <th colspan="2">案例名称</th>
                <th colspan="2">接口名称</th>
                <th colspan="2">执行状态</th>
            </tr>
            @foreach($data['script_info'] as $script)
                <tr>
                    <td colspan="2">{{$script->schemeName}}</td>
                    <td colspan="2">{{$script->script}}</td>
                    <td colspan="2">{{$script->state}}</td>
                </tr>
            @endforeach
        </table>
    @endif

    {{--//新的API结果--}}
    {{--@if(!empty($data['api_list']))--}}
        {{--<table class="api">--}}
            {{--<tr>--}}
                {{--<td colspan="12">4.统计结果</td>--}}
            {{--</tr>--}}
            {{--<tr>--}}
                {{--<th colspan="2">任务名称</th>--}}
                {{--<th colspan="2">案例名称</th>--}}
                {{--<th colspan="2">所属项目</th>--}}
                {{--<th colspan="2">创建人</th>--}}
                {{--<th colspan="2">执行状态</th>--}}
                {{--<th colspan="2">结果统计</th>--}}
            {{--</tr>--}}
            {{--@foreach($data['api_list'] as $api)--}}
                {{--<tr>--}}
                    {{--<td colspan="2">{{$api->chrTaskName}}</td>--}}
                    {{--<td colspan="2">{{$api->schemeName}}</td>--}}
                    {{--<td colspan="2">{{$api->projectName}}</td>--}}
                    {{--<td colspan="2">{{$api->createUser}}</td>--}}
                    {{--<td colspan="2">{{$api->state}}</td>--}}
                    {{--<td colspan="2">{{$api->log}}</td>--}}
                {{--</tr>--}}
            {{--@endforeach--}}
        {{--</table>--}}
    {{--@endif--}}

    {{--@if(!empty($data['project_sum']))--}}
    {{--<table class="project">--}}
        {{--<tr>--}}
            {{--<td colspan="12">4.功能模块覆盖详情</td>--}}
        {{--</tr>--}}
        {{--<tr>--}}
            {{--<th colspan="2">模块</th>--}}
            {{--<th colspan="2">总脚本数</th>--}}
            {{--<th colspan="2">本次执行数</th>--}}
            {{--<th colspan="2">总执行百分比</th>--}}
            {{--<th colspan="2">本次执行通过数</th>--}}

            {{--<th colspan="2">本次执行通过百分比</th>--}}
        {{--</tr>--}}
    {{--@foreach($data['project_sum'] as $project)--}}
        {{--<tr>--}}
            {{--<td colspan="2">{{$project->ppchrProjectName}}</td>--}}
            {{--<td colspan="2">{{$project->allscripts}}</td>--}}
            {{--<td colspan="2">{{$project->execs}}</td>--}}
            {{--<td colspan="2">{{number_format($project->execlv, 2, '.', '')}}%</td>--}}
            {{--<td colspan="2">{{$project->execPass}}</td>--}}

            {{--<td colspan="2">{{number_format($project->passlv, 2, '.', '')}}%</td>--}}
        {{--</tr>--}}
    {{--@endforeach--}}
    {{--</table>--}}
    {{--@endif--}}

    {{--@if(!empty($data['scheme_list']))--}}
    {{--<table class="script_sum">--}}
        {{--<tr>--}}
            {{--<td colspan="12">5.脚本日志统计分析</td>--}}
        {{--</tr>--}}
        {{--<tr>--}}
            {{--<th colspan="4">事件</th>--}}
            {{--<th colspan="4">次数</th>--}}
            {{--<th colspan="4">成功率</th>--}}
        {{--</tr>--}}
    {{--@foreach($data['script_sum'] as $val)--}}
        {{--<tr>--}}
            {{--<td colspan="4">{{$val->chrDescription}}</td>--}}
            {{--<td colspan="4">{{$val->count}}</td>--}}
            {{--<td colspan="4">{{number_format($val->passlv, 2, '.', '')}} %</td>--}}
        {{--</tr>--}}
    {{--@endforeach--}}
    {{--</table>--}}
    {{--@endif--}}
</body>
</html>
