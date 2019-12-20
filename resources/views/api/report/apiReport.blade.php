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
            width: 100%;
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
{{--<link href="{{url('/css/bootstrap/bootstrap.min.css')}}">--}}
{{--rel="stylesheet" type="text/css" />--}}
{{--<link rel="stylesheet" href="{{url('css/classical/common.css')}}">--}}
{{--<link rel="stylesheet" href="{{url('css/classical/index.css')}}">--}}

<!-- jquery-1.8.2.min.js -->
    <script type="text/javascript" src="{{url('/javascript/jquery/jquery-2.1.1.min.js')}}"></script>
    {{--<script src="{{url('javascript/plugins/bootstrap.min.js')}}"></script>--}}
    <script type="text/javascript" src="{{url('/javascript/common.js')}}"></script>
    <script src="{{url('assets/js/echarts.js')}}"></script>
    {{--<script--}}
    {{--src="{{url('javascript/plugins/echarts/echarts.common.min.js')}}"></script>--}}
</head>
<body >
<table class="gridtable" >
    <tr>
        <th colspan="12" >[{{$data['chrTaskName']}}]API 测试报告</th>
        {{--<th colspan="12">API 测试报告</th>--}}
    </tr>
    <tr>
        <td colspan="12" style="background-color:rgb(3, 24, 69);color: white ">1.运行平台</td>
    </tr>
    <tr>
        <th colspan="12">云测平台 【web API测试】 <a>http://upcat.yonyou.com/</a></th>
    </tr>

</table>
<table class="task">
    <tr>
        <td colspan="12" style="background-color:rgb(3, 24, 69);color: white ">2.任务报告图</td>
    </tr>
    <tr>
        <td colspan="12">

            {{--新增报表--}}
            <div class="clearfix tip">
                {{--<h4 class="clearfix">当前用例脚本总数：</h4>--}}
                {{--<span id="webTaskPieCount">0</span>--}}
            </div>
            <div id="webTaskPie" style="width: 100%; height: 300px;"></div>
            <ul class="clearfix" id="webState">
                <li class="mission-not" style="display:none;">
                    <span></span>未执行<b>0</b>
                </li>
                <li class="mission-ing" style="display:none;">
                    <span></span>执行中<b>0</b>
                </li>
                <li class="mission-success" style="display:none;">
                    <span></span>PASS<b>0</b></li>
                <li class="mission-fail" style="display:none;">
                    <span></span>Error<b>0</b>
                </li>
            </ul>
        </td>
    </tr>
</table>
@if(!empty($data['task_list']))
    <table class="task table table-hover">
        <tr>
            <td colspan="12" style="background-color:rgb(3, 24, 69);color: white ">3.案例详情</td>
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
                <td colspan="2" id="taskId" taskId="{{$task->id}}">{{$task->chrTaskName}}</td>
                <td colspan="2">{{$task->chrProjectName}}</td>
                <td colspan="2">{{$task->chrUserName}}</td>
                <td colspan="2" class="schemeState" >{{$task->state}}</td>
                <td colspan="2">{{$task->exectime}}</td>
                {{--            <td colspan="2">{{$task->updated_at}}</td>--}}

            </tr>
        @endforeach
    </table>
@endif

@if(!empty($data['scheme_list']))
    <table class="scheme">
        <tr>
            <td colspan="12" style="background-color:rgb(3, 24, 69);color: white ">4.案例详情</td>
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
            <td colspan="12" style="background-color:rgb(3, 24, 69);color: white ">4.脚本详情</td>
        </tr>
        <tr>
            <th colspan="2">案例名称</th>
            <th colspan="2">接口名称</th>
            <th colspan="2">执行状态</th>
        </tr>
        @foreach($data['script_info'] as $script)
            <tr class="success">
                <td colspan="2">{{$script->schemeName}}</td>
                <td colspan="2">{{$script->script}}</td>
                <td colspan="2" class="scriptState">{{$script->state}}</td>
            </tr>
        @endforeach
    </table>
@endif

