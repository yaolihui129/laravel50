@extends('master') @section('mastercontent')
<link href="{{url('/css/classical/classical.css')}}" rel="stylesheet"
	type="text/css" />
<link rel="stylesheet" href="{{url('css/classical/common.css')}}">
<link rel="stylesheet" href="{{url('css/classical/index.css')}}">
<input type="hidden" id="taskType" value="{{$taskType}}">
<input type="hidden" id="keyId" value="{{$keyId}}">
<div class="formbody form-horizontal">
	<div class="tabs-container popup-content">
		<div class="tab clearfix">
			<ul class="clearfix">
				<li class="cur"><a data-toggle="tab" href="#tab-func"
					aria-expanded="true">功能测试结果</a></li>
				<!-- <li><a href="javascript:void(0);">功能测试结果</a></li>
				<li><a href="javascript:void(0);">性能测试结果</a></li> -->
			</ul>
		</div>
		<div class="tab-content">
			<div id="tab-func" class="tab-pane active">
				<div class="panel-body">
					@if(!empty($step))
					<div class="interface">
						<h2>操作界面</h2>
						<div class="carousel slide" id="carousel">
							<div class="carousel-inner" id="carousel_content">
								<div class="item active" id="carousel_item" stepItem="1">
									<img alt="image" class="img-responsive mignifier"
										src="{{$step->chrImage}}">
								</div>
							</div>
							<a id="prev" class="left carousel-control"> <span
								class="icon-prev"></span></a> <a id="next"
								class="right carousel-control"> <span class="icon-next"></span>
							</a>
						</div>
						<h2>操作结果详细说明
							<a  class="btn btn-info" id="btn"  >查看脚本</a>
							<a type="button" class="btn btn-info" id="log" >日志详情</a>
							<a target="_blank" class="btn btn-info" id="bug" href="http://172.16.50.100/secure/Dashboard.jspa" >BUG汇报</a>
                            <a type="button" class="btn btn-info" id="bugfx" >BUG分析</a>
                            <a type="button" class="btn btn-info" id="run" >运行分析</a>
						</h2>
						<div id="stepDetail" stepItem="1">
							<div class="explain clearfix">
								<h6>执行命令</h6>
								<p>{{$step->chrDescription}}【{{$step->chrCmd}}】</p>
							</div>
							<div class="explain clearfix">
								<h6>耗时</h6>
								<p>{{$step->fltDuring}}

								</p>
							</div>
							<div class="explain clearfix">
								<h6>参数</h6>
								<p >
									{{$step->chrCmdParam}}
								</p>
							</div>
							<div class="explain clearfix">
								<h6>错误详情</h6>
								<p>{{$step->chrErrorMessage}}

								</p>
							</div>
						</div>
					</div>

					<div class="step">
						<ul class="clearfix tabs">
							<li class="cur" href="#step_all"><span></span>完整步骤</li>
							<li href="#step_fail"><span></span>失败步骤</li>
						</ul>
						<div class="detail" style="height: 480px; overflow: auto;">
							<div class="list" id="tab-step">
								<ul id="step_all">
									<li class="o focus" stepItem="1"><span></span>步骤{{$step->intOrderNo}}：{{$step->chrDescription}}</li>
								</ul>
								<ul id="step_fail" style="display: none;">
								</ul>
							</div>
						</div>
					</div>

					@else
					<div>尚未产生报告明细</div>
					@endif
				</div>
			</div>
		</div>
		<!-- <div id="tab-perf" class="tab-pane">
			<div class="panel-body">
				<div class="ibox float-e-margins">
					<div class="ibox-content"></div>
				</div>
			</div>
		</div>
		<div id="tab-time" class="tab-pane">
			<div class="panel-body">
				<div class="ibox float-e-margins">
					<div class="ibox-content"></div>
				</div>
			</div>
		</div>
		<div id="tab-log" class="tab-pane">
			<div class="panel-body">
				<div class="ibox float-e-margins">
					<div class="ibox-content"></div>
				</div>
			</div>
		</div> -->
	</div>
</div>
</div>
<script type="text/javascript"
	src="{{url('/javascript/service/autoreport.js')}}"></script>
