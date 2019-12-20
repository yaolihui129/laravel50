<?php

/*
 * |-------------------------------------------------------------------------- | Application Routes |-------------------------------------------------------------------------- | | Here is where you can register all of the routes for an application. | It's a breeze. Simply tell Laravel the URIs it should respond to | and give it the controller to call when that URI is requested. |
 */
Route::controllers ( [ 
		'auth' => 'Auth\AuthController', // 认证登录
		'password' => 'Auth\PasswordController'  // 重置密码
] );
Route::get ( "/", "Auth\AuthController@getIndex" ); // Index
Route::group(['prefix'=>'camp'],function(){
    Route::get('/webtest',"Campaign\WebController@index");
    Route::get('/apptest',"Campaign\AppController@index");
    Route::get('/u8',"Campaign\U8Controller@index");
});
Route::get ( '/verify/image', 'Verify\VerifyController@index' ); // 图片验证码
                                                                 
// 管理中心
Route::group ( [ 
		'middleware' => 'auth' 
], function () {
	// 管理中心框架布局
	Route::group ( [ 
			'prefix' => 'desktop' 
	], function () {
		// 个人桌面
		Route::get ( "", "DesktopController@index" ); // 个人桌面
        Route::get ( "/app_scheme", "DesktopController@index" ); // 稳定性测试app_scheme个人桌面
        Route::get ( "/app_job", "DesktopController@index" ); // 稳定性测试app_job个人桌面
        Route::get ( "/app_report", "DesktopController@index" ); // 稳定性测试app_report个人桌面
        Route::get ( "/auto_scheme", "DesktopController@index" ); // 自动化测试auto_scheme个人桌面
        Route::get ( "/auto_job", "DesktopController@index" ); // 自动化测试auto_job个人桌面
        Route::get ( "/auto_report", "DesktopController@index" ); // 自动化测试auto_report个人桌面
		Route::get ( "/deskmenus", "DesktopController@getDesktopMenus" ); // 获取桌面菜单（包括上导航，左导航，桌面菜单）信息
		Route::get ( "/menus", "DesktopController@getMenus" );
		// 登录
		Route::get ( "/index", "IndexController@index" );
		Route::group ( [ 
				'prefix' => 'index'
		], function () {
			Route::get ( "/taskpie", "IndexController@getTaskPies" ); // web
            Route::get ( "/apiTaskPie", "IndexController@getApiTaskPies" ); // Api
            Route::get ( "/appTaskPie", "IndexController@getAppTaskPies" ); // App
            Route::get ( "/dispatchTaskPie", "IndexController@getDispatchTaskPies" ); // Dispatch
			Route::get ( "/taskline", "IndexController@getTaskLines" ); // 报告绘图统计
			Route::get ( "/scheme", "IndexController@getSchemeChart" ); // 报告绘图统计
			Route::get ( "/script", "IndexController@getScriptChart" ); // 报告绘图统计
		} );
	} );
	// 系统设置
	Route::group ( [ 
			'prefix' => 'sys',
			'namespace' => 'SystemSettings' 
	], function () {
		Route::post ( "osetting", "UserController@updateUserOSDisplay" ); // 系统设置
		Route::get ( "menu/list", "MenuController@getList" ); // 项目管理
		Route::resource ( "menu", "MenuController" ); // 项目管理
		
		Route::get ( "org/tree", "OrganizeController@getOrgTree" ); // 组织机构树
		Route::resource ( "org", "OrganizeController" ); // 组织机构
		
		Route::get ( "role/list", "RoleController@getList" ); // 角色管理
		Route::get ( "role/tree", "RoleController@getRoleTree" ); // 角色管理
		Route::resource ( "role", "RoleController" ); // 角色管理
		
		Route::get ( "user/list", "UserController@getList" ); // 员工管理
		Route::resource ( "user", "UserController" ); // 员工管理
		
		Route::get ( "allotright/list", "AllotPermissionController@getList" ); // 分配权限
		Route::resource ( "allotright", "AllotPermissionController" ); // 分配权限
		
		Route::get ( "right/tree", "PermissionController@getRightTree" ); // 功能权限树
		Route::get ( "right/dtree", "PermissionController@getDataRightTree" ); // 数据权限树
		Route::resource ( "right", "PermissionController" ); // 权限管理
	} );
	
	Route::group ( [ 
			'prefix' => 'mytask',
			'namespace' => 'MyTask' 
	], function () {
		Route::resource ( "timer/list", "TimerTaskController@getTimerTaskList" ); // 定时任务管理
		Route::resource ( "timer", "TimerTaskController" ); // 定时任务管理
	} );
	Route::group ( [ 
			'prefix' => 'pc',
			'namespace' => 'ServerMachine' 
	], function () {
		Route::get ( "machine/list", "MachineController@getMachineList" ); // 机器列表
		Route::resource ( "machine", "MachineController" ); // 机器管理
	} );
	Route::group ( [ 
			'prefix' => 'report',
			'namespace' => 'Report' 
	], function () {
		Route::get ( "bill/list", "ReportController@getReportList" ); // 报告列表
        Route::get("bill/logs","ReportController@getReportLogs");//log日志
		Route::resource ( "bill", "ReportController" ); // 报告
	} );
	// 自动化
	Route::group ( [ 
			'prefix' => 'auto',
			'namespace' => 'Automation' 
	], function () {
		Route::resource ( 'browser', 'AutoBrowserController' );
		Route::resource ( "report", "AutoExecReportController" ); // 执行任务报告
		Route::group ( [ 
				'prefix' => 'web',
				'namespace' => 'Web' 
		], function () {
			// 脚本仓库
			Route::group ( [ 
					'prefix' => 'script' 
			], function () {
				Route::any ( 'uploader', 'AutoScriptController@uploader' );
				Route::get ( "tree", "AutoScriptController@getScriptTree" ); // 产品自动化脚本树结构
				Route::get ( "list", "AutoScriptController@getScriptList" ); // 产品自动化脚本列表
				Route::get ( "annalslist", "AutoScriptController@getAnnalsScriptList" ); // 历史脚本列表
				Route::get ( "backloglist", "AutoScriptController@getBackLogList" ); // 回滚日志列表
				Route::get ( "scriptdiff", "AutoScriptController@getScriptText" ); // 脚本内容
				Route::post ( "rollback", "AutoScriptController@rollBack" ); // 脚本内容
				Route::post ( "params", "AutoScriptController@storeParams" ); // 存储脚本的参数化文件
				Route::post ( "images", "AutoScriptController@storeImages" ); // 存储脚本所需图片
                Route::any ( "jiaoben", "AutoScriptController@update2" ); // 存储脚本所需图片
                Route::any ( "uploadParamNew", "AutoScriptController@uploadParamNew" ); // 上传配置文件
			} );
			Route::resource ( 'script', 'AutoScriptController' );
			// 脚本案例
			Route::group ( [ 
					'prefix' => 'scheme' 
			], function () {
				Route::get ( "list", "AutoSchemeController@getSchemeList" ); // 产品自动化案例列表
				Route::get ( "flowprocess/{id}", "AutoSchemeController@getFlowProcess" ); // 流程设计
				Route::get ( "addflow", "AutoSchemeController@addFlow" ); // 添加流程
				Route::post ( "addprocess", "AutoSchemeController@addProcess" ); // 添加流程步骤
				Route::post ( "delprocess", "AutoSchemeController@delProcess" ); // 删除流程步骤
				Route::post ( "saveflowprocess/{id}", "AutoSchemeController@saveFlowProcess" ); // 保存流程步骤面板的所有信息
				Route::get ( "download/{id}", "AutoSchemeController@downScheme" ); // 保存流程步骤面板的所有信息
				Route::get ( "exec/{id}", "AutoSchemeController@exec" ); // 案例执行
			} );
			Route::resource ( 'scheme', 'AutoSchemeController' );
			
			// 案例任务
			Route::group ( [ 
					'prefix' => 'task' 
			], function () {
				Route::get ( "list", "AutoTaskController@getTaskList" ); // 产品自动化案例列表
				Route::resource ( "exec", "AutoTaskExecController" ); // 执行任务
			} );
			Route::resource ( 'task', 'AutoTaskController' );
		} );
	} );
	// 项目管理
	Route::group ( [ 
			'prefix' => 'project',
			'namespace' => 'Project' 
	], function () {
		Route::get ( "bill/list", "ProjectController@getList" ); // 项目管理
		Route::resource ( "bill", "ProjectController" ); // 项目管理
		Route::get ( "product/tree", "ProductController@getProductTree" ); // 产品结构
		Route::resource ( "product", "ProductController" ); // 产品结构
	} );
} );

