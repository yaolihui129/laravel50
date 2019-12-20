AutoTaskUtil = function(me) {
    var tableId = "tasklist";
    var taskUrl = "/resource/web/plan";
    var execUrl = taskUrl + "/exec";
    var schemeUrl = "/resource/web/plan/getMachineAndStep";
    var schTableId = "schemelist", projectId;
    var schemeIds = [];
    var oldSchemeIds = [];
    var ifckeck = false;
    var getSelectData=[];
    return me = {
        initOwn : function() {
            $("#search").on(
                "click",
                function() {
                    var searchReqData = {
                        "search" : {
                            // "projectId" : $("#search_project").val(),
                            "state" : $("#search_state").val(),
                            "taskName" : $("#search_taskName").val()
                            // "creater" : $("#search_creater").val()
                        }
                    };
                    DataTableUtil.search(tableId, taskUrl + "/list",
                        searchReqData);
                    return false;
                });
        },
        init : function() {
            me.initOwn();
            me.createList(true);
            me.createSchemeList();
            BrowserChecksUtil.init();
        },
        createList : function(tabtool) {
            var aoColumns = [ { // <input type='checkbox'>
                "sTitle" : "",
                "data" : "id",
                "sClass" : "text-center"
            }, {
                "sTitle" : "任务名称",
                "data" : "chrTaskName"
            },
            //     {
            //     "sTitle" : "资源名称",
            //     "data" : "chrMachineName"
            // },
            //     {
            //     "sTitle" : "MAC地址",
            //     "data" : "chrMachineMacIP"
            // },
				// {
            //     "sTitle" : "步骤名称",
            //     "data" : "chrStepName"
            // },
                {
                "sTitle" : "任务状态",
                "data" : "state"
            },
                {
                "sTitle" : "任务时间",
                "data" : "updated_at"
            }];
            var request = CommonUtil.getRequest();
            var tablekv = {
                "chk" : true,
                "iDisplayLength" : 15,
                "tabtool" : tabtool,
                "opt" : {
                    "edit" : {
                        "display" : 1,
                        "info" : "编辑",
                        "func" : me.edit
                    },
                    "exec" : {
                        "display" : 1,
                        "info" : "执行",
                        "func" : me.exec
                    }
                    ,
                    "stop" : {
                        "display" : 1,
                        "info" : "立即停止",
                        "func" : me.stop
                    }
                    ,
                    "log" : {
                        "display" : 1,
                        "info" : "日志",
                        "func" : me.log
                    }
                }
            };
            if (!request["my"]) {
                var tools = {
                    "addtool" : me.add,
                    "deltool" : me.del
                };
                $.extend(tablekv, tools);
            }
            DataTableUtil.load(tableId, taskUrl + "/list", aoColumns, tablekv);
        },
        refresh : function() {
            if (parent.refreshParent == 1) {
                DataTableUtil.refresh(tableId);
            }
        },
        add : function() {
            function yes(requestData, callbackfn) {
                if (schemeIds.length > 0) {
                    requestData.machineId = schemeIds;
                    requestData.stepId=getSelectData;
                    DataTableUtil.optListForm(tableId, taskUrl, "post",
                        requestData, function() {
                            callbackfn();
                        }, true);
                }
            }
            projectId = 0;
            me.openLayer(yes);

        },
        del : function(ids) {
            $.ajax({
                type : 'GET',
                data : {
                    'id':ids
                },
                url : '/resource/web/plan/checkDel',
                success : function(ret) {
                    var res=$.parseJSON(ret);
                    if(res.success==1){
                        DataTableUtil.optListForm(tableId, taskUrl + "/" + ids, "DELETE",
                            "", function() {
                            }, true);
                    }else if(res.success==0){
                        layer.msg('当前任务正在运行无法删除', {
                            offset : "50px"
                        });
                    }
                }
            });
        },
        edit : function(id) {
            //编辑后保存函数
            function yes(requestData, callbackfn) {
                if (schemeIds.length > 0) {
                    requestData.machineIds = schemeIds;// 新增+未删除的
                    requestData.oldMachineIds = oldSchemeIds;// 原有的
                    requestData.planId=id;
                    requestData.step=getSelectData;
                    DataTableUtil.optListForm(tableId, taskUrl + "/editUpdate",
                        "GET", requestData, function() {
                            callbackfn();
                        }, true);
                }
            }
            //打开编辑界面
            CommonUtil
                .requestService("/resource/web/plan" + "/" + id + "/edit", "", false,
                    "get", function(response, status) {
                        if (response.success) {
                            me.openLayer(yes);
                            var task = response.task;
                            projectId = task.projectId;
                            //var projectIdArray=projectId.split(',');
                            //getSelectData.push(projectIdArray);
                            getSelectData=[];
                            $("#taskName").val(task.taskName);
                            var stepArray=[];
                            for ( var scheme in task.schemes) {
                                var projectIdArray=task.schemes[scheme].projectId.split(',');
                                getSelectData.push(projectIdArray);
                                oldSchemeIds.push(task.schemes[scheme].id);
                                stepArray=(task.schemes[scheme].projectId).split(',');
                                me.changeCheckedScheme(task.schemes[scheme], 0,stepArray,id);
                            }
                        }else{
                            layer.msg('任务正在运行，无法编辑', {
                                offset : "50px"
                            });
                        }
                    }, function(ex) {
                    });
        },
        openLayer : function(yes) {
            ifckeck = true;
            me.reloadScheme();
            var form = $("#taskform");
            var width = form.width() + 30 + 'px';
            layer.open({
                type : 1,
                title : "基本信息",
                skin : '', // 加上边框
                area : [ width ], // 宽高
                offset : [ '50px' ],
                content : form,
                btn : [ '保存', '取消' ],
                yes : function(index, layero) {
                    var error = ValidateUtil.validate("taskform");
                    if (!error) {
                        var requestData = {
                            "taskName" : $("#taskName").val(),
                            "projectId" : projectId
                        };
                        yes(requestData, function() {
                            layer.close(index);
                        });
                    }
                }
            });
        },
        createSchemeList : function() {
            var aoColumns = [ { // <input type='checkbox'>
                "sTitle" : "",
                "data" : "id",
                "sClass" : "text-center"
            }, {
                "sTitle" : "资源名称",
                "data" : "chrMachName"
            }, {
                "sTitle" : "Mac地址",
                "data" : "chrMacIP"
            }
            , {
                "sTitle" : "状态",
                "data" : "state"
            }
            ];
            var tablekv = {
                "chk" : me.schemeCheck,
                "iDisplayLength" : 3,
                'getData':true
            };
            DataTableUtil.load(schTableId, schemeUrl, aoColumns,
                tablekv);
        },
        schemeCheck : function(event, ck) {
            var row = JSON.parse($(ck).attr("row"));
            var parentElement=ck.parentElement;
            var ele=$(parentElement).parent().parent().siblings();
            var select=$(ele[3]).find('select');
            var selectData=$(select).val();
            getSelectData.push(selectData);
            //getSelectData=selectData;
            //var selectText=$(select).text();
            if(row.state=='执行中'||row.state=='排队中' ){
                alert('当前资源正在使用，请选择其他资源')
            }else {
                switch (event.type) {
                    case "ifChecked":// 选中
                        me.changeCheckedScheme(row, 0, selectData,'');
                        break;
                    case "ifUnchecked":// 取消选中
                        me.changeCheckedScheme(row, 1, selectData,'');
                        break;
                }
            }
        },
        reloadScheme : function() {
            $('#taskform input').iCheck('uncheck');
            // DataTableUtil.refresh(schTableId);
            $("#taskName").val("");
            $("#checkedScheme").html("");
            schemeIds = [];
            oldSchemeIds = [];
            getSelectData=[];
        },
        changeCheckedScheme : function(row, opt,selectData,planId) {

            if(selectData==null){
                alert('步骤为空，请重新选择');
            }else{
                var conId =  row.id;
                var ID=conId.toString();
                var idx = $.inArray(ID, schemeIds);
                switch (opt) {
                    case 0:// 选中
                        var selectName='';
                        //if (ifckeck) {
                        // if (!projectId || projectId == undefined
                        //     || projectId == row.projectId) {
                            projectId = row.projectId;
                            if (idx < 0) {
                                schemeIds.push(row.id);
                                $("#checkedScheme").append(
                                    "<span chk='shceme' id='machine_" + row.id + "'>"
                                    + row.chrMachName + ":"+
                                    // '<button id="clearMachine_'+row.id+ '"style="float: right" type="button" class="btn btn-primary btn-xs" machine='+row.id+' step='+row.projectId+'>删除</button>'+
                                    "<br/></span>");
                                for (var i = 0; i < selectData.length; i++) {
                                    if (selectData[i] == 10) {
                                        selectName = "产品安装";
                                    } else if (selectData[i] == 11) {
                                        selectName = "产品升级";
                                    } else if (selectData[i] == 12) {
                                        selectName = "产品卸载";
                                    } else if (selectData[i] == 14) {
                                        selectName = "获取安装盘";
                                    } else if (selectData[i] == 15) {
                                        selectName = "配置数据源";
                                    } else if (selectData[i] == 16) {
                                        selectName = "初始化数据库";
                                    } else if (selectData[i] == 18) {
                                        selectName = "账套初始化";
                                    } else if (selectData[i] == 19) {
                                        selectName = "验盘";
                                    } else if (selectData[i] == 20) {
                                        selectName = "设置加密服务器";
                                    }else{
                                        selectName = "";
                                    }
                                    $("#checkedScheme").append(
                                        "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<span chk='shceme' class='machine_" + row.id + "' id='step_" + selectData[i] + "'>"
                                        + selectName + "<br/></span>");

                                }
                                //绑定删除事件，方便编辑时删除某一资源及步骤
                                $("#clearMachine_"+row.id).on('click',function () {
                                    $("#machine_"+row.id).empty();//前端删除选定资源
                                    $(".machine_"+row.id).empty();//前端输出选定步骤
                                    var indexSchemeIds=schemeIds.indexOf(row.id);
                                    // var indexGetSelectData=getSelectData.indexOf(row.projectId);
                                    schemeIds.splice(indexSchemeIds,1);// 删除传输后台的数组中选定资源
                                    getSelectData.splice(indexSchemeIds,1);//删除传输后台的数组中选定步骤
                                    //前台点击删除时，后台数据库同步删除当前任务下已选择删除的资源数据
                                    $.ajax({
                                        url:"/resource/web/plan/editDelete",
                                        data:{
                                            'planId':planId,
                                            'machineId':row.id
                                        },
                                        type:"GET",
                                        success:function (e) {
                                            var res=$.parseJSON(e);
                                            if(res.success==1){
                                                alert('资源删除成功');
                                            }else{
                                                // alert('资源删除失败');
                                            }
                                        }

                                    })
                                });

                            }
                        // } else
                        //     layer.msg("同一任务不允许包含不同项目的案例", {
                        //         offset : "50px"
                        //     });
                        ifckeck = false;
                        break;
                    case 1:// 取消选中
                        if (idx >= 0) {
                            schemeIds.splice(idx, 1);
                            getSelectData.splice(idx, 1);
                            $("#" + conId).remove();
                            if (schemeIds.length == 0)
                                projectId = 0;
                        }

                        //取消选中时，清除数组及右侧显示已选择的资源
                        $("#machine_"+row.id).empty();//前端删除选定资源
                        $(".machine_"+row.id).empty();//前端删除选定步骤
                        var indexSchemeIds=schemeIds.indexOf(row.id);
                        // var indexGetSelectData=getSelectData.indexOf(row.projectId);
                        schemeIds.splice(indexSchemeIds,1);// 删除传输后台的数组中选定资源
                        getSelectData.splice(indexSchemeIds,1);//删除传输后台的数组中选定步骤
                        getSelectData.pop();
                        //前台点击删除时，后台数据库同步删除当前任务下已选择删除的资源数据
                        $.ajax({
                            url:"/resource/web/plan/editDelete",
                            data:{
                                'planId':planId,
                                'machineId':row.id
                            },
                            type:"GET",
                            success:function (e) {
                                var res=$.parseJSON(e);
                                if(res.success==1){
                                    alert('资源删除成功');
                                }else{
                                    // alert('资源删除失败');
                                }
                            }

                        });
                        ifckeck = true;
                        break;
                }
            }

        },
        stop : function(id) {
            var form = $("#stop");
            var width = form.width() + 30 + 'px';
            layer.open({
                type : 1,
                title : "任务停止",
                skin : '', // 加上边框
                offset : [ '30px' ],
                area : [ width ], // 宽高
                content : form,
                btn : [ '确认停止', '取消' ],
                yes : function(index, layero) {
                    CommonUtil.requestService("/resource/web/plan/stop" + "/" + id, "", true, "get",
                        function(response, status) {
                            if (response.success==1) {
                                //me.execDetail(id, response.data);
                                //alert(response.msg);
                                layer.close(index);
                                layer.msg(response.msg, {
                                    offset : [ '50px' ]
                                });
                                //window.location.reload();
                                me.createList(false);
                            }else if(response.success==0){
                                //alert(response.msg);
                                layer.close(index);
                                layer.msg(response.msg, {
                                    offset : [ '50px' ]
                                });
                                //me.createList(false);
                            }
                        }, function(ex) {
                        });
                }
            });


        },
        exec : function(id) {
            me.execReload();
            CommonUtil.requestService(execUrl + "/" + id, "", true, "get",
                function(response, status) {
                    if (response.success==1) {
                        me.execDetail(id, response.data);
                    }else if(response.success==0){
                        alert(response.error);
                    }
                }, function(ex) {
                });

        },
        execReload : function() {
            BrowserChecksUtil.reset();
            //$('#browserChecks input').iCheck('uncheck');
            $("input[name='email']:first").iCheck('check');
            $("#emailReceiver").css({
                "display" : "none"
            });
            $("#emailReceivers").val('');
        },
        execDetail : function(id, row) {
            var form = $("#execform");
            var width = form.width() + 30 + 'px';
            var sendEmail = false;
            layer.open({
                type : 1,
                title : "任务执行",
                skin : '', // 加上边框
                offset : [ '50px' ],
                area : [ width ], // 宽高
                content : form,
                btn : [ '确认执行', '取消' ],
                yes : function(index, layero) {
                    //var selBrowsers = BrowserChecksUtil.getSelBrowsers();
                    //if (selBrowsers.length > 0) {
                    //     var requestData = {
                    //         //"selBrowsers" : selBrowsers,
                    //         "emails" : (sendEmail ? $("#emailReceivers").val()
                    //             : ""),
                    //         "taskId" : id
                    //     };
                    //     DataTableUtil.optListForm(tableId, execUrl, "get",
                    //         requestData, function(response) {
                    //             layer.close(index);
                    //         }, true);
                    //}
                    // setTimeout(function () {
                        $.ajax({
                            url:taskUrl+"/apitask",
                            data:{
                                id:id,
                                emails : (sendEmail ? $("#emailReceivers").val() : "")
                            },
                            type:"get",
                            success:function (json) {
                                var res=$.parseJSON(json);
                                if(res.success==1){
                                    layer.close(index);
                                    //window.location.reload();
                                    me.createList(false);
                                }else {
                                    layer.close(index);
                                    //window.location.reload();
                                    me.createList(false);
                                }
                            }
                        })
                    // },10000)
                }
            });

            $("input[name='email']").off("ifChecked");
            $("input[name='email']").on("ifChecked", function(event) {
                var option = $(this).val();
                var erCss = {
                    "display" : "none"
                };
                sendEmail = false;
                if (option == "1") {
                    erCss = {
                        "display" : ""
                    };
                    sendEmail = true;
                }
                $("#emailReceiver").css(erCss);
            });
            if (detail) {// 编辑 最分数  最高分
                /*
                 * asynType = "PUT"; optUrl += "/" + id;
                 */
                var browserIds = detail.browserIds.split(";");
                for ( var broIdx in browserIds) {
                    $(
                        "#browserChecks input[value='" + browserIds[broIdx]
                        + "']").iCheck("check");
                }
                $("input[name='email'][value='" + detail.sendEmail + "']")
                    .iCheck("check");
                $("#emailReceivers").val(detail.emails);
            }

        },
        report : function(id, row) {
            //开启加载中效果
            var index = layer.load();
            var id=id;
            var row=row;
            $.ajax({
                type:"get",
                url:taskUrl+"/apitasklog",
                data:{
                    id:id,
                    row:row
                },
                success:function(json){
                    var result=$.parseJSON(json);
                    //关闭加载中效果
                    layer.close(index);
                    layer.open({
                        title: '日志详情',
                        content: '<table border="" cellspacing="1" cellpadding="8"><tbody id="mm" ><tr style="height: 20px;"><th style="width: 100px;">任务</th><th style="width: 100px;">结果</th><th style="width: 100px;">耗时</th></tr><tr></tr></tbody></table>',
                        area:["850px","600px"],
                        btn:['确认','取消'],
                        offset: '70px',
                        title:"日志详情",
                        skin:"",
                        success:function () {
                            for(var i = 0; i < result.data.length; i++) {
                                var model =
                                    '<tr>'+
                                    '<td>'+result.data[i]["apisehemename"]+'</td>'+
                                    '<td>'+result.data[i]["log"]+'</td>'+
                                    '<td>'+result.data[i]["time"]+'</td>'+
                                    '</tr>';
                                $(model).appendTo($("#mm"));
                            }
                        }
                    });


                }
            });
        },
        log : function(id) {
            if(id){
                layer.open({
                    type: 2,
                    title: "任务运行日志",
                    skin: '', // 加上边框
                    offset: ['50px'],
                    area: ["900px", "600px"], // 宽高
                    content: taskUrl+"/logInfo/" + id
                });
            }else{
                layer.msg("报告未生成，暂时不能查看", {
                    offset : [ '50px' ]
                });
            }
        },
        //4-19上线邮件预览功能
        look : function(id, row) {
            if(row.taskExecID){
                layer.open({
                    type: 2,
                    title: "邮件预览",
                    skin: '', // 加上边框
                    offset: ['50px'],
                    area: ["900px", "500px"], // 宽高
                    content: "/lookemail/" + id
                });
            }else{
                layer.msg("报告未生成，暂时不能查看", {
                    offset : [ '50px' ]
                });
            }
        }
    };
}();
