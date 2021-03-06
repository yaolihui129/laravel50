 @extends('master') @section('mastercontent')
<link href="{{url('css/classical/classical.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{url('css/classical/common.css')}}">
<link rel="stylesheet" href="{{url('css/classical/index.css')}}">

<header>
	<div class="width clearfix">
		<a href="/"><img
			src="{{url('../../images/icon/2/logo_03.png')}}" alt="" class="logo"></a>
		<ul class="right-top clearfix">
			<li class="user">
				<dl>
					<dt>
						<img src="{{url('../../images/icon/2/pic_03.png')}}" alt="">
					</dt>
					<dd>{{$username}}</dd>
					<span class="icon"></span>
				</dl>
			</li>
		</ul>
	</div>
</header>
<!--内容区域-->
<div class="width content clearfix">
	<!--侧边导航区域-->
	<div class="nav-list minHeight" id="leftNavbar"></div>
	<div id="page-wrapper" class="gray-bg dashbard-1">
		<div class="row content-tabs">
			<button class="roll-nav roll-left J_tabLeft">
				<i class="fa fa-backward"></i>
			</button>
			<nav class="page-tabs J_menuTabs">
				<div class="page-tabs-content">
					<a href="{{url('/desktop')}}" class="active J_menuTab" data-id="0">首页</a>
				</div>
			</nav>
			<button class="roll-nav roll-right J_tabRight">
				<i class="fa fa-forward"></i>
			</button>
			<button class="roll-nav roll-right dropdown J_tabClose">
				<span class="dropdown-toggle" data-toggle="dropdown">关闭操作<span
					class="caret"></span></span>
				<ul role="menu" class="dropdown-menu dropdown-menu-right">
					<!-- <li class="J_tabShowActive"><a>定位当前选项卡</a></li>
					<li class="divider"></li> -->
					<li class="J_tabCloseAll"><a>关闭全部选项卡</a></li>
					<li class="J_tabCloseOther"><a>关闭其他选项卡</a></li>
				</ul>
			</button>
			<a href="{{url('/auth/logout')}}"
				class="roll-nav roll-right J_tabExit"><i class="fa fa fa-sign-out"></i>
				退出</a>
		</div>
		<div class="row J_mainContent" id="content-main">
			<iframe class="J_iframe" id="iframe0" name="iframe0" width="100%"
				style='min-height: 1080px;' src="desktop/index" frameborder="0"
				scrolling="no" data-id="0" seamless></iframe>
		</div>
	</div>
</div>
<footer>
	<p>版权所有：用友公司 @2016</p>
</footer>
<!-- 全局js -->
<script src="{{url('javascript/jquery/jquery-2.1.1.min.js')}}"></script>
<!-- 自定义js -->
<script src="{{url('javascript/classical/classical.min.js')}}"></script>
<script src="{{url('javascript/plugins/contabs/contabs.min.js')}}"></script>
<!-- <script src="{{url('javascript/plugins/contabs/iframe.js')}}"></script> -->
<!-- 第三方插件  自动加载进度条-->
<!-- <script src="{{url('javascript/plugins/pace/pace.min.js')}}"></script> -->
@endsection