<script>
    function loadTaskPies() {
        var taskId=$("#taskId").attr('taskId');
        var data={
            "taskId":taskId
        };
        CommonUtil.requestService("/api/web/scheme/schemePie", data, true, "get", function (response, status) {
            if (response.success) {
                var web = response.data.web;
                webTaskPie(web);
            }
        }, function (ex) {
        });
    };

    function webTaskPie(web) {
        web = CommonUtil.parseToJson(web);
        var lgData = [];
        var color = [];
        var webCount = 0;
        for (var idx in web) {
            $("#webState .mission-" + web[idx].state + "").empty().append("<span></span>" + web[idx].name + "<b>" + web[idx].value + "</b>");
            webCount += parseInt(web[idx].value);
            if(web[idx].name=='PASS'){
                lgData['PASS']=web[idx].value;
            }else if(web[idx].name=='Error'){
                lgData['Error']=web[idx].value;
            }else if(web[idx].name=='FAIL'){
                lgData['FAIL']=web[idx].value;
            }else if(web[idx].name=='TRUE'){
                lgData['TRUE']=web[idx].value;
            }

        }
        $("#webTaskPieCount").text(webCount);
        $("#webState li").each(function () {
            var value = $(this).find("b").text();
            if (value != "0") {
                //lgData.push($(this).find("b").text());
                color.push($(this).find("span").css("background-color"));
            }
        })
        // 基于准备好的dom，初始化echarts实例
        var myChart = echarts.init(document.getElementById('webTaskPie'));
        // 指定图表的配置项和数据


        var option = {
            backgroundColor: "#031845",
            color: ['#2edfa3', '#bce672', '#ff4777', '#70f3ff', '#4b5cc4', '#f47983', '#8d4bbb', '#6635EF', '#FFAFDA'],
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b}: {c} ({d}%)"
            },
            legend: {
                orient: 'horizontal',
                icon: 'circle',
                bottom: 20,
                x: 'center',
                textStyle: {
                    color: '#fff'
                },
                data: ['未执行', '执行中', 'Error', 'PASS']
            },
            series: [{
                name: '执行结果',
                type: 'pie',
                selectedMode: 'single',
                radius: [0, '38%'],

                label: {
                    normal: {
                        show: false,
                        position: 'inner',
                        formatter: '{d}%',
                        textStyle: {
                            color: '#fff',
                            fontWeight: 'normal',
                            fontSize: 20
                        }
                    }
                },
                labelLine: {
                    normal: {
                        show: false
                    }
                },
                data: [{
                    value: lgData['FAIL'],
                    name: '未执行'
                },
                    {
                        value: lgData['TRUE'],
                        name:  '执行中'
                    },
                    {
                        value: lgData['Error'],
                        name:  'Error'
                    },
                    {
                        value: lgData['PASS'],
                        name:  'PASS'
                    }
                ]
            },
                {
                    name: '执行结果',
                    type: 'pie',
                    radius: ['40%', '42%'],
                    label: {
                        normal: {
                            formatter: '{b}:{c}\n{d}%',
                            rich: {
                                b: {
                                    fontSize: 20,
                                    color: '#fff',
                                    align: 'left',
                                    padding: 4
                                },
                                hr: {
                                    borderColor: '#12EABE',
                                    width: '100%',
                                    borderWidth: 2,
                                    height: 0
                                },
                                d: {
                                    fontSize: 20,
                                    color: '#fff',
                                    align: 'left',
                                    padding: 4
                                },
                                c: {
                                    fontSize: 20,
                                    color: '#fff',
                                    align: 'center',
                                    padding: 4
                                }
                            }
                        }
                    },
                    labelLine: {
                        normal: {
                            show: true,
                            length: 20,
                            length2: 20,
                            lineStyle: {
                                type: 'dashed',
                                width: 2
                            }
                        }
                    },
                    data: [{
                        value: lgData['FAIL'],
                        name: '未执行'
                    },
                        {
                            value: lgData['TRUE'],
                            name: '执行中'
                        },
                        {
                            value: lgData['Error'],
                            name: 'Error'
                        },
                        {
                            value: lgData['PASS'],
                            name: 'PASS'
                        }

                    ]
                }
            ]
        };
        // var option =
        //     {
        //         title: {
        //             text: '接口测试报告图',
        //             // subtext: '纯属虚构',
        //             x: 'center'
        //         },
        //         tooltip: {
        //             trigger: 'item',
        //             formatter: "{a} <br/>{b} : {c} ({d}%)"
        //         },
        //         legend: {
        //             orient: 'vertical',
        //             left: 'left',
        //             data: ['执行失败', '执行中', '未执行', '执行成功']
        //         },
        //         series: [
        //             {
        //                 name: '接口完成数',
        //                 type: 'pie',
        //                 radius: '55%',
        //                 center: ['50%', '60%'],
        //                 data: [
        //                     {value: lgData[3], name: '执行失败'},
        //                     {value: lgData[1], name: '执行中'},
        //                     {value: lgData[0], name: '未执行'},
        //                     {value: lgData[2], name: '执行成功'}
        //                 ],
        //                 itemStyle: {
        //                     emphasis: {
        //                         shadowBlur: 10,
        //                         shadowOffsetX: 0,
        //                         shadowColor: 'rgba(0, 0, 0, 0.5)'
        //                     }
        //                 }
        //             }
        //         ]
        //     };
        // 使用刚指定的配置项和数据显示图表。
        myChart.setOption(option);
    }

    loadTaskPies();
    $(".schemeState").each(function () {
        var scriptState=$(this).text();
        if(scriptState=='PASS') {
            $(this).css("background-color","#70f3ff");
        }else if(scriptState=='Error'){
            $(this).css("background-color","#ff4777");
        }
    })
    $(".success .scriptState").each(function () {
        var scriptState=$(this).text();
        if(scriptState=='PASS') {
            $(this).css("background-color","#70f3ff");
        }else if(scriptState=='Error'){
            $(this).css("background-color","#ff4777");
        }
    })
</script>

</body>
</html>