//http://localhost/ext/log post
Route::group ( [ 
		'prefix' => 'ext',
		'namespace' => 'ExtServices' 
], function () {
	Route::post ( "/log", "AutoLogController@store" );
} );

Route::get ( "run", "Report\RunController@index" ); // 运行日志分析路由
Route::get ( "runadd", "Report\RunController@add" ); // 运行日志分析保存路由
Route::get ( "update", "Automation\Web\AutoScriptController@update2" ); // 脚本保存路由
Route::get ( "bug", "Report\RunController@bugindex" ); // bug日志分析路由
Route::get ( "bugadd", "Report\RunController@bugadd" ); // bug日志分析保存路由





//App测试路由
//Route::group ( [
//    'prefix' => 'app',
//    'namespace' => 'App'
//], function () {
//    Route::get ( "/script", "AppController@index" );
//} );

//App
Route::group ( [
    'prefix' => 'app',
    'namespace' => 'App'
], function () {
    Route::resource ( 'browser', 'AppBrowserController' );
    //Route::resource ( "report", "AppExecReportController" ); // 执行任务报告
    Route::group ( [
        'prefix' => 'web',
        'namespace' => 'Web'
    ], function () {
        // App仓库
        Route::group ( [
            'prefix' => 'script'
        ], function () {
            Route::resource ( 'uploader', 'AppScriptController@uploader' );
            Route::get ( "tree", "AppScriptController@getScriptTree" ); // 产品自动化脚本树结构
            Route::get ( "list", "AppScriptController@getScriptList" ); // 产品自动化脚本列表
            Route::get ( "annalslist", "AppScriptController@getAnnalsScriptList" ); // 历史脚本列表
            Route::get ( "backloglist", "AppScriptController@getBackLogList" ); // 回滚日志列表
            Route::get ( "scriptdiff", "AppScriptController@getScriptText" ); // 脚本内容
            Route::post ( "rollback", "AppScriptController@rollBack" ); // 脚本内容
            Route::post ( "params", "AppScriptController@storeParams" ); // 存储脚本的参数化文件
            Route::post ( "images", "AppScriptController@storeImages" ); // 存储脚本所需图片
            Route::any ( "index_choose/{id}/{scriptName}", "AppScriptController@index_choose" ); // 信息完善
            Route::any ( "choose", "AppScriptController@choose" ); // 信息完善
        } );
        Route::resource ( 'script', 'AppScriptController' );
        // 脚本案例
        Route::group ( [
            'prefix' => 'scheme'
        ], function () {
            Route::get ( "list", "AppSchemeController@getSchemeList" ); // 产品自动化案例列表
            Route::get ( "flowprocess/{id}", "AppSchemeController@getFlowProcess" ); // 流程设计
            Route::get ( "addflow", "AppSchemeController@addFlow" ); // 添加流程
            Route::post ( "addprocess", "AppSchemeController@addProcess" ); // 添加流程步骤
            Route::post ( "delprocess", "AppSchemeController@delProcess" ); // 删除流程步骤
            Route::post ( "saveflowprocess/{id}", "AppSchemeController@saveFlowProcess" ); // 保存流程步骤面板的所有信息
            Route::get ( "download/{id}", "AppSchemeController@downScheme" ); // 保存流程步骤面板的所有信息
            Route::get ( "exec/{id}", "AppSchemeController@exec" ); // 案例执行
        } );
        Route::resource ( 'scheme', 'AppSchemeController' );

        // App任务
        Route::group ( [
            'prefix' => 'task'
        ], function () {
            Route::get ( "list", "AppTaskController@getTaskList" ); // 产品自动化案例列表
            Route::resource ( "exec", "AppTaskExecController" ); // 执行任务
        } );
        Route::resource ( 'task', 'AppTaskController' );
    } );
} );

