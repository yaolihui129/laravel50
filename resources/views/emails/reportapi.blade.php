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
<p>{{$user}},您好：</p>
<p>
	您的任务已经执行完毕，执行结果如下：
</p>
<table class="gridtable">
	<tr>
		{{--<th colspan="12">[{{$data['chrTaskName']}}]自动化测试报告</th>--}}
		<th colspan="12">[]API 测试报告</th>

	</tr>
	<tr>
		<td colspan="12">1.运行平台</td>
	</tr>
	<tr>
		<th colspan="12">云测平台 【web API测试】  <a>http://upcat.yonyou.com/</a> </th>
	</tr>

</table>
{{--@if(!empty($task_info))--}}
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
		{{--@foreach($data['task_list'] as $task)--}}
			<tr>
				<td colspan="2"></td>
				<td colspan="2"></td>
				<td colspan="2"></td>
				<td colspan="2"></td>
				<td colspan="2">111111</td>

			</tr>
		{{--@endforeach--}}
	</table>
{{--@endif--}}

{{--@if(!empty($data['scheme_list']))--}}
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
		{{--@foreach($data['scheme_list'] as $scheme)--}}
			<tr>
				<td colspan="2"></td>
				<td colspan="2"></td>
				<td colspan="2"></td>
				<td colspan="2"></td>
				<td colspan="2"></td>
				<td colspan="2"></td>
			</tr>
		{{--@endforeach--}}
	</table>
{{--@endif--}}
<p>详细报告请参考附件</p>
</body>
</html>
