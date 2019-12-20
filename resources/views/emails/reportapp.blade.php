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

	@if(!empty($task_info))
	<table class="task">
		<tr>
			<td colspan="12">1.应用基本信息</td>
		</tr>
		<tr>
			<th colspan="2">应用名称</th>
			<th colspan="2">应用包</th>
			<th colspan="2">应用版本</th>
			<th colspan="2">应用类别</th>
            <th colspan="2">提测时间</th>
		</tr>
		<tr>
			<td colspan="2">{{$task_info->name}}</td>
			<td colspan="2">{{$task_info->package_name}}</td>
			<td colspan="2">{{$task_info->version}}</td>
			<td colspan="2">{{$task_info->type}}</td>
			<td colspan="2">{{$task_info->time}}</td>
		</tr>
	</table>
	@endif
	<p>详细报告请参考附件</p>
</body>
</html>