//App报告路由
Route::get ( "appreport/{taskExecID}/{browserId}/{browserName}", "APP\AppReportController@index" ); //获取报告blade模板
Route::get ( "appreportlist", "APP\AppReportController@add" ); //获取报告blade模板

Route::get ( "testtest", "APP\TestReportController@add" ); //获取报告blade模板





//测试
Route::get ( "testtest", "APP\TestReportController@add" ); //获取报告blade模板
Route::get ( "test123", "APP\TestReportController@userList" ); //获取报告blade模板
Route::get ( "getScriptID", "APP\TestReportController@getScriptID" ); //获取getScriptID



//邮件预览
Route::get("lookemail/{taskid}","Email\EmailController@index");//预览报告






// API
Route::group ( [
    'prefix' => 'api',
    'namespace' => 'Api'
], function () {
    Route::resource ( 'browser', 'ApiBrowserController' );
    //Route::resource ( "report", "ApiExecReportController" ); // 执行任务报告
    Route::group ( [
        'prefix' => 'web',
        'namespace' => 'Web'
    ], function () {
        // 脚本仓库
        Route::group ( [
            'prefix' => 'script'
        ], function () {
            Route::any ( 'uploader', 'ApiScriptController@uploader' );
            Route::get ( "tree", "ApiScriptController@getScriptTree" ); // 产品自动化脚本树结构
            Route::get ( "list", "ApiScriptController@getScriptList" ); // 产品自动化脚本列表
            Route::get ( "annalslist", "ApiScriptController@getAnnalsScriptList" ); // 历史脚本列表
            Route::get ( "backloglist", "ApiScriptController@getBackLogList" ); // 回滚日志列表
            Route::get ( "scriptdiff", "ApiScriptController@getScriptText" ); // 脚本内容
            Route::post ( "rollback", "ApiScriptController@rollBack" ); // 脚本内容
            Route::post ( "params", "ApiScriptController@storeParams" ); // 存储脚本的参数化文件
            Route::post ( "images", "ApiScriptController@storeImages" ); // 存储脚本所需图片
            Route::any ( "jiaoben", "ApiScriptController@update2" ); // 存储脚本所需图片
        } );
        Route::resource ( 'script', 'ApiScriptController' );
        // 脚本案例
        Route::group ( [
            'prefix' => 'scheme'
        ], function () {
            Route::get ( "list", "ApiSchemeController@getSchemeList" ); // 产品自动化案例列表
            Route::get ( "flowprocess/{id}", "ApiSchemeController@getFlowProcess" ); // 流程设计
            Route::get ( "addflow", "ApiSchemeController@addFlow" ); // 添加流程
            Route::post ( "addprocess", "ApiSchemeController@addProcess" ); // 添加流程步骤
            Route::post ( "delprocess", "ApiSchemeController@delProcess" ); // 删除流程步骤
            Route::post ( "saveflowprocess/{id}", "ApiSchemeController@saveFlowProcess" ); // 保存流程步骤面板的所有信息
            Route::get ( "download/{id}", "ApiSchemeController@downScheme" ); // 保存流程步骤面板的所有信息
            Route::get ( "exec/{id}", "ApiSchemeController@exec" ); // 案例执行
            //接口案例运行路由
            Route::get("apisehemes","ApiSchemeController@apisehemes");
            //接口案例运行log获取路由
            Route::get("apisehemeslog","ApiSchemeController@apisehemeslog");
            //接口案例运行log获取路由(新)
            Route::get("getSchemeLog/{taskexecid}","ApiSchemeController@getSchemeLog");
            Route::get ( "/schemePie", "ApiSchemeController@getTaskPies" ); // 报告绘图统计
        } );
        Route::resource ( 'scheme', 'ApiSchemeController' );

        // 案例任务
        Route::group ( [
            'prefix' => 'task'
        ], function () {
            Route::get ( "list", "ApiTaskController@getTaskList" ); // 产品自动化案例列表
            Route::resource ( "exec", "ApiTaskExecController" ); // 执行任务

            //接口任务运行路由
            Route::get("apitask","ApiTaskController@apitask");
            //接口任务运行log获取路由
            Route::get("apitasklog","ApiTaskController@apitasklog");

            //接口任务运行log获取路由(新)
            Route::get("getSchemeLog/{taskexecid}","ApiTaskController@getSchemeLog");
            Route::get ( "/schemePie", "ApiTaskController@getTaskPies" ); // 报告绘图统计
        } );
        Route::resource ( 'task', 'ApiTaskController' );
    } );
} );

