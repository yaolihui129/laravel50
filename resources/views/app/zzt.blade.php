<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="_token" content="{{ csrf_token() }}" charset="utf-8"/>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>UpCAT- 登录</title>
    <link href="{{url('/css/bootstrap/bootstrap.min.css')}}"
          rel="stylesheet">
    <link href="{{url('/css/base.css')}}" rel="stylesheet">
    <link href="{{url('/css/login.css')}}" rel="stylesheet">
    <script type="text/javascript"
            src="{{url('/javascript/jquery/jquery-1.8.2.min.js')}}"></script>
    <script type="text/javascript"
            src="{{url('/javascript/plugins/layer/layer.js')}}"></script>
    <script type="text/javascript" src="{{url('/javascript/common.js')}}"></script>
    <script type="text/javascript"
            src="{{url('/javascript/service/login.js')}}"></script>
</head>
<body>

<div class="container">
    <table border="" cellspacing="1" cellpadding="8" id="pag">
        <tbody id="mm">
        <tr style="height: 20px; ">
            <td align="center" colspan="3">文章管理系统</td>
        </tr>
        <tr style="height: 20px;">
            <th style="width: 200px;">编号</th>
            <th style="width: 500px;">标题</th>
            <th style="width: 200px;">操作</th>
        </tr>
        @foreach ($posts as $post)
        <tr>
            <td id="id">
                {{ $post->kkk }}
            </td>
            <td>
                {{ $post->intProjectID }}
            </td>
            <td>
                {{ $post->chrTaskName }}
            </td>
        </tr>
        @endforeach


        </tbody>
    </table>

</div>
<nav aria-label="Page navigation">
    {!! $posts->render() !!}
</nav>


</body>
</html>
<?php dd($post);?>

{{--<!DOCTYPE html>--}}
{{--<html lang="en">--}}
{{--<head>--}}
{{--<meta name="_token" content="{{ csrf_token() }}" charset="utf-8"/>--}}
{{--<meta charset="utf-8">--}}
{{--<meta name="viewport"--}}
{{--content="width=device-width, initial-scale=1.0, maximum-scale=1.0">--}}
{{--<meta name="description" content="">--}}
{{--<meta name="author" content="">--}}

{{--<title>UpCAT- 登录</title>--}}
{{--<link href="{{url('/css/bootstrap/bootstrap.min.css')}}"--}}
{{--rel="stylesheet">--}}
{{--<link href="{{url('/css/base.css')}}" rel="stylesheet">--}}
{{--<link href="{{url('/css/login.css')}}" rel="stylesheet">--}}
{{--<script type="text/javascript"--}}
{{--src="{{url('/javascript/jquery/jquery-1.8.2.min.js')}}"></script>--}}
{{--<script type="text/javascript"--}}
{{--src="{{url('/javascript/jquery/jquery-2.1.1.min.js')}}"></script>--}}
{{--<script type="text/javascript"--}}
{{--src="{{url('/javascript/bootstrap/bootstrap.min.js')}}"></script>--}}
{{--<script type="text/javascript"--}}
{{--src="{{url('/javascript/plugins/layer/layer.js')}}"></script>--}}
{{--<script type="text/javascript" src="{{url('/javascript/common.js')}}"></script>--}}
{{--<script type="text/javascript"--}}
{{--src="{{url('/javascript/service/login.js')}}"></script>--}}
{{--</head>--}}
{{--<div class="right">--}}
{{--<button id="btn">start</button>--}}
{{--<table border="" cellspacing="1" cellpadding="8" id="pag">--}}
{{--<tbody id="mm" >--}}
{{--<tr style="height: 20px; ">--}}
{{--<td align="center" colspan="3">文章管理系统</td>--}}
{{--</tr>--}}
{{--<tr style="height: 20px;" >--}}
{{--<th style="width: 50px;">编号</th>--}}
{{--<th style="width: 500px;">标题</th>--}}
{{--<th style="width: 100px;">操作</th>--}}
{{--</tr>--}}

{{--</tbody>--}}
{{--</table>--}}
{{--</div>--}}

{{--<script>--}}
{{--$(function () {--}}

{{--$("#btn").on("click",function () {--}}
{{--$.ajax({--}}
{{--type: "get",--}}
{{--url: "test123",--}}
{{--success: function(json) {--}}
{{--var result = $.parseJSON(json);--}}
{{--for(var i = 0; i < result.date.length; i++) {--}}
{{--//alert(result.date[i]['id']);--}}
{{--//alert(result.date[i]['title']);--}}
{{--//alert(result.date[i]['description']);--}}
{{--var model =--}}
{{--'<tr  >' +--}}
{{--'<td id="id">' +--}}
{{--result.date[i]['id'] +--}}
{{--'</td>' +--}}
{{--'<td>' +--}}
{{--result.date[i]['taskName'] +--}}
{{--'</td>' +--}}
{{--'<td>' +--}}
{{--'<button id=' + result.date[i]['id'] + '>删除</button>' +--}}
{{--'<button id=' + result.date[i]['id'] + '123' + '>修改</button>' +--}}
{{--'</td>' +--}}
{{--'</tr>';--}}
{{--$(model).appendTo($("#mm"));--}}
{{--}--}}
{{--}--}}
{{--})--}}
{{--})--}}
{{--})--}}
{{--</script>--}}