<script type="text/javascript">
$(function(){
	AutoReportUtil.init();
    //获取窗口索引,此方法和最后一行为layer获取dom元素必须
    var index = parent.layer.getFrameIndex(window.name);
    //让层自适应iframe

	//查看脚本
    $('#btn').on('click', function(){
		var mm=$("#stepDetail > div:nth-child(1) > p").text();
		var mn=mm.split("/");
		var id=mn[1];
        layer.open({
            type : 2,
            title : "脚本",
            skin : '', // 加上边框
            offset : [ '50px' ],
            area : [ "850px", "600px" ], // 宽高
            btn : [ '保存', '取消' ],
            yes:function(index,layero){
                var iframeWin = window[layero.find('iframe')[0]['name']];
                var code_editor = iframeWin.code_editor;
                var new_code = code_editor.getValue();
                if (iframeWin.old_code != new_code) {
                    var requestData = {
                        "code" : new_code,
						"id":id
                    }};
                $.ajax({
                    type:"get",
                    url:"/update",
					data:{
                        "code" : new_code,
                        "id":id
                    },
                    success:function(json){
                        var json1=json.replace("{","");
                        var json2=json1.replace("}","");
                        var json3=json2.split(":");
                        if (json3[1]==1) {
                            layer.open({
                                title: '编辑脚本',
                                content: '编辑保存成功',
                            });
                        } else{
                            layer.open({
                                title: '编辑脚本',
                                content: '编辑保存失败',
                            });
                        }
                    }
                });
            },
            content:"/auto/web/script/"+id+"/edit?readonly=0",
        });

    });

    //日志详情
    $("#log").on("click",function () {
        var kk=$("#stepDetail > div:nth-child(1) > p").text();
        var kl=kk.split("/");
        var intScriptID=kl[1];
        var intSchemeID=kl[2];
        var intExecTaskID=kl[3];
        $.ajax({
            type:"get",
            url:"/report/bill/logs",
            data:{
                "intScriptID":intScriptID,
                "intSchemeID":intSchemeID,
				"intExecTaskID" : intExecTaskID,
            },
            success:function(json){
                var result=$.parseJSON(json);

                    layer.open({
                        title: '日志详情',
                        content: '<table border="" cellspacing="1" cellpadding="8"><tbody id="mm" ><tr style="height: 20px;"><th style="width: 200px;">名称</th><th style="width: 100px;">行号</th><th style="width: 200px;">操作</th><th style="width: 100px;">断言</th><th style="width: 100px;">结果</th><th style="width: 100px;">耗时</th></tr><tr></tr></tbody></table>',
                        area:["850px","600px"],
                        btn:['确认','取消'],
                        title:"日志详情",
                        skin:"",
                        success:function () {
                            for(var i = 0; i < result.date.length; i++) {
                            var model =
                            '<tr>'+
                            '<td>'+result.date[i]["chrScriptName"]+'</td>'+
                            '<td>'+result.date[i]["intLineNo"]+'</td>'+
                            '<td>'+result.date[i]["chrDescription"]+'</td>'+
                            '<td>'+result.date[i]["chrErrorMessage"]+'</td>'+
                            '<td>'+result.date[i]["chrResult"]+'</td>'+
                            '<td>'+result.date[i]["fltDuring"]+'</td>'+
                            '</tr>';
                            $(model).appendTo($("#mm"));
                            }
                        }
                    });


            }
        });

    });

    //BUG汇报
//    $("#bug").on("click",function () {
//        window.location.href="http://172.16.50.100/secure/Dashboard.jspa";
//    });

    //运行分析
    $("#run").on("click",function () {
        layer.open({
            type : 2,
            title : "运行日志分析",
            skin : '', // 加上边框
            offset : [ '50px' ],
            area : [ "750px", "800px" ], // 宽高
//            btn : [ '保存', '取消' ],
//            yes:function(){
//                var id=$("#id").text();
//				var title=$("#title").text();
//				var description=$("#description").text();
//                $.ajax({
//                    type:"get",
//                    url:"/runadd",
//                    data:{
//                        id:id,
//                        title:title,
//                        description:description
//                    },
//                    success:function(json){
//                        var result=$.parseJSON(json);
//                        if (result.success==1) {
//                            layer.open({
//                                title: '日志分析',
//                                content: '日志分析保存成功',
//                            });
//                        } else{
//                            layer.open({
//                                title: '日志分析',
//                                content: '日志分析保存失败',
//                            });
//                        }
//                    }
//                });
//            },
			content:"/run",
        });
    });
    //bug分析
    $("#bugfx").on("click",function () {
        layer.open({
            type : 2,
            title : "BUG日志分析",
            skin : '', // 加上边框
            offset : [ '50px' ],
            area : [ "750px", "800px" ], // 宽高
            content:"/bug",
        });
    })
    parent.layer.iframeAuto(index);
});
</script>
@endsection
