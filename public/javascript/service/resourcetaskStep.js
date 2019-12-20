AutoTaskUtil = function(me) {
    var tableId = "tasklist";
    var taskUrl = "/resource/web/step";
    //var schemeUrl = "/auto/web/scheme";
    //var schTableId = "schemelist", projectId;
    var schemeIds = [];
    var oldSchemeIds = [];
    return me = {
        initOwn : function() {
            $("#search").on(
                "click",
                function() {
                    var searchReqData = {
                        "search" : {
                            "taskName" : $("#search_taskName").val(),
                            "creater" : $("#search_creater").val()
                        }
                    };
                    DataTableUtil.search(tableId, taskUrl + "/list",
                        searchReqData);
                    return false;
                });
        },
        init : function() {
            me.initOwn();
            me.createList();
            //me.createSchemeList();
            BrowserChecksUtil.init();
        },
        createList : function() {
            var aoColumns = [ { // <input type='checkbox'>
                "sTitle" : "",
                "data" : "id",
                "sClass" : "text-center"
            }, {
                "sTitle" : "步骤ID",
                "data" : "chrStepId"
            }, {
                "sTitle" : "步骤名称",
                "data" : "chrStepName"
            }, {
                "sTitle" : "最后更新时间",
                "data" : "updated_at"
            }];
            var request = CommonUtil.getRequest();
            var tablekv = {
                "chk" : true,
                "iDisplayLength" : 15,
                "tabtool" : true,
                "opt" : {
                    // "edit" : {
                    //     "display" : 1,
                    //     "info" : "编辑",
                    //     "func" : me.edit
                    // }
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
                if (requestData) {
                    $.ajax({
                        type : 'GET',
                        data : requestData,
                        url : '/resource/web/step/checkAdd',
                        success : function(ret) {
                            var res=$.parseJSON(ret);
                            if(res.success==0){
                                DataTableUtil.optListForm(tableId, taskUrl, "post",
                                    requestData, function() {
                                        callbackfn();
                                    }, true);
                            }else if(res.success==1){
                                layer.msg(res.data, {
                                    offset : "50px"
                                });
                            }else if(res.success==2){
                                layer.msg(res.data, {
                                    offset : "50px"
                                });
                            }
                        }
                    });

                }
            }
            me.openLayer(yes);
        },
        del : function(ids) {
            $.ajax({
                type : 'GET',
                data : {
                    'id':ids
                },
                url : '/resource/web/step/checkDel',
                success : function(ret) {
                    var res=$.parseJSON(ret);
                    if(res.success==1){
                        DataTableUtil.optListForm(tableId, taskUrl + "/" + ids, "DELETE",
                            "", function() {
                            }, true);
                    }else if(res.success==0){
                        layer.msg('当前资源正在使用无法删除', {
                            offset : "50px"
                        });
                    }
                }
            });
        },
        edit : function(id) {
            $.ajax({
                type : 'GET',
                data : {
                    'id':id
                },
                url : '/resource/web/step/checkEdit',
                success : function(ret) {
                    var res=$.parseJSON(ret);
                    if(res.success==1){

                        function yes(requestData, callbackfn) {
                            // if (schemeIds.length > 0) {
                            // 	requestData.schemes = schemeIds;// 新增+未删除的
                            // 	requestData.oldSchemeIds = oldSchemeIds;// 原有的
                            DataTableUtil.optListForm(tableId, taskUrl + "/" + id,
                                "PUT", requestData, function() {
                                    callbackfn();
                                }, true);
                            // }
                        }
                        CommonUtil
                            .requestService(taskUrl + "/" + id + "/edit", "", false,
                                "get", function(response, status) {
                                    if (response.success) {
                                        me.openLayer(yes);
                                        var task = response.task;
                                        $("#taskName").val(task.taskName);
                                        $("#MacIP").val(task.MacIP);
                                        $("#belongTo").val(task.belongTo);
                                    }
                                }, function(ex) {
                                });
                    }else if(res.success==0){
                        layer.msg('当前资源正在使用，无法编辑', {
                            offset : "50px"
                        });
                    }
                }
            });

        },
        openLayer : function(yes) {
            //me.reloadScheme();
            var form = $("#taskform");
            var width = form.width() + 30 + 'px';
            layer.open({
                type : 1,
                title : "资源信息",
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
                            "MacIP":$("#MacIP").val(),
                            "belongTo":$("#belongTo").val()
                        };
                        yes(requestData, function() {
                            layer.close(index);
                        });
                    }
                }
            });
        },
        // createSchemeList : function() {
        // 	var aoColumns = [ { // <input type='checkbox'>
        // 		"sTitle" : "",
        // 		"data" : "id",
        // 		"sClass" : "text-center"
        // 	}, {
        // 		"sTitle" : "案例名称",
        // 		"data" : "schemeName"
        // 	}, {
        // 		"sTitle" : "项目名称",
        // 		"data" : "projectName"
        // 	}, {
        // 		"sTitle" : "创建人",
        // 		"data" : "createUser"
        // 	} ];
        // 	var tablekv = {
        // 		"chk" : me.schemeCheck,
        // 		"iDisplayLength" : 5
        // 	};
        // 	DataTableUtil.load(schTableId, schemeUrl + "/list", aoColumns,
        // 			tablekv);
        // },
        // schemeCheck : function(event, ck) {
        // 	var row = JSON.parse($(ck).attr("row"));
        // 	switch (event.type) {
        // 	case "ifChecked":// 选中
        // 		me.changeCheckedScheme(row, 0);
        // 		break;
        // 	case "ifUnchecked":// 取消选中
        // 		me.changeCheckedScheme(row, 1);
        // 		break;
        // 	}
        // },
        // reloadScheme : function() {
        // 	$('#taskform input').iCheck('uncheck');
        // 	// DataTableUtil.refresh(schTableId);
        // 	$("#taskName").val("");
        // 	$("#checkedScheme").html("");
        // 	schemeIds = [];
        // 	oldSchemeIds = [];
        // },
        changeCheckedScheme : function(row, opt) {
            var conId = "check_" + row.id;
            var idx = $.inArray(row.id, schemeIds);
            switch (opt) {
                case 0:// 选中
                    if (!projectId || projectId == undefined
                        || projectId == row.projectId) {
                        projectId = row.projectId;
                        if (idx < 0) {
                            schemeIds.push(row.id);
                            $("#checkedScheme").append(
                                "<span chk='shceme' id='" + conId + "'>"
                                + row.schemeName + "<br/></span>");
                        }
                    } else
                        layer.msg("同一任务不允许包含不同项目的案例", {
                            offset : "50px"
                        });
                    break;
                case 1:// 取消选中
                    if (idx >= 0) {
                        schemeIds.splice(idx, 1);
                        $("#" + conId).remove();
                        if (schemeIds.length == 0)
                            projectId = 0;
                    }
                    break;
            }

        }
    };
}();
