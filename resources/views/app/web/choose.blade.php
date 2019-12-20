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

    <script type="text/javascript"
            src="{{url('/javascript/jquery/jquery-1.8.2.min.js')}}"></script>
    <script type="text/javascript"
            src="{{url('/javascript/plugins/layer/layer.js')}}"></script>
    <script type="text/javascript" src="{{url('/javascript/common.js')}}"></script>

</head>
<body>
<form>
    <form class="form-horizontal">
        <div class="form-group" style="padding-bottom: 30px">
            <label for="scriptid" class="col-sm-2 control-label">脚本ID</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" value="<?php echo $script?>" id="scriptid" placeholder="请输入脚本ID"
                       disabled>
            </div>
        </div>
        <div class="form-group" style="padding-bottom: 30px">
            <label for="name" class="col-sm-2 control-label">应用名称</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" value="<?php echo $scriptName?>" id="name" placeholder="请输入应用名称"
                       disabled>
            </div>
        </div>
        <div class="form-group" style="padding-bottom: 30px">
            <label for="remarks" class="col-sm-2 control-label">应用安装后包名</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="remarks" placeholder="请输入应用安装后包名">
            </div>
        </div>
        <div class="form-group" style="padding-bottom: 30px">
            <label for="package_name" class="col-sm-2 control-label">测试时间</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="package_name" placeholder="请输入测试分钟数">
            </div>
        </div>
        <div class="form-group" style="padding-bottom: 50px">
            <label for="log" class="col-sm-2 control-label">测试命令</label>
            <div class="col-sm-10">
                <textarea class="form-control" id="log" placeholder="测试命令展现" disabled></textarea>
            </div>
        </div>
        <div class="form-group" style="padding-bottom: 30px">
            <label for="version" class="col-sm-2 control-label">应用版本</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="version" placeholder="请输入应用版本">
            </div>
        </div>
        <div class="form-group" style="padding-bottom: 30px">
            <label for="type" class="col-sm-2 control-label">应用类型</label>
            <div class="col-sm-10">
                <select class="form-control" id="type">
                    <option>U8应用</option>
                    <option>互联网应用</option>
                </select>
            </div>
        </div>
        <div class="form-group" style="padding-bottom: 30px">
            <label for="classification" class="col-sm-2 control-label">应用分类</label>
            <div class="col-sm-10">
                <select class="form-control" id="classification">
                    <option>U8CRM</option>
                    <option>U8移动</option>
                    <option>友空间</option>
                    <option>U商城</option>
                    <option>U订货</option>
					<option>其它</option>

                </select>
            </div>
        </div>

        <div class="form-group" style="padding-bottom: 30px">
            <label for="description" class="col-sm-2 control-label">应用介绍</label>
            <div class="col-sm-10">
                <textarea class="form-control" id="description" placeholder="请输入应用介绍"></textarea>
            </div>

        </div>

        <button type="button" class="btn btn-info center-block" id="btn">提交</button>
    </form>

</form>
<script type="text/javascript">
    $(function () {
        //获取窗口索引,此方法和最后一行为layer获取dom元素必须
        var index = parent.layer.getFrameIndex(window.name);
        //让层自适应iframe
        //运行分析
        function check_input(){
            var inp = $('#package_name');
            inp.blur(function(){
                var inpVal = inp.val();
                if(!isNaN(inpVal)&&inpVal>0){
                    alert("请确认测试分钟数为:"+inpVal);
                }else{
                    alert('请输入正整数');
                    inp.attr("value","");
                }
            })
        };
        check_input();
        $("#btn").on("click", function () {
            var scriptid = $("#scriptid").val();
            var name = $("#name").val();
            var package_name1 = $("#package_name").val();
            var package_name2=Math.round(package_name1);
            var package_name = (package_name2 * 60 * 1000) / 500;
            var version = $("#version").val();
            var type = $("#type").val();
            var classification = $("#classification").val();
            var remarks = $("#remarks").val();
            var log = "adb shell monkey -p " + remarks + " -s 200 --throttle 500 --pct-syskeys 0 --kill-process-after-error -v -v -v " + package_name;
            var description = $("#description").val();
            $("#log").val(log);
            if (scriptid == "") {
                alert("应用名称不可为空");
            } else if (name == "") {
                alert("应用版本不可为空");
            } else if (version == "") {
                alert("应用版本不可为空");
            } else if (remarks == "") {
                alert("版本备注不可为空");
            } else if (remarks == "") {
                alert("版本备注不可为空");
            } else if (log == "") {
                alert("更新日志不可为空");
            } else if (description == "") {
                alert("应用描述不可为空");
            } else {
                $.ajax({
                    type: "get",
                    url: "/app/web/script/choose",
                    data: {
                        scriptid: scriptid,
                        name: name,
                        package_name: package_name,
                        version: version,
                        type: type,
                        classification: classification,
                        remarks: remarks,
                        log: log,
                        description: description,
                    },
                    success: function (json) {
                        var result = $.parseJSON(json);
                        if (result.success == 1) {
                            layer.open({
                                title: 'App信息完善',
                                content: 'App信息完善保存成功',
                            });
                        } else {
                            layer.open({
                                title: 'App信息完善',
                                content: 'App信息完善保存失败',
                            });
                        }
                    }
                })
            }
        })
        parent.layer.iframeAuto(index);
    });
</script>
</body>
</html>