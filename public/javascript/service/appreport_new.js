$(function () {
    var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
    //让层自适应iframe
    $('#btn').on('click', function () {
        var p1 = $("#stepDetail > div:nth-child(3) > p").text();
        var p2 = p1.replace(":", ",");
        var p3 = p2.replace("{", "");
        var p4 = p3.replace("}", "");
        var p5 = p4.replace("}", "");
        var p6 = p5.replace("{", "");
        var p7 = p6.replace(":", ",");
        var p8 = p7.replace(":", ",");
        var p9 = p8.split(",");
        var p10 = p9[4];
        //window.location.href=("/auto/web/script/517/edit?readonly=0");
        function edit(id) {
            layer.open({
                type: 2,
                title: "编辑",
                skin: '', // 加上边框
                offset: ['50px'],
                area: ["960px", "460px"], // 宽高
                content: CommonUtil.getRootPath() + scriptUrl + "/" + id
                + "/edit?readonly=0",
                btn: ['保存', '取消'],
                yes: function (index, layero) {
                    // var body = layer.getChildFrame('body', index);
                    var iframeWin = window[layero.find('iframe')[0]['name']];
                    var code_editor = iframeWin.code_editor;
                    var new_code = code_editor.getValue();
                    if (iframeWin.old_code != new_code) {
                        var requestData = {
                            "code": new_code,
                            "productId": searchId
                        };
                        DataTableUtil.optListForm(tableId,
                            scriptUrl + "/" + id, "PUT", requestData,
                            function () {
                                layer.close(index);
                                me.searchScriptList();
                            }, false);
                    } else
                        layer.close(index);

                }
            });
        }
        edit(517);
        parent.layer.iframeAuto(index);
    });
});