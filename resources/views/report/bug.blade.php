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
<body class="">
<form>
    <form class="form-horizontal">
        <div class="form-group">
            <label for="id" class="col-sm-2 control-label">报告ID</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="id" placeholder="请输入标题">
            </div>
        </div>
        <div class="form-group">
            <label for="id" class="col-sm-2 control-label">标题</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="title" placeholder="请输入标题">
            </div>
        </div>
        <div class="form-group">
            <label for="title" class="col-sm-2 control-label">简述</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="newtitle" placeholder="请输入简述">
            </div>
        </div>
        <div class="form-group">
            <label for="description" class="col-sm-2 control-label">详情</label>
            <div class="col-sm-10">
                <textarea class="form-control" id="description" placeholder="请输入具体内容"></textarea>
            </div>
        </div>
        <button type="button" class="btn btn-info center-block" id="btn" >提交</button>

    </form>

</form>
<script type="text/javascript">
    $(function () {
        //获取窗口索引,此方法和最后一行为layer获取dom元素必须
        var index = parent.layer.getFrameIndex(window.name);
        //让层自适应iframe
        //运行分析
        $("#btn").on("click", function () {
            var reportid=$("#id").val();
            var title = $("#title").val();
            var newtitle = $("#newtitle").val();
            var description = $("#description").val();
            if(reportid==""){
                alert("报告ID不可为空");
            }else if(title==""){
                alert("标题不可为空");
            }else if(newtitle==""){
                alert("简述不可为空");
            } else if(description==""){
                alert("详情不可为空");
            }else{
            $.ajax({
                type: "get",
                url: "/bugadd",
                data: {
                    title: title,
                    newtitle: newtitle,
                    description: description,
                    reportid:reportid
                },
                success: function (json) {
                    var result = $.parseJSON(json);
                    if (result.success == 1) {
                        layer.open({
                            title: 'BUG分析',
                            content: 'BUG分析保存成功',
                        });
                    } else {
                        layer.open({
                            title: '日志分析',
                            content: 'BUG分析保存失败',
                        });
                    }
                }
            })}
        })
        parent.layer.iframeAuto(index);
    });
</script>
</body>
</html>