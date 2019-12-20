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
            src="{{url('/javascript/jquery/jquery-2.1.1.min.js')}}"></script>
    <script type="text/javascript"
            src="{{url('/javascript/bootstrap/bootstrap.min.js')}}"></script>
    <script type="text/javascript"
            src="{{url('/javascript/plugins/layer/layer.js')}}"></script>
    <script type="text/javascript" src="{{url('/javascript/common.js')}}"></script>
    <script type="text/javascript"
            src="{{url('/javascript/service/login.js')}}"></script>
</head>
<body>

<div class="taskExecID"  >
    <!-- Nav tabs -->
    <ul class="nav nav-tabs browserId" role="tablist" >
        <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab"
                                                  data-toggle="tab">报告样板</a></li>
        <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab"
                                   id="test">{{$taskExecID}}-{{$browserId}}-{{$browserName}}</a></li>

    </ul>
    <!-- Tab panes -->
    <div class="tab-content browserName" id={{$browserName}} >
        <div role="tabpanel" class="tab-pane active" id="home">
            <div class="right">
                <table border="" cellspacing="1" cellpadding="8">
                    <tbody style="height: 100%">
                    <tr style="height: 50px; ">
                        <td align="center" colspan="3">Monkey手机测试报告</td>
                    </tr>
                    <tr>
                        <th style="height: 40px;width: 150px ">测试手机</th>
                        <th style="height: 40px;width: 700px">Monkey报告</th>
                        <th style="height: 40px;width: 120px ">测试时间</th>
                    </tr>
                    <tr>
                        <td>
                            HUAWEI
                        </td>
                        <td>
                            请按手机型号点击左侧标签查看报告
                        </td>
                        <td>
                            2017-03-29 14:47
                        </td>
                    </tr>
                    <tr>
                        <td>
                            HUAWEI
                        </td>
                        <td>
                            请按手机型号点击左侧标签查看报告
                        </td>
                        <td>
                            2017-03-29 14:47
                        </td>
                    </tr>
                    <tr>
                        <td>
                            HUAWEI
                        </td>
                        <td>
                            请按手机型号点击左侧标签查看报告
                        </td>
                        <td>
                            2017-03-29 14:47
                        </td>
                    </tr>
                    <tr>
                        <td>
                            HUAWEI
                        </td>
                        <td>
                            请按手机型号点击左侧标签查看报告
                        </td>
                        <td>
                            2017-03-29 14:47
                        </td>
                    </tr>
                    <tr>
                        <td>
                            HUAWEI
                        </td>
                        <td>
                            请按手机型号点击左侧标签查看报告
                        </td>
                        <td>
                            2017-03-29 14:47
                        </td>
                    </tr>
                    <tr>
                        <td>
                            HUAWEI
                        </td>
                        <td>
                            请按手机型号点击左侧标签查看报告
                        </td>
                        <td>
                            2017-03-29 14:47
                        </td>
                    </tr>
                    <tr>
                        <td>
                            HUAWEI
                        </td>
                        <td>
                            请按手机型号点击左侧标签查看报告
                        </td>
                        <td>
                            2017-03-29 14:47
                        </td>
                    </tr>
                    <tr>
                        <td>
                            HUAWEI
                        </td>
                        <td>
                            请按手机型号点击左侧标签查看报告
                        </td>
                        <td>
                            2017-03-29 14:47
                        </td>
                    </tr>
                    <tr>
                        <td>
                            HUAWEI
                        </td>
                        <td>
                            请按手机型号点击左侧标签查看报告
                        </td>
                        <td>
                            2017-03-29 14:47
                        </td>
                    </tr>
                    <tr>
                        <td>
                            HUAWEI
                        </td>
                        <td>
                            请按手机型号点击左侧标签查看报告
                        </td>
                        <td>
                            2017-03-29 14:47
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>
            
        </div>
        <div role="tabpanel" class="tab-pane" id="profile">
            <div class="right">
                <table border="" cellspacing="1" cellpadding="8" id="pag">
                    <tbody id="mm" style="height: 100%">
                    <tr style="height: 50px; ">
                        <td align="center" colspan="3">Monkey手机测试报告</td>
                    </tr>
                    <tr>
                        <th style="height: 40px;width: 150px ">测试手机</th>
                        <th style="height: 40px;width: 700px">Monkey报告</th>
                        <th style="height: 40px;width: 120px ">测试时间</th>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>


    </div>

</div>

<script type="text/javascript">
    $(function () {
        //获取窗口索引,此方法和最后一行为layer获取dom元素必须
        var index = parent.layer.getFrameIndex(window.name);
        //让层自适应iframe
        $("#test").on("click", function () {
            var arr = $("#test").text();
            var arr1 = arr.split("-");
            var intExecTaskID = arr1[0];
            var browserID = arr1[1];
            $.ajax({
                type: "get",
                url: "/appreportlist",
                data: {
                    intExecTaskID: intExecTaskID,
                    browserID: browserID
                },
                success: function (json) {
                    var result = $.parseJSON(json);
                    if (result.success == 1) {
                        for (var i = 0; i < result.date.length; i++) {
                            var model =
                                '<tr  >' +
                                '<td id="id">' +
                                result.date[i]['browserName'] +
                                '</td>' +
                                '<td>' +
                                result.date[i]['log'] +
                                '</td>' +
                                '<td>' +
                                result.date[i]['time'] +
                                '</td>' +
                                '</tr>';
                            $(model).appendTo($("#mm"));
                        }
                    } else {
                        layer.open({
                            title: 'App运行报告',
                            content: '获取App运行报告失败',
                        });
                    }
                }
            })
        })

        parent.layer.iframeAuto(index);
    });
</script>
</body>
</html>