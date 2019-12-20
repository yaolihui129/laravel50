@extends('app') @section('content')
	<script type="text/javascript"
			src="{{url('/javascript/service/sub/browsers.js')}}"></script>
	<script src="/javascript/service/sub/report_detail.js"></script>
	<script type="text/javascript"
			src="{{url('/javascript/service/resourcetaskPlan.js')}}"></script>
	<link rel="stylesheet" href="{{url('/css/bootstrap/bootstrap-select.css')}}"/>
	<script src="{{url('/javascript/bootstrap/bootstrap-select.js')}}" type="text/javascript"></script>
	<form role="form" id="taskform" style="display: none; width: 800px;">
		<div class="row">
			<div class="col-sm-12">
				<div class="ibox float-e-margins">
					<div class="ibox-content">
						<div class="form-group">
							<label class="col-sm-1 control-label" for="taskName">任务名称</label>
							<div class="col-sm-11">
								<input class="form-control" id="taskName" type="text"
									   validate="required;len[1:50]" val-name="任务名称" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-1 control-label" for="memo">资源选择</label>
							<div class="col-sm-8">
								<table id="schemelist"
									   class="table table-striped table-bordered table-hover dataTables-example"
									   cellspacing="0">
									<thead>
									</thead>
								</table>
							</div>
							<div class="col-sm-3">
								<div class="panel panel-success">
									<div class="panel-heading">已选择资源&nbsp&nbsp
										{{--<button id="clearMachine" type="button" class="btn btn-primary">清空</button>--}}
									</div>
									<div class="panel-body" id="checkedScheme"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>

	<form role="form" id="execform"
		  style="display: none; width: 800px; height: 200px;">
		<div class="row">
			<div class="col-sm-12">
				<div class="ibox float-e-margins">
					<div class="ibox-content">
						{{--<div class="form-group">--}}
							{{--<label class="col-sm-4 control-label" for="execBrowser">请选择运行的浏览器</label>--}}
							{{--<div class="col-sm-8" id="browserChecks"></div>--}}
						{{--</div>--}}
						<div class="form-group">
							<label class="col-sm-4 control-label" for="email">是否发送邮件给相关人员</label>
							<div class="col-sm-8">
								<label class="radio-inline i-checks"> <input type="radio"
																			 checked="" value="0" name="email"> <i></i> 不发送邮件
								</label> <label class="radio-inline i-checks"> <input
											type="radio" value="1" name="email"> <i></i> 发送邮件
								</label>
							</div>
						</div>
						<div class="form-group" id="emailReceiver">
							<label class="col-sm-4 control-label" for="emailReceivers">邮件接收人</label>
							<div class="col-sm-6">
								<input id="emailReceivers" type="text" placeholder="邮件之间通过;隔开"
									   class="form-control">
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
	<form role="form" id="stop" style="display: none; width: 800px; height: 200px;">
		<div class="row">
			<div class="col-sm-12">
				<div class="ibox float-e-margins">
					<div class="ibox-content">
						<div class="form-group">
							<h1 class="text-center" style="font-size: 30px" >是否确认停止当前任务</h1>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
	<div class="case task">
		<form target="#">
			<ul class="clearfix">
				{{--<li><label>机器资源</label><select id="search_project"--}}
											   {{--class="form-control">--}}
						{{--<option value=""></option> @foreach($projects as $project)--}}
							{{--<option value="{{$project->id}}">{{$project->chrMachName}}</option>--}}
						{{--@endforeach--}}
					{{--</select></li>--}}
				<li><label>任务状态</label><select id="search_state" class="form-control">
						<option value="-1"></option>@foreach($states as $state)
							<option value="{{$state['id']}}">{{$state['state']}}</option>
						@endforeach
					</select></li>
				<li><label>任务名称</label><input id="search_taskName" type="text"></li>
				{{--<li><label>MAC地址</label><input id="search_creater" type="text"></li>--}}
				{{--<select id="ly" class="selectpicker" data-style="btn-info">--}}
					{{--<option value="FAQ" selected="selected">FAQ</option>--}}
					{{--<option value="F" selected="selected">F</option>--}}
					{{--<option value="A" selected="selected">A</option>--}}
				{{--</select>--}}

				<button class="btnCommon" id="search">查询</button>
			</ul>
		</form>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content">
					<table id="tasklist"
						   class="table table-striped table-bordered table-hover dataTables-example"
						   cellspacing="0">
						<thead>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>


	<script type="text/javascript">
        $(function(){
            // $("#clearMachine").on('click',function () {
            //     $("#checkedScheme").empty();
            //     getSelectData=[];
            // });
            AutoTaskUtil.init();
        });
	</script>
@endsection

