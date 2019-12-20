//定义全局变量
var memInfo;
var basePath = "/";
//var basePath = "http://172.20.19.200:10555/yycollege/";
//var basePath = "http://10.2.104.43:8080/yycollege/";
var localBasePath = basePath;
var printAlert = false;
var totalPapreCounts = 20;
var timerId;
/*答题计时器ID*/
var userdTime;
/*答题过程中所花费的时间*/
var countTimer;
/*3 2 1倒计时*/
var scoreTimer;
/*答题计时器ID*/
var thisLeftTime_best1;
var thisRightTime_best1;
var thisCurrent;

var thisLeftTime = "";
var thisRightTime = "";
var thisCurrent2 = "";
var bestLeftTime = "";
var bestRightTime = "";
var bestPercent = "";
var startLine = new Date("2017/01/04 00:00:00").getTime();
var deadLine = new Date("2099/01/11 23:59:59").getTime();
var examStore = [];
var allSum = 0;
var examIdAndStore = "";


var getMemid='';
var getName='';
var getDeptName='';
var getCompanyName='';
var getCode='';


function tiktok() {
    var timeUsed = 0,
        scoreTimer = setInterval(function () {
            tStr = (((++timeUsed) / 100).toFixed(2) + '').split('.');
            timerId = scoreTimer;
            userdTime = timeUsed;
            $('.used_time_info input').eq(1).val(tStr[1]) && $('.used_time_info input').eq(0).val(tStr[0]);
        }, 10);

}

/*获取的所有题目*/
var paperArr = [];
/*当前第几题*/
var answerCount = 1;

