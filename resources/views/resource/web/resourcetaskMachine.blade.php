@extends('app') @section('content')
<script type="text/javascript"
	src="{{url('/javascript/service/sub/browsers.js')}}"></script>
<script src="/javascript/service/sub/report_detail.js"></script>
<script type="text/javascript"
	src="{{url('/javascript/service/resourcetaskMachine.js')}}"></script>
<form role="form" id="taskform" style="display: none; width: 800px;">
	<div class="row">
		<div class="col-sm-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content">
					<div class="form-group">
						<label class="col-sm-2 control-label" for="taskName">资源名称</label>
						<div class="col-sm-10">
							<input class="form-control" id="taskName" type="text"
								validate="required;len[1:50]" val-name="资源名称" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="MacIP">MAC地址</label>
						<div class="col-sm-10">
							<input class="form-control" id="MacIP" type="text"
								   validate="required;len[1:50]" val-name="MAC地址" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="belongTo">所属人</label>
						<div class="col-sm-10">
							<input class="form-control" id="belongTo" type="text"
								   validate="required;len[1:50]" val-name="所属人" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="hostIp">Esxi服务器IP地址</label>
						<div class="col-sm-10">
							<input class="form-control" id="hostIp" type="text"
								   validate="required;len[1:50]" val-name="Esxi服务器IP地址" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="vmName">虚拟机名字</label>
						<div class="col-sm-10">
							<input class="form-control" id="vmName" type="text"
								   validate="required;len[1:50]" val-name="虚拟机名字" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="userName">Esxi服务器用户名</label>
						<div class="col-sm-10">
							<input class="form-control" id="userName" type="text"
								   validate="required;len[1:50]" val-name="虚拟机登陆账号" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="password">Esxi服务器密码</label>
						<div class="col-sm-10">
							<input class="form-control" id="password" type="text"
								   validate="required;len[1:50]" val-name="虚拟机登陆密码" />
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
					<div class="form-group">
						<label class="col-sm-4 control-label" for="execBrowser">请选择运行的浏览器</label>
						<div class="col-sm-8" id="browserChecks"></div>
					</div>
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
<div class="case task">
	<form target="#">
		<ul class="clearfix">
			<li><label>资源名称</label><input id="search_taskName" type="text"></li>
			<li><label>资源所属人</label><input id="search_creater" type="text"></li>

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
	AutoTaskUtil.init();
});
</script>
@endsection

