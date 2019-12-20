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
        @foreach($userlist as $val)
        <tr>
            <td id="id">
                {{$val->id}}
            </td>
            <td>
                {{$val->state}}
            </td>
            <td>
                {{$val->taskName}}
            </td>
        </tr>
            @endforeach



        </tbody>
    </table>
</div>
<nav aria-label="Page navigation">
    {!! $paginator->render() !!}
</nav>


</body>
</html>