//接口任务邮件发送模板重做
Route::get ( "getApiReport", "Api\web\ApiTaskController@getApiReport" );
//测试最新邮件报告路由
Route::get ( "newApiReport", "Api\web\ApiTaskController@newApiReport" );
//获取饼状图数据
Route::get ( "getAllReportInfo", "Api\web\ApiTaskController@getAllReportInfo" );

//历次任务执行结果趋势(点击可折叠)
Route::get ( "getAllTaskInfoSuccess", "Api\web\ApiTaskController@getAllTaskInfoSuccess" );






//接口案例运行路由
Route::get("apisehemes","Api\web\ApiSchemeController@insetrlogonce");
//接口任务运行路由
Route::get("apitasks","Api\web\ApiTaskExecController@apitasks");



//调查问卷

//接口案例运行路由
Route::get("/add","Api\web\ApiSchemeController@add");



//测试工具链接基下载
Route::group ( [
    'prefix'=>'camp',
    'namespace' => 'Campaign',
    'middleware' => 'auth'
], function () {
    Route:: resource( "/ult", "U8Controller@ult" );
    Route:: resource( "/mtt", "U8Controller@mtt" );
    Route:: resource( "/sett", "U8Controller@sett" );
    Route:: resource( "/dult", "U8Controller@dult" );
    Route:: resource( "/pct", "U8Controller@pct" );
    Route:: resource( "/js", "U8Controller@js" );
    Route:: resource( "/lsbcx", "U8Controller@lsbcx" );
    Route:: resource( "/gdi", "U8Controller@gdi" );
    Route:: resource( "/sjkjgdb", "U8Controller@sjkjgdb" );
    Route:: resource( "/wj", "U8Controller@wj" );
    Route:: resource( "/xn", "U8Controller@xn" );
    Route:: resource( "/ylzx", "U8Controller@ylzx" );
} );