$(function () {
        /*快速点击事件*/
        FastClick.attach(document.body);
        setHeader(true);


        //接收来自U8应用首页的参数
        function getUrlParam(name) {
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
            var r = window.decodeURIComponent(location.search).substr(1).match(reg);  //匹配目标参数
            if (r != null) return unescape(r[2]); return null; //返回参数值
        }

        getMemid=getUrlParam('memberId');
        getName=getUrlParam('name');
        getDeptName=getUrlParam('deptName');
        getCompanyName=getUrlParam('companyName');
        getCode=getUrlParam('code');
        console.log('memberId:'+getMemid);
        console.log('name:'+getName);
        console.log('deptName:'+ getDeptName);
        console.log('companyName:'+ getCompanyName);
        console.log('code:'+ getCode);

        //根据U8应用首页传递参数获取用户信息  查看用户成绩并初始化答题须知/竞赛说明页面
        if (getMemid) {
            memInfo = {
                "memberId": getMemid,
                "name": getName,
                "deptName": getDeptName,
                "companyName": getCompanyName
            };
            getUserScores();
        } else if (!getMemid) {
            getUserInfo();
        }






        // //获取用户信息  查看用户成绩并初始化答题须知/竞赛说明页面
        // if (getRequest(location.search)["memberId"] && !getRequest(location.search)["code"]) {
        //     var decodeStr = decodeURIComponent(location.search);
        //     memInfo = {
        //         "memberId": getRequest(decodeStr)["memberId"],
        //         "name": getRequest(decodeStr)["name"],
        //         "deptName": getRequest(decodeStr)["deptName"],
        //         "companyName": getRequest(decodeStr)["companyName"]
        //     };
        //     getUserScores();
        // } else if (!getRequest(location.search)["memberId"]) {
        //     getUserInfo();
        // }

        //判断本地存储是否有值
        // var localObj = JSON.parse(localStorage.getItem("localObjStr"));
        // if (localObj && (localObj.code == getRequest(location.search)["code"])) {
        //     //处理不了直接关闭
        //     var func1 = function (YonYouJSBridge) {
        //         function quit() {
        //             var data = '{"function":"closePage"}';
        //             YonYouJSBridge.send(data, function (responseData) {
        //             });
        //         };
        //         quit();
        //     };
        //     connectWebViewJavascriptBridge(func1);
        // }

        //初始化home page 底部div
        var screenHeight = document.body.clientHeight;
        var homePicHeight = $(".home").height();
        var homeOptHeight = px2rem(screenHeight - homePicHeight);
        $(".home_opt").css("height", homeOptHeight + 'rem');

        $(".home_page").addClass("cur_page");

        //点击开始考试按钮响应
        $(".home_opt .start").off("click").on("click", function () {
            paperArr.length = 0;
            beginExam();
        });

        //开始答题的另一个通道
        /*$(".score_page .top").off("click").on("click",function(){
            $(".cur_page").addClass("hidden");
            beginExam();
        });*/

        function beginExam() {

            var fieldCode = $("#ly").val();
            var productCode = $("#cp").val();
            var user=$("#user").val();
            var pwd=$("#pwd").val();
            var choose=$("#ly").val();
            $.ajax({
                url:'/checkLogin',
                type: "get",
                data:{
                    'user':user,
                    'pwd':pwd,
                    'choose':choose
                },
                success:function (json) {
                    var result = $.parseJSON(json);

                    if(result.success==0){
                        if (choose != '') {
                            var nowTime = new Date().getTime();
//		if(nowTime < startLine || nowTime > deadLine){
                            if (nowTime > deadLine) {
                                return false;
                            } else {
                                //清空答题时间计时器,答题计数器从1开始,防止人为操作返回继续答题
                                clearInterval(timerId);
                                answerCount = 1;
                                //切换页头
                                setHeader(false);
                                var route1 = {"route": "#count"};
                                addroute(route1);
                                //获取试题
                                getAndShowPagers(choose);

                                var countDownPage = $(".count_down_page");
                                $(".cur_page").addClass("hidden");
                                $(".cur_page").removeClass("cur_page");
                                countDownPage.addClass("cur_page").removeClass("hidden");

                                //这里做个判断如果倒计时不是从3开始那么要从三开始
                                $(".time_count_down").css("background-image", "url(./assets/img/count_down_3.jpg)");

                                //开启倒计时定时器
                                var trace = 2;
                                countTimer = setInterval(function () {
                                    if (trace == 0) {
                                        clearInterval(countTimer);
                                        blockExamPage();
                                        return false;
                                    }
                                    var imgSrc = "./assets/img/count_down_" + trace + ".jpg";
                                    $(".time_count_down").css("background-image", "url(" + imgSrc + ")");
                                    trace--;
                                }, 1000);

                            }
                        } else {
                            alert("请选择人员进行评价");
                        }

                    }else if(result.success==1){
                        alert(result.msg);
                    }else if(result.success==2){
                        alert(result.msg);
                    }
                }
            });





        }

        //点击英雄榜事件
        $(".rank").on("click", function () {
            //切换页头
            setHeader(false);
            var route1 = {"route": "#scores"};
            addroute(route1);
            //获取英雄榜数据(默认周排行)

            //解决考试完后显示异常周排行和总排行
            $(".score_page").show();

            getHeroWeekData();
            //$(".week_rank").click();
            //$(".total_rank").click();
            var heroPage = $(".score_page");
            $(".cur_page").addClass("hidden");
            $(".cur_page").removeClass("cur_page");
            heroPage.addClass("cur_page").removeClass("hidden");


        });

        $('.share').on("click", function () {
            //切换页头
            setHeader(false);
            $(".header .htitle").text("最佳成绩");
            var route1 = {"route": "#share"};
            addroute(route1);
            $(".exam_pass_page").addClass("hidden");
            $(".cur_page").removeClass("cur_page");
            $('.share_score_page').removeClass('hidden');
            $('.share_score_page').addClass('cur_page');
            if ($('.best_scores').css('display') == 'none') {
                $(".share_rank_div span i").text($(".this_victory span i").text());
            } else {
                $(".share_rank_div span i").text($(".best_scores span i.best").text());
            }
        });

        //答题选择事件(点击选项也可以)
        $(".options").off("click").on("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            $(this).siblings(".options").children("input").removeAttr("checked");
            $(this).children("input").attr("checked", "checked");

            //if ($(this).children("label").attr("for") == $(".title_content").attr("ans")) {
                //答对
//			var randomInt = getRanInt(5);
                var randomInt = "new";
                $(this).css("color", "#3fb31b");
                $(".model_layer").css("display", "block");
                $(".model_content").css("display", "block");
                $(".answer_pic").css("background-image", "url(./assets/img/wright/wright_" + randomInt + ".jpg)");
                $(".answer_res_info").css("display", "block");
                //$(".answer_res_info").html("恭喜你,答对了!");
                $(".answer_res_info").css("background-color", "#DD4B4B");

                $(this).children("label").css("background-image", "url(./assets/img/answer_wright.png)");
                //$("#next").empty();
                setTimeout(hideModelWright, 1000);


                var thisStore=$(this).children("label").attr("for");
                //考试成绩进入数组
                var rightStore = 5;
                if(thisStore=='a'){
                    rightStore=1;
                }else if (thisStore=='b'){
                    rightStore=2;
                }else if (thisStore=='c'){
                    rightStore=3;
                }else if(thisStore=='d'){
                    rightStore=5;
                }
                var rightQuestionId = $(".title_content").attr("questionId");
                var rightProductCode = $(".title_content").attr("ProductCode");
                var right = [];
                right['id'] = rightQuestionId;
                right['productcode'] = rightProductCode;
                right['store'] = rightStore;

                examStore.push(right);
                //console.log(examStore);
            //}
            // else {
            //     //答错
            //     var randomInt = "new";
            //     $(this).css("color", "#DD4B4B");
            //     $(".model_layer").css("display", "block");
            //     $(".model_content").css("display", "block");
            //     $(".answer_pic").css("background-image", "url(./assets/img/wrong/wrong_" + randomInt + ".jpg)");
            //     $(".answer_res_info").css("display", "block");
            //     //$(".answer_res_info").html("再接再厉哦!");
            //     $(".answer_res_info").css("background-color", "#FAAB00");
            //     $(this).children("label").css("background-image", "url(./assets/img/answer_wrong.png)");
            //     alert("正确答案是:" + $(".title_content").attr("ans"));
            //     //$("#next").empty();
            //     setTimeout(hideModelWright, 1000);
            //
            //
            //     //考试成绩进入数组
            //     var wrongStore = 0;
            //     var wrongQuestionId = $(".title_content").attr("questionId");
            //     var wrongProductCode = $(".title_content").attr("ProductCode");
            //     var wrong = [];
            //     wrong['id'] = wrongQuestionId;
            //     wrong['productcode'] = wrongProductCode;
            //     wrong['store'] = wrongStore;
            //
            //     examStore.push(wrong);
            //
            //     //console.log(examStore);
            // }
        });

        //答完题后点击"在挑战一次" 响应事件
        $(".challenge_again_new").on("click", function () {
            var reAnswerPage = $(".home_page");
            addroute(route);
            $(".cur_page").addClass("hidden");
            $(".cur_page").removeClass("cur_page");
            reAnswerPage.addClass("cur_page").removeClass("hidden");
            var screenHeight = document.body.clientHeight;
            var homePicHeight = $(".home").height();
            var homeOptHeight = px2rem(screenHeight - homePicHeight);
            $(".home_opt").css("height", homeOptHeight + 'rem');
            getUserScores();
            //window.location.reload();
        });

        $('#shareexam').on('click', function () {
            //分享
            var func = function (YonYouJSBridge) {
                function share() {
                    var url1 = "http://yycollege.upesn.com/yycollege/exam/share.html?memId=" + memInfo.memberId;
                    var imgpath = "http://yycollege.upesn.com/yycollege/exam/assets/img/shareicon.png";
                    var title = '全员挑战赛答题挑战赛', content = '我在全员挑战赛的答题比赛中挑战成功，你也来试试吧，还可以抽奖哦！',
                        data = '{"function":"share","parameters":{"content":"' + content + '","title":"' + title + '","url":"' + url1 + '","imgUrl":"' + imgpath + '"}}';
                    YonYouJSBridge.send(data, function (responseData) {
                    });
                }
                share()
            };

            connectWebViewJavascriptBridge(func);


        });

        $('.header .close').on('click', function () {
            var func1 = function (YonYouJSBridge) {
                function quit() {
                    var data = '{"function":"closePage"}';
                    YonYouJSBridge.send(data, function (responseData) {
                    });
                }
                quit();
            };
            connectWebViewJavascriptBridge(func1);
        });

        $(document).off("click", ".confirm_quit").on("click", ".confirm_quit", function () {
            $(".cur_page").addClass('hidden');
            $(".cur_page").prev().addClass('hidden');
            $(".cur_page").prev().prev().removeClass('hidden');
            $('.cur_page').removeClass('cur_page');
            $(".home_page").addClass("cur_page");
            showQuitModel("none", "");
            setHeader(true);
            Navroute(route);
            if (countTimer) {
                clearInterval(countTimer);
            }
            if (countTimer) {
                clearInterval(scoreTimer);
            }
        });
        $(document).off("click", ".cancel_quit").on("click", ".cancel_quit", function () {

            var param = {"route": "#exam"};
            addroute(param);
            showQuitModel("none", "");
        });

        $('.get_prize').on('click', payward);

        /*周排行*/
        $(".week_rank").on("click", getHeroWeekData);

        /*总排行*/
        $(".total_rank").on("click", getHeroData);


        /**
         * 根据code获取用户信息
         */
        function getUserInfo() {

            //获取code
            var code = getCode;
            //获取用户信息
            var URL = basePath + "getUserInfo?code=" + code;
            $.ajax({
                type: 'GET',
                async: true,
                url: URL,
                cache: false,
                contentType: "application/json",
                success: function (backData) {
                    //alert("欢迎:"+JSON.stringify(backData.data.name));
                    var alldata = backData.data;
                    var str = alldata.memberId;
                    var str1 = alldata.name;
                    var str2 = alldata.deptName;
                    var str3 = alldata.companyName;
                    $(".answer_counts_left").attr('memberId', str);
                    $(".answer_counts_left").attr('name', str1);
                    $(".answer_counts_left").attr('deptName', str2);
                    $(".answer_counts_left").attr('companyName', str3);
                    if (backData.flag == "0") {
                        memInfo = backData.data;
                        getUserScores();
                    } else {
                        $(".loading_layer").css("display", "none");
                        $(".loading_content").css("display", "none");
                    }
                },
                error: function (tt) {
                    debugInfo(tt);
                }
            });

            //便于测试
//		if (!memInfo){
//			memInfo = {"memberId":"2851030","name":"刘海超","deptName":"社交与协同办公服务事业部-社交与协同服务后台研发部","companyName":"用友股份"};
//			getUserScores();
//		}

        }

        /**
         * 查询用户最好成绩
         */
        function getUserScores() {
            var memId = memInfo.memberId;
            var url = basePath + "exam/find?memberId=" + memId;
            $.ajax({
                type: 'GET',
                async: true,
                url: url,
                cache: false,
                contentType: "application/json",
                success: function (backData) {
                    if (backData.flag == "0") {
                        var resData = backData.data;
                        var leftTime = formatMicroTime(resData.bestDuration)[0];
                        var rightTime = formatMicroTime(resData.bestDuration)[1];
                        thisLeftTime_best1 = leftTime;
                        thisRightTime_best1 = rightTime;
                        //初始化需要成绩的各个页面
                        if ($(".home_page").hasClass("cur_page")) {
                            //更换banner
                            $(".home_best_score").css("display", "block");
                            $(".home_score_div input").eq(0).val(leftTime);
                            $(".home_score_div input").eq(1).val(rightTime);
                            $(".home_rank_div span i").html(resData.bestPercent);
                            $(".home").css("background-image", "url(./assets/img/exam_notice_new.jpg)");
                        }

                        setTimeout(function () {
                            //隐藏loading
                            $(".loading_layer").css("display", "none");
                            $(".loading_content").css("display", "none");
                        }, 2000);
                    } else {
                        //没有成绩
                        $(".loading_layer").css("display", "none");
                        $(".loading_content").css("display", "none");
                    }
                },
                error: function (tt) {
                    debugInfo(tt);
                }
            });
        }

        /**
         * 获取并展示试题
         */
        function getAndShowPagers(choose) {
            var paperNums = totalPapreCounts;
            var url = basePath + "exam/question";
            $.ajax({
                type: 'GET',
                async: true,
                url: url,
                data: {
                    'fieldCode': 'FAG',
                    'productCode': 'GL',
                    'choose':choose
                },
                success: function (json) {
                    console.log(JSON.stringify(backData));
                    var backData=$.parseJSON(json);
                    if (backData.flag == "0") {
                        /*重置题目及分数*/
                        paperArr.length = 0;
                        allSum=0;
                        //已成功获取试题
                        paperArr = backData.data;
                        //将第一题展现  (用完之后就从数组中除掉)
                        var firstQuestion = paperArr.splice(0, 1)[0];
                        $(".answer_counts_right i").html(answerCount);
                        $(".title_content").html("(单选题)" + answerCount + "." + firstQuestion.title);
                        $(".title_content").attr("ans", firstQuestion.answer);
                        $(".title_content").attr("questionId", firstQuestion.id);
                        $(".title_content").attr("productcode", firstQuestion.productcode);
                        $("#a").siblings("span").html("a." + firstQuestion.a);
                        $("#b").siblings("span").html("b." + firstQuestion.b);
                        if (firstQuestion.c == "") {
                            $("#anc").hide();
                        } else {
                            $("#c").siblings("span").html("c." + firstQuestion.c);
                        }
                        if (firstQuestion.d == "") {
                            $("#and").hide();
                        } else {
                            $("#d").siblings("span").html("d." + firstQuestion.d);
                        }
                        //$("#next").append('<img src="/exam/assets/img/next.png"/>');

                    } else {
                        alert("所选产品试题不足考试数目,请重新选择试题.");
                        //history.back();
                        //window.location.reload();
                    }
                },
                error: function (tt) {
                    debugInfo(tt);
                }
            });
        }

        /**
         * 获取英雄榜数据(总排行)
         */
        function getHeroData() {
            $(".cur_rank").removeClass("cur_rank");
            $(".total_rank").addClass("cur_rank");
            $(".no_rank").css("display", "none");//总榜肯定有数据
            var top = 20;
            //获取自己的最好成绩
            var url = basePath + "exam/find?memberId=2851030";
            var myRank = "";
            $.ajax({
                type: 'GET',
                async: true,
                url: url,
                cache: false,
                contentType: "application/json",
                success: function (backData) {
                    console.log(JSON.stringify(backData));
                    $(".score_page .top").removeClass("no_weak_rank");
                    if (backData.flag == "0") {
                        myRank = backData.data.ranking;
                        myName = backData.data.name;
                        mybestScore = backData.data.bestScore;
                        mybestDuration = backData.data.bestDuration;
                        $(".rankword i").html("您目前排名");
                        if (myRank == "0") {
                            $(".score_page .top").removeClass("active");
                            $(".score_page .top .rankword").css("display", "none");
                        } else {
                            $(".score_page .top").addClass("active");
                            $(".score_page .top .rankword").css("display", "block");
                            $(".score_page .top .rankword em").html(myRank);
                            var deadline = getNowTime();
                            $(".rankword .time").html(deadline);


                        }
                    }
                },
                error: function (tt) {
                    debugInfo(tt);
                }
            });
            //如果没哟正确取到我的成绩
            if (myRank == "" || myRank == "0") {
                $(".score_page .top").removeClass("active");
                $(".score_page .top .rankword").css("display", "none");
            }
            getHeroList(top);

        }

        /**
         * 获取英雄榜数据(周排行)
         */
        function getHeroWeekData() {
            //防止总排行有数据灌入,但本周无数据,先清空在调用路由
            var temp = '<li>' +
                '<div class="no_rank">' +
                '<div class="get_sofa">' +
                '</div>' +
                '<div class="sofa_info">' +
                '本周排名暂无人挑战哦，快来抢占第一名吧～' +
                '</div>' +
                '</div>' +
                '<li>';
            if (!$(".scorelist").children().children(".no_rank")[0]) {
                $(".scorelist").empty();
                $(".scorelist").append(temp);
            }
            $(".no_rank").css("display", "block");
            $(".score_page .top").addClass("no_weak_rank");
            $(".rankword").css("display", "none");


            $(".cur_rank").removeClass("cur_rank");
            $(".week_rank").addClass("cur_rank");
            var startTime = getWeekStartTs();
            //获取自己的最好成绩
            var endTime = getWeekEndTs();


            var url = basePath + "exam/find?memberId=2851030" + "&startTime=" + startTime + "&endTime=" + endTime;
            var myRank = "";
            //防止排名banner抖动
            $.ajax({
                type: 'GET',
                async: true,
                url: url,
                cache: false,
                contentType: "application/json",
                success: function (backData) {
                    console.log(JSON.stringify(backData));
                    if (backData.flag == "0") {
                        myRank = backData.data.ranking;
                        myName = backData.data.name;
                        mybestScore = backData.data.bestScore;
                        mybestDuration = backData.data.bestDuration;
                        $(".rankword i").html("您本周排名");
                        if (myRank == "0") {
                            $(".score_page .top").removeClass("active");
                            $(".score_page .top .rankword").css("display", "none");
                        } else {
                            $(".score_page .top").addClass("active");
                            $(".score_page .top").removeClass("no_weak_rank");
                            $(".score_page .top .rankword").css("display", "block");
                            $(".score_page .top .rankword em").html(myRank);
                            var startDate = getWeekStartTime();
                            var endDate = getWeekEndTime();
                            $(".rankword .time").html(startDate + " 至 " + endDate);


                        }
                    } else {
                        //不存在答题记录
                        $(".score_page .top").addClass("no_weak_rank");
                    }
                },
                error: function (tt) {
                    debugInfo(tt);
                }
            });
            //如果没哟正确取到我的成绩
            if (myRank == "" || myRank == "0") {
                $(".score_page .top").removeClass("active");
                $(".score_page .top .rankword").css("display", "none");
            }
            getWeekRank();
        }

        function blockExamPage() {
            //开始计时
            tiktok();
            var route1 = {"route": "#exam"};
            addroute(route1);
            //展示题目
            var paperPage = $(".cur_page").next();
            $(".cur_page").addClass("hidden");
            $(".cur_page").removeClass("cur_page");
            paperPage.addClass("cur_page").removeClass("hidden");
        }

        function getHeroList(top) {
            var url = basePath + "exam/list?number=" + top;
            $.ajax({
                type: 'GET',
                async: true,
                url: url,
                cache: false,
                contentType: "application/json",
                success: function (backData) {
                    console.log(JSON.stringify(backData));
                    $(".score_page .top .rank").removeClass("this_week_rank");
                    $(".score_page .top .rank").html("全员挑战赛前20强");
                    if (backData.flag == "0") {
                        // 清空原来英雄榜数据
                        $(".score_page .scorelist").empty();


                        //已经获得了英雄榜单数据
                        var heroArr = backData.data;
                        for (var i = 0; i < heroArr.length; i++) {
                            var arr = formatMicroTime(heroArr[i].duration);
                            var secDuration = arr[0] + "." + arr[1].substr(0, 2) + " S";
                            var score = heroArr[i].score;
                            var thisDeptName = heroArr[i].deptName;
                            if (!thisDeptName) {
                                thisDeptName = "";
                            }
                            var thisComName = heroArr[i].companyName;
                            if (!thisComName) {
                                thisComName = "";
                            }


                            //渲染当前登录人员最好成绩
                            if (heroArr[i].memberId == '2851030') {
                                var temp_week = $('<li class="scoreitem" >' +
                                    '<div class="ranknum">' +
                                    '<div class="num">' + heroArr[i].order + '</div>' +
                                    '</div>' +
                                    '<div class="info">' +
                                    '<span class="name">' + '您最好成绩' + '</span>' +
                                    '<span class="com">' + '最好成绩用时' + '</span>' +
                                    '<span class="dep">' + heroArr[i].name + '</span>' +
                                    '</div>' +
                                    '<div class="score">' +
                                    score +
                                    '</div>' +
                                    '<div class="time">' +
                                    secDuration +
                                    '</div>' +
                                    '</li>' +
                                    '<div style="text-align:center;border:2px solid #a1a1a1;font-size: 35%;background:#e13c31;width:100%;border-radius:25px;color: white">总排行榜</div>'
                                );

                                if (heroArr[i].order == "1") {
                                    temp_week.find(".ranknum").addClass("h");
                                } else if (heroArr[i].order == "2") {
                                    temp_week.find(".ranknum").addClass("m");
                                } else if (heroArr[i].order == "3") {
                                    temp_week.find(".ranknum").addClass("l");
                                }
                                $(".score_page .scorelist").prepend(temp_week);

                            }

                            var temp = $('<li class="scoreitem">' +
                                '<div class="ranknum">' +
                                '<div class="num">' + heroArr[i].order + '</div>' +
                                '</div>' +
                                '<div class="info">' +
                                '<span class="name">' + heroArr[i].name + '</span>' +
                                '<span class="com">' + thisComName + '</span>' +
                                '<span class="dep">' + thisDeptName + '</span>' +
                                '</div>' +
                                '<div class="score">' +
                                score +
                                '</div>' +
                                '<div class="time">' +
                                secDuration +
                                '</div>' +
                                '</li>');
                            if (heroArr[i].order == "1") {
                                temp.find(".ranknum").addClass("h");
                            } else if (heroArr[i].order == "2") {
                                temp.find(".ranknum").addClass("m");
                            } else if (heroArr[i].order == "3") {
                                temp.find(".ranknum").addClass("l");
                            }

                            $(".score_page .scorelist").append(temp);
                        }

                    }
                },
                error: function (tt) {
                    debugInfo(tt);
                }
            });

        }


        /**
         * 从url中获得参数
         * @param {Object} url
         */
        function getRequest(url) {
            var theRequest = {};
            if (url.indexOf("?") != -1) {
                var str = url.substr(1);
                strs = str.split("&");
                for (var i = 0; i < strs.length; i++) {
                    theRequest[strs[i].split("=")[0]] = unescape(strs[i].split("=")[1]);
                }
            }
            return theRequest;
        }

        /**显示/关闭调试信息
         * @param {Object} msg
         */
        function debugInfo(msg) {
            if (printAlert) {
                alert(msg);
            } else {

            }
        }

        /**
         * 获取一个1到intNum的整数
         * @param {Object} intNum
         */
        function getRanInt(intNum) {
            var ran = Math.random() * intNum;
            var intRes = Math.ceil(ran);
            return intRes;
        }

        /**
         * 隐藏遮罩和图片并将选项重置(答错时)
         */
        function hideModelWrong() {
            $(".model_layer").css("display", "none");
            $(".model_content").css("display", "none");
            $(".answer_res_info").css("display", "none");
            //重置选项
            $(".paper_content .options").css("color", "#666666");
            $(".paper_content .options input[type='radio']").removeAttr("checked");
            $(".paper_content .options label").css("background-image", "url(./assets/img/option_unchecked.png)");
        }

//	var index;
        /**
         * 答对时图层处理(进入到下一题)
         */
        function hideModelWright() {
            //$("#next").append('<img src="/exam/assets/img/next.png"/>');
            answerCount++;
            $(".model_layer").css("display", "none");
            $(".model_content").css("display", "none");
            $(".answer_res_info").css("display", "none");
            //显示下一题
            var nextQuestion = paperArr.splice(0, 1)[0];
            //如果还有题目
            if (nextQuestion) {
                $("#anc").show();
                $("#and").show();
                $(".answer_counts_right i").html(answerCount);
                //$(".title_content").html(answerCount+"."+nextQuestion.title);
                $(".title_content").attr("examtype", nextQuestion.examtype);
                //按试题类型添加标识
                if ($(".title_content").attr("examtype") == 1) {
                    $(".title_content").html("(单选题)" + answerCount + "." + nextQuestion.title);

                    $(".title_content").attr("ans", nextQuestion.answer);
                    $(".title_content").attr("questionId", nextQuestion.id);
                    $(".title_content").attr("productcode", nextQuestion.productcode);
                    $("#a").siblings("span").html("a." + nextQuestion.a);
                    $("#b").siblings("span").html("b." + nextQuestion.b);
                    if (nextQuestion.c == "") {
                        $("#anc").hide();
                    } else {
                        $("#c").siblings("span").html("c." + nextQuestion.c);
                    }
                    if (nextQuestion.d == "") {
                        $("#and").hide();
                    } else {
                        $("#d").siblings("span").html("d." + nextQuestion.d);
                    }

                    //重置选项
                    $(".paper_content .options").css("color", "#666666");
                    $(".paper_content .options input[type='radio']").removeAttr("checked");
                    $(".paper_content .options label").css("background-image", "url(./assets/img/option_unchecked.png)");
                    //$("#next").empty();

                } else if ($(".title_content").attr("examtype") == 3) {

                    $(".title_content").html("(判断题)" + answerCount + "." + nextQuestion.title);
                    //$(".options").children("input").attr('type','radio');

                    $(".title_content").attr("ans", nextQuestion.answer);
                    $(".title_content").attr("questionId", nextQuestion.id);
                    $(".title_content").attr("productcode", nextQuestion.productcode);
                    $("#a").siblings("span").html("a." + nextQuestion.a);
                    $("#b").siblings("span").html("b." + nextQuestion.b);

                    $("#anc").hide();
                    $("#and").hide();
                    //重置选项
                    $(".paper_content .options").css("color", "#666666");
                    $(".paper_content .options input[type='radio']").removeAttr("checked");
                    $(".paper_content .options label").css("background-image", "url(./assets/img/option_unchecked.png)");
                    //$("#next").empty();


                } else {
                    $(".choose_input").hide();
                    $("#a").css('width','0.6rem');
                    $("#a").css('height','0.6rem');
                    $("#b").css('width','0.6rem');
                    $("#b").css('height','0.6rem');
                    $("#c").css('width','0.6rem');
                    $("#c").css('height','0.6rem');
                    $("#d").css('width','0.6rem');
                    $("#d").css('height','0.6rem');



                    $("#next").show();
                    $(".paper_content .options label").css("background-image", "url(./assets/img/option_unchecked.png)");

                    $(".home_opt_new").removeAttr('style');
                    $(".title_content").html("(多选题)" + answerCount + "." + nextQuestion.title);
                    //$(".home_opt_new>div").css('background-image', 'url(/exam/assets/img/jump.png)');
                    $(".options").children("input").attr('type', 'checkbox');
                    $(".options").unbind();
                    $(".options").on('click', function () {
                        if ($(this).children("input").prop('checked') == false) {
                            $(this).children("input").prop('checked', true);
                            $(this).children("label").css("background-image", "url(./assets/img/option_checked.png)");
                        } else {
                            $(this).children("input").prop('checked', false);
                            $(this).children("label").css("background-image", "url(./assets/img/option_unchecked.png)");
                        }
                    });


                    $(".choose").unbind();
                    $(".choose").on('click', function () {
                        if ($(this).prop('checked') == false) {
                            $(this).prop('checked', true);
                        } else {
                            $(this).prop('checked', false);
                        }
                    });







                    $("#anc").show();
                    $("#and").show();

                    $(".title_content").attr("ans", nextQuestion.answer);
                    $(".title_content").attr("questionId", nextQuestion.id);
                    $(".title_content").attr("productcode", nextQuestion.productcode);
                    $("#a").siblings("span").html("a." + nextQuestion.a).attr('value', 'a');
                    $("#b").siblings("span").html("b." + nextQuestion.b).attr('value', 'b');
                    if (nextQuestion.c == "") {
                        $("#anc").hide();
                    } else {
                        $("#c").siblings("span").html("c." + nextQuestion.c);
                    }
                    if (nextQuestion.d == "") {
                        $("#and").hide();
                    } else {
                        $("#d").siblings("span").html("d." + nextQuestion.d);
                    }
                    //添加value以便获取多选值
                    $("#a").attr('value', 'a');
                    $("#b").attr('value', 'b');
                    $("#c").attr('value', 'c');
                    $("#d").attr('value', 'd');
                    //重置选项
                    $(".paper_content .options").css("color", "#666666");
                    $(".paper_content .options input[type='checkbox']").removeAttr("checked");


                }


            }


            //如果是最后一道题跳转到"考试通过界面"
            if (answerCount > totalPapreCounts) {
                clearInterval(timerId);
                debugInfo("已经答完所有题目了");
                memInfo = {
                    "memberId": "2851030",
                    "name": "刘海超",
                    "deptName": "社交与协同办公服务事业部-社交与协同服务后台研发部",
                    "companyName": "用友股份"
                };
                var ts = new Date().getTime();
                //保存成绩
                var memId = getMemid;
                //var memId='zzt';
                var name = getName;
                var deptName = getDeptName;
                //保存成绩时增加公司字段
                var companyName = getCompanyName;

                //var memId=$(".answer_counts_left").attr('memberId');
                //var name=$(".answer_counts_left").attr('name');
                //var deptName= $(".answer_counts_left").attr('deptName');
                //var companyName= $(".answer_counts_left").attr('companyName');
                //var memId="82715";
                //var name="郑梓涛";
                //var deptName= "123";
                //var companyName= "用友网络科技股份有限公司";
                //alert(memId+"/"+name+"/"+companyName);


                //userdTime=3788;
                //ts=1527215849324;
                var duration = userdTime * 10;
                console.log(duration);
                var url = basePath + "exam/save";

                //成绩汇总
                for (var i = 0, n; n = examStore[i]; i++) {
                    allSum += n.store;
                    examIdAndStore += n.id + ":" + n.store + ",";
                }
                if(allSum>100){
                    allSum=100
                }
                var sumArray = [];
                sumArray['memId'] = memId;
                sumArray['allSum'] = allSum;

                examStore.push(sumArray);
                //console.log(examStore);


                var obj = {
                    "memberId": memId,
                    "name": name,
                    "companyName": companyName,
                    "deptName": deptName,
                    "duration": duration,
                    "ts": ts,
                    "examIdAndStore": examIdAndStore,
                    "score": allSum
                };

                /*md5加密校验*/
                var md5String = $.md5("" + memId + duration + ts);
                //var md5String = "16f0b8ed54ee8d3f9372b7aa14efb452";
                document.cookie = "d8d93k1udfn8kdm=" + md5String;

                //handle
                if (duration <= 10 * 1000) {
                    var func1 = function (YonYouJSBridge) {
                        function quit() {
                            var data = '{"function":"closePage"}';
                            YonYouJSBridge.send(data, function (responseData) {
                            });
                        }
                        quit();
                    };
                    connectWebViewJavascriptBridge(func1);
                    return false;
                }

                $.ajax({
                    type: 'POST',
                    async: true,
                    url: url,
                    cache: false,
                    data: JSON.stringify(obj),
                    contentType: "application/json",
                    success: function (backData) {
                        debugInfo(JSON.stringify(backData));
                        console.log(JSON.stringify(backData));
                        if (backData.flag == "0") {
                            $('.this_scores').removeClass('hidden');
                            setHeader(false);
                            var route = {"route": "#score"};
                            addroute(route);

                            //显示考试通过成绩页
                            var paperPassPage = $(".exam_pass_page");
                            $(".cur_page").addClass("hidden");
                            $(".cur_page").removeClass("cur_page");
                            paperPassPage.addClass("cur_page").removeClass("hidden");

                            //根据返回的数据是否有历史成绩来判断是否是第一次考试
                            var usefulData = backData.data;
                            thisLeftTime = formatMicroTime(usefulData.curDuration)[0];
                            thisRightTime = formatMicroTime(usefulData.curDuration)[1];
                            thisCurrent2 = usefulData.curPercent;
                            if (usefulData.bestDuration) {
                                thisLeftTime_best1 = formatMicroTime(usefulData.bestDuration)[0];
                                thisRightTim_best1 = formatMicroTime(usefulData.bestDuration)[1];
                            } else {
                                thisLeftTime_best1 = thisLeftTime;
                                thisRightTim_best1 = thisRightTime;
                                thisCurrent = usefulData.curPercent;
                            }
                            $(".share_score_div input").eq(0).val(thisLeftTime_best1);
                            $(".share_score_div input").eq(1).val(thisRightTim_best1);
                            $(".share_rank_div span i").html(usefulData.bestPercent);
                            $('.share_score_wrap span').text(memInfo.name);
                            $(".this_cost_time input").eq(0).val(thisLeftTime);
                            $(".this_cost_time input").eq(1).val(thisRightTime);
                            $(".this_victory span i").html(usefulData.curPercent);
                            $("#thisSocre input").val(usefulData.curScore);
                            if (!usefulData.bestDuration || !usefulData.bestPercent) {
                                //首次考试
                                $(".best_scores").css("display", "none");

                            } else {
                                //非首次考试
                                $(".best_scores").css("display", "block");
                                bestLeftTime = formatMicroTime(usefulData.bestDuration)[0];
                                bestRightTime = formatMicroTime(usefulData.bestDuration)[1];
                                bestPercent = usefulData.bestPercent;
                                bestStore = usefulData.bestStore;
                                $(".best_scores span i").eq(0).html(bestStore);
                                $(".best_scores span i").eq(1).html(bestPercent);
                            }
                            //解决考试完后显示异常周排行和总排行
                            $(".score_page").hide();


                            //恢复默认选项(重置题号)
                            answerCount = 1;
                            $("#next").hide();
                            $(".options").children("input").attr('type', 'radio');
                            //去除多选题绑定的事件,重新绑定事件
                            $(".options").unbind();
                            $(".options").off("click").on("click", function (e) {
                                //e.preventDefault();
                                //e.stopPropagation();
                                $(this).siblings(".options").children("input").removeAttr("checked");
                                $(this).children("input").attr("checked", "checked");

                                if ($(this).children("label").attr("for") == $(".title_content").attr("ans")) {
                                    //答对
//			var randomInt = getRanInt(5);
                                    var randomInt = "new";
                                    $(this).css("color", "#3fb31b");
                                    $(".model_layer").css("display", "block");
                                    $(".model_content").css("display", "block");
                                    $(".answer_pic").css("background-image", "url(./assets/img/wright/wright_" + randomInt + ".jpg)");
                                    $(".answer_res_info").css("display", "block");
                                    $(".answer_res_info").html("恭喜你,答对了!");
                                    $(".answer_res_info").css("background-color", "#DD4B4B");

                                    $(this).children("label").css("background-image", "url(./assets/img/answer_wright.png)");
                                    //$("#next").empty();
                                    setTimeout(hideModelWright, 1000);

                                    //考试成绩进入数组
                                    var rightStore = 5;
                                    var rightQuestionId = $(".title_content").attr("questionId");
                                    var rightProductCode = $(".title_content").attr("ProductCode");
                                    var right = [];
                                    right['id'] = rightQuestionId;
                                    right['productcode'] = rightProductCode;
                                    right['store'] = rightStore;

                                    examStore.push(right);

                                    //console.log(examStore);


                                } else {
                                    //答错
                                    var randomInt = "new";
                                    $(this).css("color", "#DD4B4B");
                                    $(".model_layer").css("display", "block");
                                    $(".model_content").css("display", "block");
                                    $(".answer_pic").css("background-image", "url(./assets/img/wrong/wrong_" + randomInt + ".jpg)");
                                    $(".answer_res_info").css("display", "block");
                                    $(".answer_res_info").html("再接再厉哦!");
                                    $(".answer_res_info").css("background-color", "#FAAB00");
                                    $(this).children("label").css("background-image", "url(./assets/img/answer_wrong.png)");
                                    alert("正确答案是:" + $(".title_content").attr("ans"));
                                    //$("#next").empty();
                                    setTimeout(hideModelWright, 1000);


                                    //考试成绩进入数组
                                    var wrongStore = 0;
                                    var wrongQuestionId = $(".title_content").attr("questionId");
                                    var wrongProductCode = $(".title_content").attr("ProductCode");
                                    var wrong = [];
                                    wrong['id'] = wrongQuestionId;
                                    wrong['productcode'] = wrongProductCode;
                                    wrong['store'] = wrongStore;

                                    examStore.push(wrong);

                                    //console.log(examStore);
                                }
                            });


                            $(".paper_content .options").css("color", "#666666");
                            $(".paper_content .options input[type='radio']").removeAttr("checked");
                            $(".paper_content .options label").css("background-image", "url(./assets/img/option_unchecked.png)");

                            examStore = [];
                            allSum = 0;
                            examIdAndStore = "";
                            //答完题后将用户信息存储于本地
                            saveToLocal();
                        }

                    },
                    error: function (tt) {
                        debugInfo(tt);
                    }
                });


            }

        }

        /**
         * 离开弹出框组件
         * @param {Object} block_hide
         * @param {Object} info  确定要放弃答题?...
         */
        function showQuitModel(block_hide, info) {
            if (block_hide == "block") {
                var str = $('<div class="quit_layer"></div>' +
                    '<div class="quit_content">' +
                    '<span class="quit_close"></span>' +
                    '<span class="quit_info">' + info + '</span>' +
                    '<div class="quit_confirm_cancel">' +
                    '<span class="confirm_quit"></span>' +
                    '<span class="cancel_quit"></span>' +
                    '</div>' +
                    '</div>');
                $(str[0]).css("display", block_hide);
                $(str[1]).css("display", block_hide);
                $("#main").append(str);
            } else {
                $(".quit_layer").css("display", block_hide);
                $(".quit_content").css("display", block_hide);
            }
        }


        function setHeader(isHome, title) {
            // body...
            if (isHome) {
                $('.header .back').addClass('hidden');
                $('.header .close').removeClass('hidden');
            } else {
                $('.header .close').addClass('hidden');
                $('.header .back').removeClass('hidden');
            }

        }

//页面路由
        var route = {"route": "#home"};
        addroute(route);

        function addroute(param) {
            history.pushState(param, "", param.route)
        }

        function Navroute(param) {
            history.replaceState(param, "", param.route);
        }

        window.onpopstate = function (event) {
            var state = event.state;
            console.log(state.route);
            $(".header .htitle").text("全员挑战赛");
//	if(!$(".score_page").hasClass("hidden")){
//		alert(state.route)
//	}
            switch (state.route) {
                case "#home":
                    if ($(".home_page").hasClass("cur_page")) {
                        closePageView();
                    }
                    $(".cur_page").addClass('hidden');
                    $(".cur_page").prev().removeClass('hidden');
                    if (countTimer) {
                        clearInterval(countTimer);
                    }
                    $('.cur_page').removeClass('cur_page');
                    $(".home_page").addClass("cur_page");
                    $(".home_page").removeClass('hidden').addClass('cur_page');
                    $('.exam_page').addClass('hidden');
                    $('.exam_pass_page').addClass('hidden');
                    $('.page_turning_eff').css('display', 'none');
                    setHeader(true);
                    handleQuitDiv();
                    break;
                case "#count":
                    if (!$(".exam_page").hasClass("hidden")) {
                        showQuitModel("block", "确定要放弃答题?");
                    } else if ($(".home_page").hasClass("cur_page")) {
                        closePageView();
                    }
                    break;
                case "#exam":
                    if ($(".home_page").hasClass("cur_page")) {
                        closePageView();
                    } else if ($(".score_page").hasClass("cur_page")) {
                        $(".cur_page").addClass('hidden');
                        $(".cur_page").removeClass('hidden');
                        $(".home_page").addClass("cur_page");
                        $(".home_page").removeClass("hidden");
                        setHeader(true);
                        break;
                    }

                    $(".cur_page").addClass('hidden');
                    $(".cur_page").prev().addClass('hidden');
                    $(".cur_page").prev().prev().addClass('hidden');
                    $(".cur_page").prev().prev().prev().removeClass('hidden');
                    $('.cur_page').removeClass('cur_page');
                    $(".home_page").addClass("cur_page");
                    setHeader(true);
                    break;
                case "#score":
                    if ($(".home_page").hasClass("cur_page")) {
                        closePageView();
                    }
                    $(".cur_page").addClass('hidden');
                    $('.cur_page').removeClass('cur_page');
                    $(".exam_pass_page").removeClass('hidden');
                    $(".exam_pass_page").addClass('cur_page');
                    setHeader(false);
                    break;
                case "#scores":
                    if ($(".home_page").hasClass("cur_page")) {
                        closePageView();
                    }
                    $(".cur_page").addClass('hidden');
                    $(".cur_page").prev().addClass('hidden');
                    $(".cur_page").prev().prev().addClass('hidden');
                    $(".cur_page").prev().prev().prev().addClass('hidden');
                    $(".cur_page").prev().removeClass('hidden');
                    setHeader(true);
                    break;
                default:
                    if ($(".home_page").hasClass("cur_page")) {
                        closePageView();
                    }
                    break;

            }

        };

        /**
         * 获取当前截止时间
         */
        function getNowTime() {
            var date = new Date();
            var strBlank = date.getFullYear() + "年" + (date.getMonth() + 1) + "月" + date.getDate() + "日";
            var hourTime = date.getHours();
            if (hourTime < 10) {
                hourTime = "0" + hourTime;
            }
            var minTime = date.getMinutes();
            if (minTime < 10) {
                minTime = "0" + minTime;
            }
            var secTime = date.getSeconds();
            if (secTime < 10) {
                secTime = "0" + secTime;
            }
            var afterBlank = hourTime + ":" + minTime + ":" + secTime;
            return "截止至: " + strBlank + " " + afterBlank;
        }

        /**
         * 格式化毫秒时间(着重处理1000和100的整数倍)
         * @param {Object} microTime
         */
        function formatMicroTime(microTime) {
            var duration = "";
            var intTime = parseInt(microTime);
            if (!((intTime / 1000) % 1)) {
                duration = intTime / 1000.00 + '.00';
            } else if (!((intTime / 100) % 1)) {
                duration = intTime / 1000.00 + '0';
            } else {
                duration = intTime / 1000.00 + '';
            }
            var arr = duration.split(".");
            return arr;
        }

        function payward() {
            window.location.href = localBasePath + "lottery/index.html?memberId="
                + memInfo.memberId + "&name=" + memInfo.name + "&deptName=" + memInfo.deptName
                + "&companyName=" + memInfo.companyName;
        }

        var now = new Date(); //当前日期
        nowDayOfWeek = now.getDay(); //今天本周的第几天
        if (nowDayOfWeek == 0) {
            nowDayOfWeek = 7;//将周日当做每周的第7天
        }
        nowYear = now.getFullYear(); //当前年
        nowMonth = now.getMonth(); //月
        nowDay = now.getDate(); //日

        function formatDate(date) { //格局化日期：yyyy-MM-dd
            var myyear = date.getFullYear();
            var mymonth = date.getMonth() + 1;
            var myweekday = date.getDate();
            if (mymonth < 10) {
                mymonth = "0" + mymonth;
            }
            if (myweekday < 10) {
                myweekday = "0" + myweekday;
            }
            return (myyear + "-" + mymonth + "-" + myweekday);
        }

        function getWeekStartTs() { //获得本周的开端日期(暂且把周一当做一周的开始) 周一的00:00:00
            var weekStartDate = new Date(nowYear, nowMonth, nowDay - nowDayOfWeek + 1);
            return Date.parse(weekStartDate);
        }

        function getWeekEndTs() { //获得本周的停止日期  下个周一的00:00:00
            var weekEndDate = new Date(nowYear, nowMonth, nowDay + (6 - nowDayOfWeek + 1 + 1));
            return Date.parse(weekEndDate);
        }

        function getWeekStartTime() { //获得本周的开端日期(暂且把周一当做一周的开始) 周一的00:00:00
            var weekStartDate = new Date(nowYear, nowMonth, nowDay - nowDayOfWeek + 1);
            return formatDate(weekStartDate);
        }

        function getWeekEndTime() { //获得本周的停止日期  下个周日的24:00:00
            var weekEndDate = new Date(nowYear, nowMonth, nowDay + (6 - nowDayOfWeek + 1));
            return formatDate(weekEndDate);
        }

        function getWeekCurTs() { //获得当前日期时间戳
            var curDate = new Date(nowYear, nowMonth, nowDay);
            return Date.parse(curDate);
        }

        function getWeekCurTime() { //获得当前日期
            var curDate = new Date(nowYear, nowMonth, nowDay);
            return formatDate(curDate);
        }


        function getWeekRank() {
            var startTime = getWeekStartTs();
            var endTime = getWeekEndTs();
            var url = basePath + "exam/list?number=20&startTime=" + startTime + "&endTime=" + endTime;
            $.ajax({
                type: 'GET',
                async: true,
                url: url,
                cache: false,
                contentType: "application/json",
                success: function (backData) {
                    console.log(JSON.stringify(backData.data));
                    $(".score_page .top .rank").addClass("this_week_rank");
                    $(".score_page .top .rank").html("本周全员挑战赛前20强");
                    if (backData.flag == "0") {
                        //已经获得了英雄榜单数据
                        var heroArr = backData.data;
                        if (!heroArr.length) {
                            var temp = '<li>' +
                                '<div class="no_rank">' +
                                '<div class="get_sofa">' +
                                '</div>' +
                                '<div class="sofa_info">' +
                                '本周排名暂无人挑战哦，快来抢占第一名吧～' +
                                '</div>' +
                                '</div>' +
                                '<li>';
                            if (!$(".scorelist").children().children(".no_rank")[0]) {
                                $(".scorelist").empty();
                                $(".scorelist").append(temp);
                            }
                            $(".no_rank").css("display", "block");
                            $(".score_page .top").addClass("no_weak_rank");
                            $(".rankword").css("display", "none");

                        } else {
                            $(".no_rank").css("display", "none");
                            // 清空原来英雄榜数据
                            $(".score_page .scorelist").empty();
                            for (var i = 0; i < heroArr.length; i++) {
                                var arr = formatMicroTime(heroArr[i].duration);
                                var secDuration = arr[0] + "." + arr[1].substr(0, 2) + " S";
                                var score = heroArr[i].score;
                                var thisDeptName = heroArr[i].deptName;
                                if (!thisDeptName) {
                                    thisDeptName = "";
                                }
                                var thisComName = heroArr[i].companyName;
                                if (!thisComName) {
                                    thisComName = "";
                                }


                                if (heroArr[i].memberId == '2851030') {


                                    //var arr_week = formatMicroTime(mybestDuration_week);
                                    //var secDuration_week = arr_week[0] + "." + arr_week[1].substr(0, 2) + " S";
                                    var temp_week = $('<li class="scoreitem" >' +
                                        '<div class="ranknum">' +
                                        '<div class="num">' + heroArr[i].order + '</div>' +
                                        '</div>' +
                                        '<div class="info">' +
                                        '<span class="name">' + '您最好成绩' + '</span>' +
                                        '<span class="com">' + '最好成绩用时' + '</span>' +
                                        '<span class="dep">' + heroArr[i].name + '</span>' +
                                        '</div>' +
                                        '<div class="score">' +
                                        score +
                                        '</div>' +
                                        '<div class="time">' +
                                        secDuration +
                                        '</div>' +
                                        '</li>' +
                                        '<div style="text-align:center;border:2px solid #a1a1a1;font-size: 35%;background:#e13c31;width:100%;border-radius:25px;color: white">本周排行榜</div>'
                                    );

                                    if (heroArr[i].order == "1") {
                                        temp_week.find(".ranknum").addClass("h");
                                    } else if (heroArr[i].order == "2") {
                                        temp_week.find(".ranknum").addClass("m");
                                    } else if (heroArr[i].order == "3") {
                                        temp_week.find(".ranknum").addClass("l");
                                    }
                                    $(".score_page .scorelist").prepend(temp_week);

                                }


                                var temp = $('<li class="scoreitem">' +
                                    '<div class="ranknum">' +
                                    '<div class="num">' + heroArr[i].order + '</div>' +
                                    '</div>' +
                                    '<div class="info">' +
                                    '<span class="name">' + heroArr[i].name + '</span>' +
                                    '<span class="com">' + thisComName + '</span>' +
                                    '<span class="dep">' + thisDeptName + '</span>' +
                                    '</div>' +
                                    '<div class="score">' +
                                    score +
                                    '</div>' +
                                    '<div class="time">' +
                                    secDuration +
                                    '</div>' +
                                    '</li>');
                                if (heroArr[i].order == "1") {
                                    temp.find(".ranknum").addClass("h");
                                } else if (heroArr[i].order == "2") {
                                    temp.find(".ranknum").addClass("m");
                                } else if (heroArr[i].order == "3") {
                                    temp.find(".ranknum").addClass("l");
                                }

                                $(".score_page .scorelist").append(temp);
                            }
                        }

                    } else {
                        $(".no_rank").css("display", "block");
                    }
                },
                error: function (tt) {
                    debugInfo(tt);
                }
            });
        }

        function saveToLocal() {
            var oldCode = getRequest(location.search)["code"];
            var localObj = {
                "memInfo": memInfo, "thisLeftTime": thisLeftTime,
                "thisRightTime": thisRightTime, "thisCurrent2": thisCurrent2,
                "bestLeftTime": bestLeftTime, "bestRightTime": bestRightTime,
                "bestPercent": bestPercent, "code": oldCode
            };

            var localObjStr = JSON.stringify(localObj);
            localStorage.setItem("localObjStr", localObjStr);
        }

//处理由于设备点击物理按键,返回至答题首页时残留的quite框
        function handleQuitDiv() {
            if (($(".quit_content").css("display") == "block") && ($(".quit_content").css("display") == "block")) {
                $(".quit_layer").remove();
                $(".quit_content").remove();
                $(".count_down_page").addClass("hidden");
            }
        }

//展示活动截止弹出框
        function showDeadLineBlock() {
            $("#main .dead_layer").remove();

            var deadLayer = $("<div class='dead_layer'></div>");
            $("#main").append(deadLayer);
            $(".dead_layer").css("display", "block");
            var deadDiv = $('<div class="dead_block"></div>');
            deadLayer.append(deadDiv);
            $(".dead_block").css("display", "block");
            var continueExam = $("<span class='continueExam'></span>");
            $(".dead_block").append(continueExam);
        }

//隐藏活动截至公告谈层
        function hiddenDeadBlock() {
            //去掉遮罩层
            $(".dead_layer").css("display", "none");
            $(".dead_block").css("display", "none");
        }

        function closePageView() {
            var func1 = function (YonYouJSBridge) {
                function quit() {
                    var data = '{"function":"closePage"}';
                    YonYouJSBridge.send(data, function (responseData) {
                    });
                }
                quit();
            };
            connectWebViewJavascriptBridge(func1);
        }

//选择领域
        $("#btn_ly").on("click", function () {
            var lyVal = $("#ly").val();
            alert(lyVal);
        });

//选择产品
        $("#btn_cp").on("click", function () {
            var cpVal = $("#cp").val();
            alert(cpVal);
        });

//下一题
        $("#next").on("click", function (e) {
            if ($(".title_content").attr("examtype") == 2) {
                var store = "";
                $('input[name="option"]:checked').each(function () {
                    store += $(this).val();
                });
                if (store != '') {
                    if (store == $(".title_content").attr("ans")) {
                        var randomInt = "new";

                        //$(".options").css("color", "#3fb31b");
                        $(".model_layer").css("display", "block");
                        $(".model_content").css("display", "block");
                        $(".answer_pic").css("background-image", "url(./assets/img/wright/wright_" + randomInt + ".jpg)");
                        $(".answer_res_info").css("display", "block");
                        $(".answer_res_info").html("恭喜你,答对了!");
                        $(".answer_res_info").css("background-color", "#DD4B4B");

                        //$(".options").children("label").css("background-image", "url(./assets/img/answer_wright.png)");


                        //$("#next").empty();
                        setTimeout(hideModelWright, 1000);


                        //考试成绩进入数组
                        var rightStore_many = 5;
                        var rightQuestionId_many = $(".title_content").attr("questionId");
                        var rightProductCode_many = $(".title_content").attr("ProductCode");
                        var right_many = [];
                        right_many['id'] = rightQuestionId_many;
                        right_many['productcode'] = rightProductCode_many;
                        right_many['store'] = rightStore_many;

                        examStore.push(right_many);

                        //(examStore);
                    } else {
                        var randomInt = "new";
                        //$(".options").css("color", "#DD4B4B");
                        $(".model_layer").css("display", "block");
                        $(".model_content").css("display", "block");
                        $(".answer_pic").css("background-image", "url(./assets/img/wrong/wrong_" + randomInt + ".jpg)");
                        $(".answer_res_info").css("display", "block");
                        $(".answer_res_info").html("再接再厉哦!");
                        $(".answer_res_info").css("background-color", "#FAAB00");
                        $(".options").children("label").css("background-image", "url(./assets/img/answer_wrong.png)");

                        alert("正确答案是:" + $(".title_content").attr("ans"));
                        //$("#next").empty();
                        setTimeout(hideModelWright, 1000);


                        //考试成绩进入数组
                        var wrongStore_many = 0;
                        var wrongQuestionId_many = $(".title_content").attr("questionId");
                        var wrongProductCode_many = $(".title_content").attr("ProductCode");
                        var wrong_many = [];
                        wrong_many['id'] = wrongQuestionId_many;
                        wrong_many['productcode'] = wrongProductCode_many;
                        wrong_many['store'] = wrongStore_many;

                        examStore.push(wrong_many);

                        //console.log(examStore);


                    }
                    $(".options").children("label").removeAttr('style');
                } else {
                    alert('此题为多选题,必须选择一个选项');
                }

            }
        });

//跳过此题
        $("#jump").on("click", function () {
            alert("success_jump")
        });


        // $(".start_new").on('click', function () {
        //     alert('success');
        // });



//第二页签---提升历程
        //绑定提升历程事件
        $(".rank_new").on('click', function () {
            //切换页头
            setHeader(false);
            var route1 = {"route": "#scores"};
            addroute(route1);
            //获取提升历程
            getHeroWeekData_new();
            var heroPage = $(".score_page_up");
            $(".cur_page").addClass("hidden");
            $(".cur_page").removeClass("cur_page");
            heroPage.addClass("cur_page").removeClass("hidden");
        });


        /*能力提升趋势*/
        $(".week_rank_up").on("click", getHeroWeekData_new);

        /*能力提升明细*/
        $(".total_rank_up").on("click", getHeroData_new);


        //获取提升历程趋势
        function getHeroWeekData_new() {
            //防止总排行有数据灌入,但本周无数据,先清空在调用路由
            var temp = '<li>' +
                '<div class="no_rank_up">' +
                    '<div class="get_sofa_up">' +
                     '</div>' +
                     '<div class="sofa_info_up">' +
                            '本周排名暂无人挑战哦，快来抢占第一名吧～' +
                     '</div>' +
                     '<div style="width: 100%;height: 50%">' +
                           '<div id="forms_times" style="width: 100%;height:100%"></div>' +
                     '</div>' +
                     '<div style="width: 100%;height: 50%">' +
                            '<div id="forms_score" style="width: 100%;height:100%"></div>' +
                     '</div>' +
                '<li>';
            if (!$(".scorelist_up").children().children(".no_rank_up")[0]) {
                $(".scorelist_up").empty();
                $(".scorelist_up").append(temp);
            }
            $(".no_rank_up").css("display", "block");
            $(".score_page_up .top_up").addClass("no_weak_rank_up");
            $(".rankword_up").css("display", "none");

            $(".cur_rank_up").removeClass("cur_rank_up");
            $(".week_rank_up").addClass("cur_rank_up");


            $(".get_sofa_up").hide();
            $(".sofa_info_up").hide();

            $("#main_new").show();

            //获取次数趋势分析数据
            var url = basePath + "exam/numberofday?memberId=2851030";
            $.ajax({
                type: 'GET',
                async: true,
                url: url,
                cache: false,
                contentType: "application/json",
                success: function (backData) {
                    debugInfo(JSON.stringify(backData));
                    console.log(JSON.stringify(backData));
                    if (backData.flag == "0") {
                        var usefulData = backData.data;
                        //var examofDay=usefulData.examofDay;
                        //var number=usefulData.number;
                        var examofDay_array = [];
                        var number_array = [];
                        for (var i = 0; i < usefulData.length; i++) {
                            var nowday = usefulData[i]['examofDay'];
                            var activerCount = usefulData[i]['number'];
                            examofDay_array.push(nowday);
                            number_array.push(activerCount);

                        }
                        // 基于准备好的dom，初始化echarts实例
                        var myChart = echarts.init(document.getElementById('forms_times'));

                        var option = {
                            backgroundColor: '#394056',
                            title: {
                                //top:'',
                                text: '考试次数趋势分析',
                                textStyle: {
                                    fontWeight: 'normal',
                                    fontSize: '25',
                                    color: '#F1F1F3'
                                },
                                left: 'center'
                            },
                            tooltip: {
                                trigger: 'axis', //触发类型。[ default: 'item' ] :数据项图形触发，主要在散点图，饼图等无类目轴的图表中使用;'axis'坐标轴触发，主要在柱状图，折线图等会使用类目轴的图表中使用
                                axisPointer: {
                                    lineStyle: {
                                        color: '#57617B'
                                    }
                                }
                            },
                            legend: {
                                icon: 'rect', //设置图例的图形形状，circle为圆，rect为矩形
                                itemWidth: 14, //图例标记的图形宽度[ default: 25 ]
                                itemHeight: 5, //图例标记的图形高度。[ default: 14 ]
                                itemGap: 13, //图例每项之间的间隔。横向布局时为水平间隔，纵向布局时为纵向间隔。[ default: 10 ]
                                data: ['考试次数'],
                                top: '5%',
                                right: '4%', //图例组件离容器右侧的距离
                                textStyle: {
                                    fontSize: 12,
                                    color: '#F1F1F3'
                                }
                            },
                            grid: {
                                left: '5%', //grid 组件离容器左侧的距离。
                                right: '5%', //grid 组件离容器右侧的距离。
                                bottom: '3%', //grid 组件离容器下侧的距离。
                                top: '13%',
                                containLabel: true //grid 区域是否包含坐标轴的刻度标签[ default: false ]
                            },
                            xAxis: [{
                                type: 'category',
                                boundaryGap: false, //坐标轴两边留白策略，类目轴和非类目轴的设置和表现不一样
                                axisLine: {
                                    lineStyle: {
                                        color: '#57617B' //坐标轴线线的颜色。
                                    }
                                },
                                data: examofDay_array
                            }],
                            yAxis: [{
                                type: 'value', //坐标轴类型。'value' 数值轴，适用于连续数据;'category' 类目轴，适用于离散的类目数据，为该类型时必须通过 data 设置类目数据;'time' 时间轴;'log' 对数轴.
                                name: '次数', //坐标轴名称。
                                min: 0,
                                //max: 100,
                                axisTick: {
                                    show: false //是否显示坐标轴刻度
                                },
                                axisLine: {
                                    lineStyle: {
                                        color: '#57617B' //坐标轴线线的颜色
                                    }
                                },
                                axisLabel: {
                                    margin: 10, //刻度标签与轴线之间的距离
                                    textStyle: {
                                        fontSize: 17 //文字的字体大小
                                    }
                                },
                                splitLine: {
                                    lineStyle: {
                                        color: '#57617B' //分隔线颜色设置
                                    }
                                }
                            },
                            ],
                            series: [{
                                name: '考试次数', //系列名称，用于tooltip的显示，legend 的图例筛选，在 setOption 更新数据和配置项时用于指定对应的系列
                                type: 'line',
                                smooth: true, //是否平滑曲线显示
                                symbol: 'circle', //标记的图形。ECharts 提供的标记类型包括 'circle', 'rect', 'roundRect', 'triangle', 'diamond', 'pin', 'arrow'
                                symbolSize: 5, //标记的大小，可以设置成诸如 10 这样单一的数字，也可以用数组分开表示宽和高，例如 [20, 10] 表示标记宽为20，高为10[ default: 4 ]
                                showSymbol: false, //是否显示 symbol, 如果 false 则只有在 tooltip hover 的时候显示
                                lineStyle: { //线条样式
                                    normal: {
                                        width: 1 //线宽。[ default: 2 ]
                                    }
                                },
                                areaStyle: { //区域填充样式
                                    normal: {
                                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{ //填充的颜色。
                                            offset: 0, // 0% 处的颜色
                                            color: 'rgba(137, 189, 27, 0.3)'
                                        }, {
                                            offset: 0.8, // 80% 处的颜色
                                            color: 'rgba(137, 189, 27, 0)'
                                        }], false),
                                        shadowColor: 'rgba(0, 0, 0, 0.1)', //阴影颜色。支持的格式同color
                                        shadowBlur: 10 //图形阴影的模糊大小。该属性配合 shadowColor,shadowOffsetX, shadowOffsetY 一起设置图形的阴影效果
                                    }
                                },
                                itemStyle: { //折线拐点标志的样式
                                    normal: {
                                        color: 'rgb(137,189,27)',
                                        borderColor: 'rgba(137,189,2,0.27)', //图形的描边颜色。支持的格式同 color
                                        borderWidth: 12 //描边线宽。为 0 时无描边。[ default: 0 ]

                                    }
                                },
                                data: number_array
                            }]
                        };

                        // 使用刚指定的配置项和数据显示图表。
                        myChart.setOption(option);

                        $("#forms_times").css('width','100%');
                        $("#forms_times").css('height','100%');
                        $("#forms_score").css('width','100%');
                        $("#forms_score").css('height','100%');


                    }
                }
            });


            //获取分数趋势分析数据
            var url_store = basePath + "exam/promote?memberId=2851030";
            $.ajax({
                type: 'GET',
                async: true,
                url: url_store,
                cache: false,
                contentType: "application/json",
                success: function (backData) {
                    debugInfo(JSON.stringify(backData));
                    console.log(JSON.stringify(backData));
                    if (backData.flag == "0") {
                        var usefulData_store = backData.data;
                        //var examofDay=usefulData.examofDay;
                        //var number=usefulData.number;
                        var examofDay_array_store = [];
                        var number_array_store = [];
                        for (var i = 0; i < usefulData_store.length; i++) {
                            var nowday_store = usefulData_store[i]['updateTime'];
                            var activerCount_store = usefulData_store[i]['score'];
                            examofDay_array_store.push(nowday_store);
                            number_array_store.push(activerCount_store);
                        }
                        // 基于准备好的dom，初始化echarts实例
                        var myChart_stroe = echarts.init(document.getElementById('forms_score'));
                        var thisWeekday=examofDay_array_store[examofDay_array_store.length-3];

                        var option_store = {
                            backgroundColor: '#050e2b',
                            title: {
                                //top:'',
                                text: '考试分数趋势分析',
                                textStyle: {
                                    fontWeight: 'normal',
                                    fontSize: '25',
                                    color: '#F1F1F3'
                                },
                                left: 'center'
                            },
                            tooltip: {
                                trigger: 'axis', //触发类型。[ default: 'item' ] :数据项图形触发，主要在散点图，饼图等无类目轴的图表中使用;'axis'坐标轴触发，主要在柱状图，折线图等会使用类目轴的图表中使用
                                axisPointer: {
                                    lineStyle: {
                                        color: '#57617B'
                                    }
                                }
                            },
                            legend: {
                                icon: 'rect', //设置图例的图形形状，circle为圆，rect为矩形
                                itemWidth: 14, //图例标记的图形宽度[ default: 25 ]
                                itemHeight: 5, //图例标记的图形高度。[ default: 14 ]
                                itemGap: 13, //图例每项之间的间隔。横向布局时为水平间隔，纵向布局时为纵向间隔。[ default: 10 ]
                                data: ['考试次数'],
                                top: '5%',
                                right: '4%', //图例组件离容器右侧的距离
                                textStyle: {
                                    fontSize: 12,
                                    color: '#F1F1F3'
                                }
                            },
                            grid: {
                                left: '5%', //grid 组件离容器左侧的距离。
                                right: '5%', //grid 组件离容器右侧的距离。
                                bottom: '3%', //grid 组件离容器下侧的距离。
                                top: '13%',
                                containLabel: true //grid 区域是否包含坐标轴的刻度标签[ default: false ]
                            },
                            xAxis: [{
                                type: 'category',
                                boundaryGap: false, //坐标轴两边留白策略，类目轴和非类目轴的设置和表现不一样
                                axisLine: {
                                    lineStyle: {
                                        color: '#57617B' //坐标轴线线的颜色。
                                    }
                                },
                                data: examofDay_array_store
                            }],
                            yAxis: [{
                                type: 'value', //坐标轴类型。'value' 数值轴，适用于连续数据;'category' 类目轴，适用于离散的类目数据，为该类型时必须通过 data 设置类目数据;'time' 时间轴;'log' 对数轴.
                                name: '次数', //坐标轴名称。
                                min: 0,
                                //max: 100,
                                axisTick: {
                                    show: false //是否显示坐标轴刻度
                                },
                                axisLine: {
                                    lineStyle: {
                                        color: '#57617B' //坐标轴线线的颜色
                                    }
                                },
                                axisLabel: {
                                    margin: 10, //刻度标签与轴线之间的距离
                                    textStyle: {
                                        fontSize: 17 //文字的字体大小
                                    }
                                },
                                splitLine: {
                                    lineStyle: {
                                        color: '#57617B' //分隔线颜色设置
                                    }
                                }
                            },
                            ],
                            series: [{
                                name: '考试次数', //系列名称，用于tooltip的显示，legend 的图例筛选，在 setOption 更新数据和配置项时用于指定对应的系列
                                type: 'line',
                                smooth: true, //是否平滑曲线显示
                                symbol: 'circle', //标记的图形。ECharts 提供的标记类型包括 'circle', 'rect', 'roundRect', 'triangle', 'diamond', 'pin', 'arrow'
                                symbolSize: 5, //标记的大小，可以设置成诸如 10 这样单一的数字，也可以用数组分开表示宽和高，例如 [20, 10] 表示标记宽为20，高为10[ default: 4 ]
                                showSymbol: false, //是否显示 symbol, 如果 false 则只有在 tooltip hover 的时候显示
                                lineStyle: { //线条样式
                                    normal: {
                                        width: 1 //线宽。[ default: 2 ]
                                    }
                                },
                                areaStyle: { //区域填充样式
                                    normal: {
                                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{ //填充的颜色。
                                            offset: 0, // 0% 处的颜色
                                            color: 'rgba(137, 189, 27, 0.3)'
                                        }, {
                                            offset: 0.8, // 80% 处的颜色
                                            color: 'rgba(137, 189, 27, 0)'
                                        }], false),
                                        shadowColor: 'rgba(0, 0, 0, 0.1)', //阴影颜色。支持的格式同color
                                        shadowBlur: 10 //图形阴影的模糊大小。该属性配合 shadowColor,shadowOffsetX, shadowOffsetY 一起设置图形的阴影效果
                                    }
                                },
                                itemStyle: { //折线拐点标志的样式
                                    normal: {
                                        color: 'rgb(137,189,27)',
                                        borderColor: 'rgba(137,189,2,0.27)', //图形的描边颜色。支持的格式同 color
                                        borderWidth: 12 //描边线宽。为 0 时无描边。[ default: 0 ]

                                    }
                                },
                                data: number_array_store
                            }]
                        };

                        // 使用刚指定的配置项和数据显示图表。
                        myChart_stroe.setOption(option_store);


                        $("#forms_score > div:nth-child(2)").css('font-size','26');


                    }
                }
            });




            //领域及产品考试详情
            var forms_product =
                '<li>' +
                    '<div style="width: 100%;">' +
                        '<div id="forms_times" style="width: 100%;">' +
                            '<h6 align="center">领域及产品考试统计表</h6>'+
                            '<table  cellspacing="1" cellpadding="8" style="width: 100%;border: solid;font-size: 30%" >' +
                                '<thead>' +
                                '<tr>' +
                                    '<th style="width: 20%;">领域</th>' +
                                    '<th style="width: 60%;">产品</th>' +
                                    '<th style="width: 10%;">次数</th>' +
                                    '<th style="width: 10%;">分数</th>' +
                                '</tr>' +
                                '</thead>' +
                                '<tbody id="field_times">' +
                                '</tbody>' +
                            '</table>' +
                        '</div>' +
                    '</div>' +
                '<li>';

            $(".scorelist_up").append(forms_product);


            var product_url = basePath + "exam/getExamProduct?memberId=2851030";
            $.ajax({
                type: 'GET',
                async: true,
                url: product_url,
                cache: false,
                contentType: "application/json",
                success: function (backData) {
                    console.log(JSON.stringify(backData));
                    if (backData.flag == "0") {
                        //已经获得了英雄榜单数据
                        var productArr = backData.data;


                        for (var i = 0; i < productArr.length; i++) {
                            var chrMenu_Name=productArr[i].chrMenu_Name;
                            var productName=productArr[i].productName;
                            var examCount=productArr[i].examCount;
                            var maxScore=productArr[i].maxScore;

                            var model_times=
                                '<tr>' +
                                '<td >'+chrMenu_Name+'</td>' +
                                '<td >'+productName+'</td>' +
                                '<td >'+examCount+'</td>' +
                                '<td >'+maxScore+'</td>' +
                                '</tr>';

                            $(model_times).appendTo($("#field_times"));

                        }
                    }
                },
                error: function (tt) {
                    debugInfo(tt);
                }
            });








        }


        //获取提升历程明细
        function getHeroData_new() {
            $(".cur_rank_up").removeClass("cur_rank_up");
            $(".total_rank_up").addClass("cur_rank_up");
            $(".no_rank_up").css("display", "none");//总榜肯定有数据
            var top = 20;
            //获取自己的最好成绩
            var url = basePath + "exam/find?memberId=2851030";
            var myRank = "";
            $.ajax({
                type: 'GET',
                async: true,
                url: url,
                cache: false,
                contentType: "application/json",
                success: function (backData) {
                    console.log(JSON.stringify(backData));
                    $(".score_page_up .top_up").removeClass("no_weak_rank");
                    if (backData.flag == "0") {
                        myRank = backData.data.ranking;
                        myName = backData.data.name;
                        mybestScore = backData.data.bestScore;
                        mybestDuration = backData.data.bestDuration;
                        $(".rankword_up i").html("您所有考试明细");
                        if (myRank == "0") {
                            $(".score_page_up .top_up").removeClass("active");
                            $(".score_page_up .top_up .rankword_up").css("display", "none");
                        } else {
                            $(".score_page_up .top_up").addClass("active");
                            $(".score_page_up .top_up .rankword_up").css("display", "block");
                            //$(".score_page_up .top_up .rankword_up em").html(myRank);
                            var deadline = getNowTime();
                            $(".rankword_up .time_up").html(deadline);
                        }
                    }
                },
                error: function (tt) {
                    debugInfo(tt);
                }
            });
            //如果没哟正确取到我的成绩
            if (myRank == "" || myRank == "0") {
                $(".score_page_up .top_up").removeClass("active");
                $(".score_page_up .top_up .rankword_up").css("display", "none");
            }
            getHeroList_new(top);

        }

        function getHeroList_new(top) {
            var url = basePath + "exam/promote?memberId=2851030";
            $.ajax({
                type: 'GET',
                async: true,
                url: url,
                cache: false,
                contentType: "application/json",
                success: function (backData) {
                    console.log(JSON.stringify(backData));
                    $(".score_page_up .top_up .rank").removeClass("this_week_rank_up");
                    $(".score_page_up .top_up .rankv").html("全员挑战赛前20强");
                    if (backData.flag == "0") {
                        // 清空原来英雄榜数据
                        $(".score_page_up .scorelist_up").empty();
                        //已经获得了英雄榜单数据
                        var heroArr = backData.data;

                        var examNum=1;

                        for (var i = 0; i < heroArr.length; i++) {
                            var arr = formatMicroTime(heroArr[i].duration);
                            var secDuration = arr[0] + "." + arr[1].substr(0, 2) + " S";
                            var score = heroArr[i].score;
                            var thisDeptName = heroArr[i].deptName;
                            var updateTime=heroArr[i].updateTime;
                            var examids=heroArr[i].examids;
                            var errorids=heroArr[i].errorids;
                            var answerdate=heroArr[i].answer;
                            examNum_new=examNum++;
                            if (!thisDeptName) {
                                thisDeptName = "";
                            }
                            var thisComName = heroArr[i].companyName;
                            if (!thisComName) {
                                thisComName = "";
                            }
                            var temp = $('<li class="scoreitem"' + ' ' + 'examids=' + examids + ' ' + 'errorids=' + errorids +' ' + 'answer=' + answerdate + ">" +
                                '<div class="ranknum">' +
                                '<div class="num">' + examNum_new + '</div>' +
                                '</div>' +
                                '<div class="info">' +
                                '<span class="name">' + '考试得分' + '</span>' +
                                '<span class="com">' + '考试用时' + '</span>' +
                                '<span class="dep" >' + '考试时间' + '</span>' +
                                '</div>' +
                                '<div class="score">' +
                                score +
                                '</div>' +
                                '<div class="time">' +
                                secDuration +
                                '</div>' +
                                '<div class="updateTime">' +
                                updateTime +
                                '</div>' +
                                '</li>');

                            $(".score_page_up .scorelist_up").append(temp);

                            //点击条目返回当前考试详情
                            $(".scoreitem").off('click').on('click', function (e) {
                                e.preventDefault();
                                e.stopPropagation();
                                var examid=$(this).attr('examids');
                                var errorid=$(this).attr('errorids');
                                //var answer_data=$(this).attr('answer');
                                var urlGetExam = basePath + "exam/examdetial?ids="+examid;
                                $.ajax({
                                    type: 'GET',
                                    async: true,
                                    url: urlGetExam,
                                    cache: false,
                                    contentType: "application/json",
                                    success: function (backData) {
                                        console.log(JSON.stringify(backData));
                                        if (backData.flag == "0") {
                                            //已经获得了英雄榜单数据
                                            var heroArr = backData.data;

                                            layer.open({
                                                title: [
                                                    '考试明细',
                                                    'background-color:#8DCE16; color:#fff;'
                                                ],
                                                area: ['70%'],
                                                content: '<table border="" cellspacing="1" cellpadding="8" style="width: 100%">' +
                                                '<tbody id="mm" >' +
                                                '<tr style="height: 30%;width: 100%;">' +
                                                '<th style="width: 6%;">题号</th>' +
                                                '<th style="width: 40%;">试题(字体标红为考试时选错)</th>' +
                                                '<th style="width: 40%;">正确答案</th>' +
                                                '</tr>' +
                                                '<tr>' +
                                                '</tr>' +
                                                '</tbody>' +
                                                '</table>',
                                                btn: ['关闭'],
                                            });
                                            $(".layui-m-layerchild").css('max-width', "100%");
                                            $(".layui-m-layerchild h3").css('background-color','#eb4849');
                                            $(".layui-m-layerchild").css('overflow','auto');
                                            $(".layui-m-layerchild").css('height','1600px');

                                            var examid_split=examid.split(';');
                                            var errorid_split=errorid.split(';');
                                            var index='';
                                            var num=1;

                                            for (var i = 0; i < heroArr.length; i++) {
                                                var title=heroArr[i].title;
                                                var answer=heroArr[i].answer;
                                                var answerData='';
                                                var answer_ifright=heroArr[i].id;
                                                num_new=num++;
                                                if(answer=='a'){
                                                    answerData='a.'+heroArr[i].a
                                                }else if(answer=='b'){
                                                    answerData='b'+heroArr[i].b
                                                }else if(answer=='c'){
                                                    answerData='c'+heroArr[i].c
                                                }else if(answer=='d'){
                                                    answerData='d'+heroArr[i].d
                                                }else if(answer=='ab'){
                                                    answerData='a.'+heroArr[i].a+"<br>"+'b.'+heroArr[i].b
                                                }else if(answer=='ac'){
                                                    answerData='a.'+heroArr[i].a+"<br>"+'c.'+heroArr[i].c
                                                }else if(answer=='ad'){
                                                    answerData='a.'+heroArr[i].a+"<br>"+'d.'+heroArr[i].d
                                                }else if(answer=='bc'){
                                                    answerData='b.'+heroArr[i].b+"<br>"+'c.'+heroArr[i].c
                                                }else if(answer=='bd'){
                                                    answerData='b.'+heroArr[i].b+"<br>"+'d.'+heroArr[i].d
                                                }else if(answer=='cd'){
                                                    answerData='c.'+heroArr[i].c+"<br>"+'d.'+heroArr[i].d
                                                }else if(answer=='abc'){
                                                    answerData='a.'+heroArr[i].a+"<br>"+'b.'+heroArr[i].b+'<br>'+'c.'+heroArr[i].c
                                                }else if(answer=='abd'){
                                                    answerData='a.'+heroArr[i].a+"<br>"+'b.'+heroArr[i].b+'<br>'+'d.'+heroArr[i].d
                                                }else if(answer=='acd'){
                                                    answerData='a.'+heroArr[i].a+"<br>"+'c.'+heroArr[i].c+"<br>"+'d.'+heroArr[i].d
                                                }else if(answer=='bcd'){
                                                    answerData='b.'+heroArr[i].b+"<br>"+'c.'+heroArr[i].c+"<br>"+'d.'+heroArr[i].d
                                                }else if(answer=='abcd'){
                                                    answerData='a.'+heroArr[i].a+"<br>"+'b.'+heroArr[i].b+'<br>'+'c.'+heroArr[i].c+"<br>"+'d.'+heroArr[i].d
                                                }

                                                var answer_ifright_str=answer_ifright.toString();
                                                var index=$.inArray(answer_ifright_str,errorid_split);
                                                if(index >=0){
                                                    var model =
                                                        '<tr style="line-height:100px;">' +
                                                        '<td>' + num_new + '</td>' +
                                                        '<td class="ifRight" style="color: red" >' + title + '</td>' +
                                                    '<td>' + answerData + '</td>' +
                                                    '</tr>';

                                                }else{
                                                    model =
                                                        '<tr style="line-height:100px;">' +
                                                        '<td>' + num_new + '</td>' +
                                                        '<td class="ifRight" style="color: #3FB31B" >' + title + '</td>' +
                                                    '<td>' + answerData + '</td>' +
                                                    '</tr>';
                                                }

                                                $(model).appendTo($("#mm"));
                                                $("#mm tr").css("text-align","left");
                                                $("#mm").find("tr:first-child").css("text-align","center");
                                                $("#mm tr").css('font-size','30px');

                                            }
                                        }
                                    }
                                });

                            });

                        }

                    }
                },
                error: function (tt) {
                    debugInfo(tt);
                }
            });
        }

    }
);