// resource
Route::group ( [
    'prefix' => 'resource',
    'namespace' => 'Resource'
], function () {
    Route::group ( [
        'prefix' => 'web',
        'namespace' => 'Web'
    ], function () {
        // machine
        Route::group ( [
            'prefix' => 'machine'
        ], function () {
            //Route::get ( "index", "ResourceMachineController@index" ); // 资源池首页
            Route::get ( "list", "ResourceMachineController@getTaskList" ); // 资源池列表
            Route::get ( "checkAdd", "ResourceMachineController@checkAdd" ); // 新增时去重
            Route::get ( "checkDel", "ResourceMachineController@checkDel" ); // 删除时校验状态
            Route::get ( "checkEdit", "ResourceMachineController@checkEdit" ); // 编辑时校验状态
        } );
        Route::resource ( 'machine', 'ResourceMachineController' );


        //step
        Route::group ( [
            'prefix' => 'step'
        ], function () {
            Route::get ( "index", "ResourceStepController@index" ); // 产品自动化案例列表
            Route::get ( "list", "ResourceStepController@getTaskList" ); // 资源池列表
            Route::get ( "checkAdd", "ResourceStepController@checkAdd" ); // 新增时去重
            Route::get ( "checkDel", "ResourceStepController@checkDel" ); // 删除时校验状态
            Route::get ( "checkEdit", "ResourceStepController@checkEdit" ); // 编辑时校验状态
        } );
        Route::resource ( 'step', 'ResourceStepController' );

        //plan
        Route::group ( [
            'prefix' => 'plan'
        ], function () {
            Route::get ( "index", "ResourcePlanController@index" ); // 产品自动化案例列表
            Route::get ( "list", "ResourcePlanController@getTaskList" ); // 产品自动化案例列表
            Route::resource ( "exec", "ResourcePlanController@exec" ); // 执行任务
            Route::resource ( "stop", "ResourcePlanController@stop" ); // 执行任务
            Route::resource ( "getMachineAndStep", "ResourcePlanController@getMachineAndStep" ); // 获取机器资源及步骤

            Route::get ( "editUpdate", "ResourcePlanController@editUpdate" ); // 编辑后保存

            Route::get ( "checkAdd", "ResourcePlanController@checkAdd" ); // 新增时去重
            Route::get ( "checkDel", "ResourcePlanController@checkDel" ); // 删除时校验状态
            Route::get ( "checkEdit", "ResourcePlanController@checkEdit" ); // 编辑时校验状态
            Route::get ( "editDelete", "ResourcePlanController@editDelete" ); // 编辑时删除当前任务下资源

            //任务运行路由
            Route::get("apitask","ResourcePlanController@apitask");
            //任务运行log获取路由
            Route::get("getLog","ResourcePlanController@getLog");
            //任务运行log模板展示路由
            Route::get("logInfo/{taskid}","ResourcePlanController@logInfo");

        } );
        Route::resource ( 'plan', 'ResourcePlanController' );
    } );
} );
//给杨学振金盘程序调用路由
Route::resource("/getData","Resource\web\ResourcePlanController@getData");
//获取金盘程序返回路由
Route::resource("/getDataBack","Resource\web\ResourcePlanController@getDataBack");



//评价
Route::resource("/login","Exam\ExamController@login");
Route::resource("/checkLogin","Exam\ExamController@checkLogin");

//获取所有备选人
Route::resource("/exam/fields","Exam\ExamController@getAllUser");
//根据备选人获取考评试题
Route::resource("/exam/question","Exam\ExamController@question");





