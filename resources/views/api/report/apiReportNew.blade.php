<!doctype html public "-//w3c//dtd html 4.0 transitional//en">
<html>
<head><title>报表</title>
    <meta content="text/html; charset=utf-8" http-equiv=content-type>
    <link
            rel=stylesheet href="{{url('/newimport/ns_report.css')}}">
    <link rel=stylesheet
          href="{{url('/newimport/ns_report_rsas.css')}}">
    <link rel=stylesheet
          href="{{url('/newimport/wdatepicker.css')}}">
    <script src="{{url('/newimport/jquery.js')}}"></script>

    <script src="{{url('/newimport/common.js')}}"></script>

    <script src="{{url('/newimport/wdatepicker.js')}}"></script>

    <link href="{{url('/css/bootstrap/bootstrap.min.css')}}">
{{--rel="stylesheet" type="text/css" />--}}
{{--<link rel="stylesheet" href="{{url('css/classical/common.css')}}">--}}
{{--<link rel="stylesheet" href="{{url('css/classical/index.css')}}">--}}

<!-- jquery-1.8.2.min.js -->
    <script type="text/javascript" src="{{url('/javascript/jquery/jquery-2.1.1.min.js')}}"></script>
    <script src="{{url('javascript/plugins/bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{url('/javascript/common.js')}}"></script>
    <script src="{{url('assets/js/echarts.js')}}"></script>
    {{--<script--}}
    {{--src="{{url('javascript/plugins/echarts/echarts.common.min.js')}}"></script>--}}
</head>
<body>
<div id=report class=wrapper_w800>
    <div class=report_tip></div>
    <div id=head class=report_title>
        <h1>api接口测试邮件报告</h1><span class=note>&nbsp;</span></div><!--head end,catalog start-->
    <div id=catalog>
        <div class=report_h1>目录</div>
    </div>
    <div id=content  >
        <div class="report_h report_h1">1.总体报告分析(点击可折叠)</div>
            <div class=report_content>
                <div id="webTaskPie_all" style="width: 100%; height: 500px;"></div>
            </div>
        <div class="report_h report_h1">2.任务报告分析(点击可折叠)</div>
            <div class=report_content>
                <div id=title00 class="report_h report_h2">2.1任务信息</div>
                <div>
                    @foreach($data['task_list'] as $task)
                    <table class="report_table plumb">
                        <tbody>
                        <tr class=odd>
                            <th style="vertical-align: middle" width=120>任务执行结果</th>
                            <td style="padding-bottom: 6px; padding-left: 6px; padding-right: 6px; padding-top: 6px">
                                @if($task->state=='PASS')
                                <img  align=absmiddle src="{{url('/newimport/pass.png')}}"/>
                                <span style="color: #60e767" class="level_danger middle">{{$task->state}}</span></td>
                                @elseif($task->state=='ERROR')
                                    <img  align=absmiddle src="{{url('/newimport/error')}}"/>
                                    <span style="color: #df6967" class="level_danger middle">{{$task->state}}</span></td>
                                @endif

                        </tr>
                        </tbody>
                    </table>
                    <table width="100%">
                        <tbody>
                        <tr>
                            <td valign=top width="50%">
                                <table class="report_table plumb">
                                    <tbody>
                                    <tr class=odd>
                                        <th width=120>任务名称</th>
                                        <td>扫描【http://upcat.yonyou.com】</td>
                                    </tr>
                                    <tr class=even>
                                        <th>任务类型</th>
                                        <td>API接口测试</td>
                                    </tr>
                                    <tr class=odd>
                                        <th width=120>任务状态</th>
                                        <td>执行完成</td>
                                    </tr>
                                    <tr class=even>
                                        <th>所属项目</th>
                                        <td>{{$task->chrProjectName}}</td>
                                    </tr>
                                    <tr class=odd>
                                        <th>下达任务用户</th>
                                        <td>{{$task->chrUserName}}</td>
                                    </tr>
                                    <tr class=even>
                                        <th>任务数据来源</th>
                                        <td>UPCAT</td>
                                    </tr>
                                    <tr class=odd>
                                        <th>任务说明</th>
                                        <td>自动执行</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td width=20></td>
                            <td valign=top width="50%">
                                <table class="report_table plumb">
                                    <tbody>
                                    <tr class=odd>
                                        <th width=120>时间统计</th>
                                        <td>开始：{{$task->updated_at}}<br>结束：{{$task->exectime}}</td>
                                    </tr>
                                    <tr class=even>
                                        <th>任务统计</th>
                                        <td>任务ID：{{$task->id}}<br>运行ID：{{$task->taskExecID}}<br>项目ID：{{$task->projectId}}<br>项目名称：{{$task->chrProjectName}}</td>
                                    </tr>
                                    <tr class=odd>
                                        <th>系统版本信息</th>
                                        <td>v6.0r02f01sp03</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    @endforeach
                </div>
                <div id=title01 class="report_h report_h2">2.2执行报告</div>
                <div>
                    <div class="report_h report_h3">2.2.1执行报告分析图</div>
                    <div class=center>
                        {{--<img src="{{url('/newimport/f312c596d01c9a0f0ecfbd0afbfa674d.png')}}"/>--}}
                        <div id="webTaskPie_task" style="width: 100%; height: 400px;"></div>
                        </div>
                </div>
            </div>
        <div class="report_h report_h1">3.案例报告分析(点击可折叠)</div>
            <div class=report_content>
                <div id=title03 class="report_h report_h2">3.1案例信息</div>
                <div>
                    @foreach($data['scheme_list'] as $scheme)
                        <table class="report_table plumb">
                            <tbody>
                            <tr class=odd>
                                <th style="vertical-align: middle" width=120>案例执行结果</th>
                                <td style="padding-bottom: 6px; padding-left: 6px; padding-right: 6px; padding-top: 6px">
                                    @if($scheme->state=='PASS')
                                        <img  align=absmiddle src="{{url('/newimport/pass.png')}}"/>
                                        <span style="color: #60e767" class="level_danger middle">{{$scheme->state}}</span></td>
                                @elseif($scheme->state=='ERROR')
                                    <img  align=absmiddle src="{{url('/newimport/error')}}"/>
                                    <span style="color: #df6967" class="level_danger middle">{{$scheme->state}}</span></td>
                                @endif

                            </tr>
                            </tbody>
                        </table>
                        <table width="100%">
                            <tbody>
                            <tr>
                                <td valign=top width="50%">
                                    <table class="report_table plumb">
                                        <tbody>
                                        <tr class=odd>
                                            <th width=120>任务名称</th>
                                            <td>{{$scheme->chrTaskName}}</td>
                                        </tr>
                                        <tr class=even>
                                            <th>案例名称</th>
                                            <td>{{$scheme->schemeName}}</td>
                                        </tr>
                                        <tr class=odd>
                                            <th width=120>案例状态</th>
                                            <td>执行完成</td>
                                        </tr>
                                        <tr class=even>
                                            <th>所属项目</th>
                                            <td>{{$scheme->projectName}}</td>
                                        </tr>
                                        <tr class=odd>
                                            <th>下达案例用户</th>
                                            <td>{{$scheme->createUser}}</td>
                                        </tr>
                                        <tr class=even>
                                            <th>案例数据来源</th>
                                            <td>UPCAT</td>
                                        </tr>
                                        <tr class=odd>
                                            <th>案例说明</th>
                                            <td>自动执行</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td width=20></td>
                                <td valign=top width="50%">
                                    <table class="report_table plumb">
                                        <tbody>
                                        <tr class=odd>
                                            <th width=120>时间统计</th>
                                            <td>开始：{{$scheme->updated_at}}<br>结束：{{$scheme->exectime}}</td>
                                        </tr>
                                        <tr class=even>
                                            <th>案例统计</th>
                                            <td>案例ID：{{$scheme->id}}<br>运行ID：{{$scheme->taskExecID}}<br>项目ID：{{$scheme->projectId}}<br>项目名称：{{$scheme->projectName}}</td>
                                        </tr>
                                        <tr class=odd>
                                            <th>系统版本信息</th>
                                            <td>v6.0r02f01sp03</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    @endforeach
                </div>
                <div id=title04 class="report_h report_h2">3.2执行报告</div>
                <div>
                    <div class="report_h report_h3">3.2.执行报告分析图</div>
                    <div class=center>
                        {{--<img src="{{url('/newimport/f312c596d01c9a0f0ecfbd0afbfa674d.png')}}"/>--}}
                        <div id="webTaskPie_scheme" style="width: 100%; height: 400px;"></div>
                    </div>
                </div>
            </div>
        <div class="report_h report_h1">4.脚本报告分析(点击可折叠)</div>
            <div class=report_content>
                <div id=title06 class="report_h report_h2">4.1脚本信息</div>
                <div>
                    <table id=vuln_distribution class=report_table>
                        <thead>
                        <tr class=second_title>
                            {{--<th style="width: 40px">序号</th>--}}
                            <th>所属案例</th>
                            <th style="width: 60px">脚本名称</th>
                            <th style="width: 40px">执行结果</th>
                            {{--<th style="width: 30px">备注</th>--}}
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($data['script_info'] as $script)
                                @if(($script->id)%2==0)
                            <tr style="cursor: pointer" class="odd vuln_low" onclick="no_toggle('{{$script->id}}','table_{{$script->id}}')">
                            {{--<td>{{$script->id}}</td>--}}
                            <td>
                                <img id='{{$script->id}}' class="ico plus" />
                                <img align=absmiddle src="{{url('/newimport/vuln_low.gif')}}"/>
                                <span style="color: #737373">
                                    {{$script->schemeName}}
                                </span>
                            </td>
                                <td>{{$script->script}}</td>
                                <td>{{$script->state}}</td>
                                {{--<td>1</td>--}}
                            </tr>
                            <tr id="table_{{$script->id}}" class="more hide odd">
                            <th></th>
                            <td style="padding-left: 20px" class=extend colspan=4>
                                <table style="white-space: pre-wrap" class=report_table width="100%">
                                    <tbody>
                                    <tr class=odd>
                                        <th width="20%">受影响主机</th>
                                        <td width="80%">121.43.122.79;&nbsp;</td>
                                    </tr>
                                    <tr class=even>
                                        <th>详细描述</th>
                                        <td>openssh是一种开放源码的ssh协议的实现，初始版本用于openbsd平台，现在已经被移植到多种unix/linux类操作系统下。

                                            如果配置为cbc模式的话，openssh没有正确地处理分组密码算法加密的ssh会话中所出现的错误，导致可能泄露密文中任意块最多32位纯文本。在以标准配置使用openssh时，攻击者恢复32位纯文本的成功概率为2^{-18}，此外另一种攻击变种恢复14位纯文本的成功概率为2^{-14}。
                                        </td>
                                    </tr>
                                    <tr class=odd>
                                        <th>解决办法</th>
                                        <td>临时解决方法：

                                            * 在ssh会话中仅使用ctr模式加密算法，如aes-ctr。

                                            厂商补丁：

                                            openssh
                                            -------
                                            目前厂商已经发布了升级补丁以修复这个安全问题，请到厂商的主页下载：

                                            https://downloads.ssh.com/


                                            对于具体linux发行版中使用的版本，可以参考如下链接，确认系统是否受该漏洞影响:
                                            redhat
                                            -------
                                            https://rhn.redhat.com/errata/rhsa-2009-1287.html

                                            suse
                                            -------
                                            http://support.novell.com/security/cve/cve-2008-5161.html

                                            ubuntu
                                            -------
                                            http://people.canonical.com/~ubuntu-security/cve/2008/cve-2008-5161.html
                                        </td>
                                    </tr>
                                    <tr class=even>
                                        <th>威胁分值</th>
                                        <td>2.6</td>
                                    </tr>
                                    <tr class=odd>
                                        <th>危险插件</th>
                                        <td>否</td>
                                    </tr>
                                    <tr class=even>
                                        <th>发现日期</th>
                                        <td>2008-11-19</td>
                                    </tr>
                                    <tr class=odd>
                                        <th>cve编号</th>
                                        <td><a href="http://cve.mitre.org/cgi-bin/cvename.cgi?name=cve-2008-5161"
                                               target=_blank>cve-2008-5161</a></td>
                                    </tr>
                                    <tr class=even>
                                        <th>bugtraq</th>
                                        <td><a href="http://www.securityfocus.com/bid/32319" target=_blank>32319</a>
                                        </td>
                                    </tr>
                                    <tr class=odd>
                                        <th>nsfocus</th>
                                        <td><a href="http://www.nsfocus.net/vulndb/12630" target=_blank>12630</a></td>
                                    </tr>
                                    <tr class=even>
                                        <th>cnnvd编号</th>
                                        <td>
                                            <a href="http://www.cnnvd.org.cn/vulnerability/show/cv_cnnvdid/cnnvd-200811-321"
                                               target=_blank>cnnvd-200811-321</a></td>
                                    </tr>
                                    <tr class=odd>
                                        <th>cncve编号</th>
                                        <td>cncve-20085161</td>
                                    </tr>
                                    <tr class=even>
                                        <th>cvss评分</th>
                                        <td>2.6</td>
                                    </tr>
                                    <tr class=odd>
                                        <th>cnvd编号</th>
                                        <td><a href="http://www.cnvd.org.cn/flaw/show/cnvd-2009-12630" target=_blank>cnvd-2009-12630</a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                                @else
                                    <tr style="cursor: pointer" class="even vuln_low" onclick="no_toggle('{{$script->id}}','table_{{$script->id}}')">
                                        {{--<td>{{$script->id}}</td>--}}
                                        <td>
                                            <img id='{{$script->id}}' class="ico plus" src="{{url('/newimport/blank.gif')}}"/>
                                            <img align=absmiddle src="{{url('/newimport/vuln_low.gif')}}"/>
                                            <span style="color: #737373">
                                    {{$script->schemeName}}
                                </span>
                                        </td>
                                        <td>{{$script->script}}</td>
                                        <td>{{$script->state}}</td>
                                        {{--<td>1</td>--}}
                                    </tr>
                                    <tr id="table_{{$script->id}}" class="more hide odd">
                                        <th></th>
                                        <td style="padding-left: 20px" class=extend colspan=4>
                                            <table style="white-space: pre-wrap" class=report_table width="100%">
                                                <tbody>
                                                <tr class=odd>
                                                    <th width="20%">受影响主机</th>
                                                    <td width="80%">121.43.122.79;&nbsp;</td>
                                                </tr>
                                                <tr class=even>
                                                    <th>详细描述</th>
                                                    <td>openssh是一种开放源码的ssh协议的实现，初始版本用于openbsd平台，现在已经被移植到多种unix/linux类操作系统下。

                                                        如果配置为cbc模式的话，openssh没有正确地处理分组密码算法加密的ssh会话中所出现的错误，导致可能泄露密文中任意块最多32位纯文本。在以标准配置使用openssh时，攻击者恢复32位纯文本的成功概率为2^{-18}，此外另一种攻击变种恢复14位纯文本的成功概率为2^{-14}。
                                                    </td>
                                                </tr>
                                                <tr class=odd>
                                                    <th>解决办法</th>
                                                    <td>临时解决方法：

                                                        * 在ssh会话中仅使用ctr模式加密算法，如aes-ctr。

                                                        厂商补丁：

                                                        openssh
                                                        -------
                                                        目前厂商已经发布了升级补丁以修复这个安全问题，请到厂商的主页下载：

                                                        https://downloads.ssh.com/


                                                        对于具体linux发行版中使用的版本，可以参考如下链接，确认系统是否受该漏洞影响:
                                                        redhat
                                                        -------
                                                        https://rhn.redhat.com/errata/rhsa-2009-1287.html

                                                        suse
                                                        -------
                                                        http://support.novell.com/security/cve/cve-2008-5161.html

                                                        ubuntu
                                                        -------
                                                        http://people.canonical.com/~ubuntu-security/cve/2008/cve-2008-5161.html
                                                    </td>
                                                </tr>
                                                <tr class=even>
                                                    <th>威胁分值</th>
                                                    <td>2.6</td>
                                                </tr>
                                                <tr class=odd>
                                                    <th>危险插件</th>
                                                    <td>否</td>
                                                </tr>
                                                <tr class=even>
                                                    <th>发现日期</th>
                                                    <td>2008-11-19</td>
                                                </tr>
                                                <tr class=odd>
                                                    <th>cve编号</th>
                                                    <td><a href="http://cve.mitre.org/cgi-bin/cvename.cgi?name=cve-2008-5161"
                                                           target=_blank>cve-2008-5161</a></td>
                                                </tr>
                                                <tr class=even>
                                                    <th>bugtraq</th>
                                                    <td><a href="http://www.securityfocus.com/bid/32319" target=_blank>32319</a>
                                                    </td>
                                                </tr>
                                                <tr class=odd>
                                                    <th>nsfocus</th>
                                                    <td><a href="http://www.nsfocus.net/vulndb/12630" target=_blank>12630</a></td>
                                                </tr>
                                                <tr class=even>
                                                    <th>cnnvd编号</th>
                                                    <td>
                                                        <a href="http://www.cnnvd.org.cn/vulnerability/show/cv_cnnvdid/cnnvd-200811-321"
                                                           target=_blank>cnnvd-200811-321</a></td>
                                                </tr>
                                                <tr class=odd>
                                                    <th>cncve编号</th>
                                                    <td>cncve-20085161</td>
                                                </tr>
                                                <tr class=even>
                                                    <th>cvss评分</th>
                                                    <td>2.6</td>
                                                </tr>
                                                <tr class=odd>
                                                    <th>cnvd编号</th>
                                                    <td><a href="http://www.cnvd.org.cn/flaw/show/cnvd-2009-12630" target=_blank>cnvd-2009-12630</a>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                            @endif
                            @endforeach
                        </tbody>
                        <tfoot>
                        {{--<tr class=first_title>--}}
                            {{--<td colspan=4>合计</td>--}}
                            {{--<td>{{$script->count}}</td>--}}
                        {{--</tr>--}}
                        </tfoot>
                    </table>


                </div>
                <div id=title07 class="report_h report_h2">4.2执行报告</div>
                <div>
                    {{--<div class="report_h report_h3">4.2.1主机风险分布</div>--}}
                    <div class=center>
                        {{--<img src="{{url('/newimport/f312c596d01c9a0f0ecfbd0afbfa674d.png')}}"/>--}}
                        <div id="webTaskPie_script" style="width: 100%; height: 400px;"></div>
                    </div>
                </div>
            </div>
        <div class="report_h report_h1">5.历次任务执行成功率分析(点击可折叠)</div>
            <div class=report_content>
                <div id="webTaskPie_allTaskResult" style="width: 100%; height: 500px;"></div>
            </div>
        <div class="report_h report_h1">6.历次任务执行次数趋势(点击可折叠)</div>
            <div class=report_content>
                <div id="webTaskPie_allTaskTimes" style="width: 100%; height: 500px;"></div>
        </div>
        <div class="report_h report_h1">7.历次案例执行结果趋势(点击可折叠)</div>
            <div class=report_content>
                <div id="webTaskPie_allSchemeResult" style="width: 100%; height: 500px;"></div>
            </div>
        <div class="report_h report_h1">8.历次案例执行次数趋势(点击可折叠)</div>
            <div class=report_content>
                <div id="webTaskPie_allSchemeTimes" style="width: 100%; height: 500px;"></div>
            </div>
    </div>
    <div class=report_tip></div>
</div><!--content end-->
<div></div>
</body>
<script>
    //获取邮件中URL中的$apitaskexecid
    function getQueryString(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var r = window.location.search.substr(1).match(reg);
        if (r != null) return unescape(r[2]);
        return null;
    }
    var taskid=getQueryString('taskid');
    //alert('taskid'+taskid);
    var taskexecid=getQueryString('taskexecid');
    //alert('taskexecid'+taskexecid);

    var lgData=new Array();
    lgData['PASS']=3;
    lgData['Error']=5;
    lgData['TRUE']=2;
    lgData['FAIL']=6;


    //总体饼状图ajax调用
    var datAll={
        "taskId":taskid,
        "taskexecid":taskexecid
    };
    CommonUtil.requestService("/getAllReportInfo", datAll, true, "get", function (response, status) {
        if (response.success) {
            var task = response.data.task;
            task = CommonUtil.parseToJson(task);
            var allTask=[];
            var allTaskCount=0;
            for (var idx in task) {
                if(task[idx].name=='PASS'){
                    allTask['PASS']=task[idx].count;
                    allTaskCount=allTaskCount+task[idx].count;
                }else if(task[idx].name=='ERROR'){
                    allTask['ERROR']=task[idx].count;
                    allTaskCount=allTaskCount+task[idx].count;
                }else if(task[idx].name=='FAIL'){
                    allTask['FAIL']=task[idx].count;
                    allTaskCount=allTaskCount+task[idx].count;
                }else if(task[idx].name=='TRUE'){
                    allTask['TRUE']=task[idx].count;
                    allTaskCount=allTaskCount+task[idx].count;
                }

            }

            var scheme = response.data.scheme;
            scheme = CommonUtil.parseToJson(scheme);
            var allScheme=[];
            var allSchemeCount=0;
            for (var idx in scheme) {
                if(scheme[idx].name=='PASS'){
                    allScheme['PASS']=scheme[idx].count;
                    allSchemeCount=allSchemeCount+scheme[idx].count;
                }else if(scheme[idx].name=='ERROR'){
                    allScheme['ERROR']=scheme[idx].count;
                    allSchemeCount=allSchemeCount+scheme[idx].count;
                }else if(scheme[idx].name=='FAIL'){
                    allScheme['FAIL']=scheme[idx].count;
                    allSchemeCount=allSchemeCount+scheme[idx].count;
                }else if(scheme[idx].name=='TRUE'){
                    allScheme['TRUE']=scheme[idx].count;
                    allSchemeCount=allSchemeCount+scheme[idx].count;
                }
            }
            var script = response.data.script;
            script = CommonUtil.parseToJson(script);
            var allScript=[];
            var allScriptCount=0;
            for (var idx in script) {
                if(script[idx].name=='PASS'){
                    allScript['PASS']=script[idx].count;
                    allScriptCount=allScriptCount+script[idx].count;
                }else if(script[idx].name=='ERROR'){
                    allScript['ERROR']=script[idx].count;
                    allScriptCount=allScriptCount+script[idx].count;
                }else if(script[idx].name=='FAIL'){
                    allScript['FAIL']=script[idx].count;
                    allScriptCount=allScriptCount+script[idx].count;
                }else if(script[idx].name=='TRUE'){
                    allScript['TRUE']=script[idx].count;
                    allScriptCount=allScriptCount+script[idx].count;
                }
            }
            // 总体饼状图
            var myChart_all = echarts.init(document.getElementById('webTaskPie_all'));
            // 指定图表的配置项和数据
            var yiwub = [{
                name: "PASS",
                value: allTask['PASS']
            },
                {
                    name: "ERROR",
                    value: allTask['ERROR']
                },
                {
                    name: "TRUE",
                    value: allTask['TRUE']
                },
                {
                    name: "FAIL",
                    value: allTask['FAIL']
                }
            ];
            var shig = [{
                name: "PASS",
                value: allScheme['PASS']
            },
                {
                    name: "ERROR",
                    value: allScheme['ERROR']
                },
                {
                    name: "TRUE",
                    value: allScheme['TRUE']
                },
                {
                    name: "FAIL",
                    value: allScheme['FAIL']
                }
            ];

            var ganb = [{
                name: "PASS",
                value: allScript['PASS']
            },
                {
                    name: "ERROR",
                    value: allScript['ERROR']
                },
                {
                    name: "TRUE",
                    value: allScript['TRUE']
                }, {
                    name: "FAIL",
                    value: allScript['FAIL']
                }
            ];

            var jx_legend = [];
            var ywb_t = allTaskCount,
                shig_t = allSchemeCount,
                ganb_t = allScriptCount;
            for (var i = 0; i < yiwub.length; i++) {
                jx_legend.push(yiwub[i].name)
                //ywb_t = ywb_t
            }
            for (var i = 0; i < shig.length; i++) {
                jx_legend.push(shig[i].name)
                //shig_t = ywb_t + shig[i].value
            }
            for (var i = 0; i < ganb.length; i++) {
                jx_legend.push(ganb[i].name)
                //ganb_t = ywb_t + ganb[i].value
            }
            var jx_color = ['#86D560', '#AF89D6', '#59ADF3', '#FF999A', '#FFCC67'];
            var option_all = {
                backgroundColor: "rgb(2,66,126)",
                title: [{
                    text: '任务',
                    subtext: ywb_t + '个',
                    x: '15%',
                    y: 'center',
                    textStyle: {
                        fontWeight: 'normal',
                        fontSize: 16,
                        color: "white"
                    }
                }, {
                    text: '案例',
                    subtext: shig_t + '个',
                    x: 'center',
                    y: 'center',
                    textStyle: {
                        fontWeight: 'normal',
                        fontSize: 16,
                        color: "white"
                    }
                }, {
                    text: '脚本',
                    subtext: ganb_t + '个',
                    x: '82%',
                    y: 'center',
                    textStyle: {
                        fontWeight: 'normal',
                        fontSize: 16,
                        color: "white"
                    }
                }],
                tooltip: {
                    show: true,
                    trigger: 'item',
                    formatter: "{b}: {c} ({d}%)"
                },
                legend: {
                    orient: 'horizontal',
                    bottom: '0%',
                    data: jx_legend,
                    textStyle: {
                        color: "white"
                    }
                },
                series: [{
                    type: 'pie',
                    selectedMode: 'single',
                    center: ['17%', '50%'],
                    radius: ['18%', '54%'],
                    color: jx_color,
                    label: {
                        normal: {
                            position: 'inner',
                            formatter: '{b}:{c}\n{d}%',
                            textStyle: {
                                color: '#fff',
                                fontWeight: 'bold',
                                fontSize: 14
                            }
                        }
                    },
                    labelLine: {
                        normal: {
                            show: false
                        }
                    },
                    data: yiwub
                }, {
                    type: 'pie',
                    selectedMode: 'single',
                    center: ['50.5%', '50%'],
                    radius: ['18%', '54%'],
                    color: jx_color,
                    label: {
                        normal: {
                            position: 'inner',
                            formatter: '{b}:{c}\n{d}%',
                            textStyle: {
                                color: '#fff',
                                fontWeight: 'bold',
                                fontSize: 14
                            }
                        }
                    },
                    labelLine: {
                        normal: {
                            show: false
                        }
                    },
                    data: shig
                }, {
                    type: 'pie',
                    selectedMode: 'single',
                    center: ['84%', '50%'],
                    radius: ['18%', '54%'],
                    color: jx_color,
                    label: {
                        normal: {
                            position: 'inner',
                            formatter: '{b}:{c}\n{d}%',
                            textStyle: {
                                color: '#fff',
                                fontWeight: 'bold',
                                fontSize: 14
                            }
                        }
                    },
                    labelLine: {
                        normal: {
                            show: false
                        }
                    },
                    data: ganb
                }]
            };
            // 使用刚指定的配置项和数据显示图表。
            myChart_all.setOption(option_all);

        }
    }, function (ex) {
    });






    // 任务饼状图
    var dataTask={
        "taskId":taskid,
        "taskexecid":taskexecid
    };
    CommonUtil.requestService("/getAllReportInfo", dataTask, true, "get", function (response, status) {
        if (response.success) {
            var task = response.data.task;
            task = CommonUtil.parseToJson(task);
            var allTask=[];
            var allTaskCount=0;
            for (var idx in task) {
                if(task[idx].name=='PASS'){
                    allTask['PASS']=task[idx].count;
                    allTaskCount=allTaskCount+task[idx].count;
                }else if(task[idx].name=='ERROR'){
                    allTask['ERROR']=task[idx].count;
                    allTaskCount=allTaskCount+task[idx].count;
                }else if(task[idx].name=='FAIL'){
                    allTask['FAIL']=task[idx].count;
                    allTaskCount=allTaskCount+task[idx].count;
                }else if(task[idx].name=='TRUE'){
                    allTask['TRUE']=task[idx].count;
                    allTaskCount=allTaskCount+task[idx].count;
                }

            }

            var scheme = response.data.scheme;
            scheme = CommonUtil.parseToJson(scheme);
            var allScheme=[];
            var allSchemeCount=0;
            for (var idx in scheme) {
                if(scheme[idx].name=='PASS'){
                    allScheme['PASS']=scheme[idx].count;
                    allSchemeCount=allSchemeCount+scheme[idx].count;
                }else if(scheme[idx].name=='ERROR'){
                    allScheme['ERROR']=scheme[idx].count;
                    allSchemeCount=allSchemeCount+scheme[idx].count;
                }else if(scheme[idx].name=='FAIL'){
                    allScheme['FAIL']=scheme[idx].count;
                    allSchemeCount=allSchemeCount+scheme[idx].count;
                }else if(scheme[idx].name=='TRUE'){
                    allScheme['TRUE']=scheme[idx].count;
                    allSchemeCount=allSchemeCount+scheme[idx].count;
                }
            }
            var script = response.data.script;
            script = CommonUtil.parseToJson(script);
            var allScript=[];
            var allScriptCount=0;
            for (var idx in script) {
                if(script[idx].name=='PASS'){
                    allScript['PASS']=script[idx].count;
                    allScriptCount=allScriptCount+script[idx].count;
                }else if(script[idx].name=='ERROR'){
                    allScript['ERROR']=script[idx].count;
                    allScriptCount=allScriptCount+script[idx].count;
                }else if(script[idx].name=='FAIL'){
                    allScript['FAIL']=script[idx].count;
                    allScriptCount=allScriptCount+script[idx].count;
                }else if(script[idx].name=='TRUE'){
                    allScript['TRUE']=script[idx].count;
                    allScriptCount=allScriptCount+script[idx].count;
                }
            }
            var myChart_task = echarts.init(document.getElementById('webTaskPie_task'));
            // 指定图表的配置项和数据
            var option_task = {
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
                        value: allTask['FAIL'],
                        name: '未执行'
                    },
                        {
                            value: allTask['TRUE'],
                            name:  '执行中'
                        },
                        {
                            value: allTask['Error'],
                            name:  'Error'
                        },
                        {
                            value: allTask['PASS'],
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
                            value: allTask['FAIL'],
                            name: '未执行'
                        },
                            {
                                value: allTask['TRUE'],
                                name: '执行中'
                            },
                            {
                                value: allTask['Error'],
                                name: 'Error'
                            },
                            {
                                value: allTask['PASS'],
                                name: 'PASS'
                            }

                        ]
                    }
                ]
            };
            // 使用刚指定的配置项和数据显示图表。
            myChart_task.setOption(option_task);

        }
    }, function (ex) {
    });




    //案例饼状图
    var dataScheme={
        "taskId":taskid,
        "taskexecid":taskexecid
    };
    CommonUtil.requestService("/getAllReportInfo", dataScheme, true, "get", function (response, status) {
        if (response.success) {
            var task = response.data.task;
            task = CommonUtil.parseToJson(task);
            var allTask=[];
            var allTaskCount=0;
            for (var idx in task) {
                if(task[idx].name=='PASS'){
                    allTask['PASS']=task[idx].count;
                    allTaskCount=allTaskCount+task[idx].count;
                }else if(task[idx].name=='ERROR'){
                    allTask['ERROR']=task[idx].count;
                    allTaskCount=allTaskCount+task[idx].count;
                }else if(task[idx].name=='FAIL'){
                    allTask['FAIL']=task[idx].count;
                    allTaskCount=allTaskCount+task[idx].count;
                }else if(task[idx].name=='TRUE'){
                    allTask['TRUE']=task[idx].count;
                    allTaskCount=allTaskCount+task[idx].count;
                }

            }

            var scheme = response.data.scheme;
            scheme = CommonUtil.parseToJson(scheme);
            var allScheme=[];
            var allSchemeCount=0;
            for (var idx in scheme) {
                if(scheme[idx].name=='PASS'){
                    allScheme['PASS']=scheme[idx].count;
                    allSchemeCount=allSchemeCount+scheme[idx].count;
                }else if(scheme[idx].name=='ERROR'){
                    allScheme['ERROR']=scheme[idx].count;
                    allSchemeCount=allSchemeCount+scheme[idx].count;
                }else if(scheme[idx].name=='FAIL'){
                    allScheme['FAIL']=scheme[idx].count;
                    allSchemeCount=allSchemeCount+scheme[idx].count;
                }else if(scheme[idx].name=='TRUE'){
                    allScheme['TRUE']=scheme[idx].count;
                    allSchemeCount=allSchemeCount+scheme[idx].count;
                }
            }
            var script = response.data.script;
            script = CommonUtil.parseToJson(script);
            var allScript=[];
            var allScriptCount=0;
            for (var idx in script) {
                if(script[idx].name=='PASS'){
                    allScript['PASS']=script[idx].count;
                    allScriptCount=allScriptCount+script[idx].count;
                }else if(script[idx].name=='ERROR'){
                    allScript['ERROR']=script[idx].count;
                    allScriptCount=allScriptCount+script[idx].count;
                }else if(script[idx].name=='FAIL'){
                    allScript['FAIL']=script[idx].count;
                    allScriptCount=allScriptCount+script[idx].count;
                }else if(script[idx].name=='TRUE'){
                    allScript['TRUE']=script[idx].count;
                    allScriptCount=allScriptCount+script[idx].count;
                }
            }
            var myChart_scheme = echarts.init(document.getElementById('webTaskPie_scheme'));
            // 指定图表的配置项和数据
            var option_scheme = {
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
                        value: allScheme['FAIL'],
                        name: '未执行'
                    },
                        {
                            value: allScheme['TRUE'],
                            name:  '执行中'
                        },
                        {
                            value: allScheme['Error'],
                            name:  'Error'
                        },
                        {
                            value: allScheme['PASS'],
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
                            value: allScheme['FAIL'],
                            name: '未执行'
                        },
                            {
                                value: allScheme['TRUE'],
                                name: '执行中'
                            },
                            {
                                value: allScheme['Error'],
                                name: 'Error'
                            },
                            {
                                value: allScheme['PASS'],
                                name: 'PASS'
                            }

                        ]
                    }
                ]
            };
            // 使用刚指定的配置项和数据显示图表。
            myChart_scheme.setOption(option_scheme);

        }
    }, function (ex) {
    });





    //脚本饼状图

    var dataScript={
        "taskId":taskid,
        "taskexecid":taskexecid
    };
    CommonUtil.requestService("/getAllReportInfo", dataScript, true, "get", function (response, status) {
        if (response.success) {
            var task = response.data.task;
            task = CommonUtil.parseToJson(task);
            var allTask=[];
            var allTaskCount=0;
            for (var idx in task) {
                if(task[idx].name=='PASS'){
                    allTask['PASS']=task[idx].count;
                    allTaskCount=allTaskCount+task[idx].count;
                }else if(task[idx].name=='ERROR'){
                    allTask['ERROR']=task[idx].count;
                    allTaskCount=allTaskCount+task[idx].count;
                }else if(task[idx].name=='FAIL'){
                    allTask['FAIL']=task[idx].count;
                    allTaskCount=allTaskCount+task[idx].count;
                }else if(task[idx].name=='TRUE'){
                    allTask['TRUE']=task[idx].count;
                    allTaskCount=allTaskCount+task[idx].count;
                }

            }

            var scheme = response.data.scheme;
            scheme = CommonUtil.parseToJson(scheme);
            var allScheme=[];
            var allSchemeCount=0;
            for (var idx in scheme) {
                if(scheme[idx].name=='PASS'){
                    allScheme['PASS']=scheme[idx].count;
                    allSchemeCount=allSchemeCount+scheme[idx].count;
                }else if(scheme[idx].name=='ERROR'){
                    allScheme['ERROR']=scheme[idx].count;
                    allSchemeCount=allSchemeCount+scheme[idx].count;
                }else if(scheme[idx].name=='FAIL'){
                    allScheme['FAIL']=scheme[idx].count;
                    allSchemeCount=allSchemeCount+scheme[idx].count;
                }else if(scheme[idx].name=='TRUE'){
                    allScheme['TRUE']=scheme[idx].count;
                    allSchemeCount=allSchemeCount+scheme[idx].count;
                }
            }
            var script = response.data.script;
            script = CommonUtil.parseToJson(script);
            var allScript=[];
            var allScriptCount=0;
            for (var idx in script) {
                if(script[idx].name=='PASS'){
                    allScript['PASS']=script[idx].count;
                    allScriptCount=allScriptCount+script[idx].count;
                }else if(script[idx].name=='ERROR'){
                    allScript['ERROR']=script[idx].count;
                    allScriptCount=allScriptCount+script[idx].count;
                }else if(script[idx].name=='FAIL'){
                    allScript['FAIL']=script[idx].count;
                    allScriptCount=allScriptCount+script[idx].count;
                }else if(script[idx].name=='TRUE'){
                    allScript['TRUE']=script[idx].count;
                    allScriptCount=allScriptCount+script[idx].count;
                }
            }


            var myChart_script = echarts.init(document.getElementById('webTaskPie_script'));
            // 指定图表的配置项和数据
            var option_script = {
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
                        value: allScript['FAIL'],
                        name: '未执行'
                    },
                        {
                            value: allScript['TRUE'],
                            name:  '执行中'
                        },
                        {
                            value: allScript['Error'],
                            name:  'Error'
                        },
                        {
                            value: allScript['PASS'],
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
                            value: allScript['FAIL'],
                            name: '未执行'
                        },
                            {
                                value: allScript['TRUE'],
                                name: '执行中'
                            },
                            {
                                value: allScript['Error'],
                                name: 'Error'
                            },
                            {
                                value: allScript['PASS'],
                                name: 'PASS'
                            }

                        ]
                    }
                ]
            };
            // 使用刚指定的配置项和数据显示图表。
            myChart_script.setOption(option_script);

        }
    }, function (ex) {
    });



    //历次任务执行结果趋势
    var dataScript={
        "taskId":taskid,
        "taskexecid":taskexecid
    };
    CommonUtil.requestService("/getAllTaskInfoSuccess", dataScript, true, "get", function (response, status) {
        if (response.success) {
            var task = response.data.task;
            task = CommonUtil.parseToJson(task);
            var allTaskState=[];
            for (var idx in task) {
                if(task[idx].name=='PASS'){
                    allTaskState['PASS']=parseInt((task[idx].count/task[idx].count)*100);
                    allTaskState['ERROR']=0;
                }else if(task[idx].name=='ERROR'){
                    allTaskState['ERROR']=parseInt((task[idx].count/task[idx].count)*100);
                }
            }
            var pass=allTaskState['PASS'];
            var error=allTaskState['ERROR'];


            var myChart_allTaskResult = echarts.init(document.getElementById('webTaskPie_allTaskResult'));
            // 指定图表的配置项和数据
            var option_allTaskResult =
                option = {
                    backgroundColor: '#101736',
                    color: ['#00c2ff', '#f9cf67', '#e92b77'],
                    legend: {
                        show: true,
                        // icon: 'circle',//图例形状
                        bottom: 45,
                        center: 0,
                        itemWidth: 14, // 图例标记的图形宽度。[ default: 25 ]
                        itemHeight: 14, // 图例标记的图形高度。[ default: 14 ]
                        itemGap: 21, // 图例每项之间的间隔。[ default: 10 ]横向布局时为水平间隔，纵向布局时为纵向间隔。
                        textStyle: {
                            fontSize: 14,
                            color: '#ade3ff'
                        },
                        data: ['2019', '2018', '2017'],
                    },
                    radar: [{

                        indicator: [{
                            text: 'PASS',
                            max: 100
                        },
                            {
                                text: 'ERROR',
                                max: 100
                            },
                            {
                                text: 'FAIL',
                                max: 100
                            },
                            {
                                text: 'TRUE',
                                max: 100
                            },
                            {
                                text: 'FALSE',
                                max: 100
                            }
                        ],

                        textStyle: {
                            color: 'red'
                        },
                        center: ['50%', '50%'],
                        radius: 142,
                        startAngle: 90,
                        splitNumber: 3,
                        orient: 'horizontal', // 图例列表的布局朝向,默认'horizontal'为横向,'vertical'为纵向.
                        // shape: 'circle',
                        // backgroundColor: {
                        //     image:imgPath[0]
                        // },
                        name: {
                            formatter: '{value}',
                            textStyle: {
                                fontSize: 14, //外圈标签字体大小
                                color: '#5b81cb' //外圈标签字体颜色
                            }
                        },
                        splitArea: { // 坐标轴在 grid 区域中的分隔区域，默认不显示。
                            show: true,
                            areaStyle: { // 分隔区域的样式设置。
                                color: ['#141c42', '#141c42'], // 分隔区域颜色。分隔区域会按数组中颜色的顺序依次循环设置颜色。默认是一个深浅的间隔色。
                            }
                        },
                        // axisLabel:{//展示刻度
                        //     show: true
                        // },
                        axisLine: { //指向外圈文本的分隔线样式
                            lineStyle: {
                                color: '#153269'
                            }
                        },
                        splitLine: {
                            lineStyle: {
                                color: '#113865', // 分隔线颜色
                                width: 1, // 分隔线线宽
                            }
                        }
                    }, ],
                    series: [{
                        name: '雷达图',
                        type: 'radar',
                        itemStyle: {
                            emphasis: {
                                lineStyle: {
                                    width: 4
                                }
                            }
                        },
                        data: [{
                            name: '2019',
                            value: [pass, error, 10, 100, 10],
                            areaStyle: {
                                normal: { // 单项区域填充样式
                                    color: {
                                        type: 'linear',
                                        x: 0, //右
                                        y: 0, //下
                                        x2: 1, //左
                                        y2: 1, //上
                                        colorStops: [{
                                            offset: 0,
                                            color: '#00c2ff'
                                        }, {
                                            offset: 0.5,
                                            color: 'rgba(0,0,0,0)'
                                        }, {
                                            offset: 1,
                                            color: '#00c2ff'
                                        }],
                                        globalCoord: false
                                    },
                                    opacity: 1 // 区域透明度
                                }
                            },
                            symbolSize: 2.5, // 单个数据标记的大小，可以设置成诸如 10 这样单一的数字，也可以用数组分开表示宽和高，例如 [20, 10] 表示标记宽为20，高为10。
                            label: {                    // 单个拐点文本的样式设置
                                normal: {
                                    show: true,             // 单个拐点文本的样式设置。[ default: false ]
                                    position: 'top',        // 标签的位置。[ default: top ]
                                    distance: 2,            // 距离图形元素的距离。当 position 为字符描述值（如 'top'、'insideRight'）时候有效。[ default: 5 ]
                                    color: '#6692e2',          // 文字的颜色。如果设置为 'auto'，则为视觉映射得到的颜色，如系列色。[ default: "#fff" ]
                                    fontSize: 14,           // 文字的字体大小
                                    formatter:function(params) {
                                        return params.value;
                                    }
                                }
                            },
                            itemStyle: {
                                normal: { //图形悬浮效果
                                    borderColor: '#00c2ff',
                                    borderWidth: 2.5
                                }
                            },
                            // lineStyle: {
                            //     normal: {
                            //         opacity: 0.5// 图形透明度
                            //     }
                            // }
                        }, {
                            name: '2018',
                            value: [50, 20, 45, 30, 75],
                            symbolSize: 2.5,
                            itemStyle: {
                                normal: {
                                    borderColor: '#f9cf67',
                                    borderWidth: 2.5,
                                }
                            },
                            areaStyle: {
                                normal: { // 单项区域填充样式
                                    color: {
                                        type: 'linear',
                                        x: 0, //右
                                        y: 0, //下
                                        x2: 1, //左
                                        y2: 1, //上
                                        colorStops: [{
                                            offset: 0,
                                            color: '#f9cf67'
                                        }, {
                                            offset: 0.5,
                                            color: 'rgba(0,0,0,0)'
                                        }, {
                                            offset: 1,
                                            color: '#f9cf67'
                                        }],
                                        globalCoord: false
                                    },
                                    opacity: 1 // 区域透明度
                                }
                            },
                            // lineStyle: {
                            //     normal: {
                            //         opacity: 0.5// 图形透明度
                            //     }
                            // }
                        }, {
                            name: '2017',
                            value: [100, 80, 12, 50, 25],
                            symbolSize: 2.5,
                            itemStyle: {
                                normal: {
                                    borderColor: '#e92b77',
                                    borderWidth: 2.5,
                                }
                            },
                            areaStyle: {
                                normal: { // 单项区域填充样式
                                    color: {
                                        type: 'linear',
                                        x: 0, //右
                                        y: 0, //下
                                        x2: 1, //左
                                        y2: 1, //上
                                        colorStops: [{
                                            offset: 0,
                                            color: '#e92b77'
                                        }, {
                                            offset: 0.5,
                                            color: 'rgba(0,0,0,0)'
                                        }, {
                                            offset: 1,
                                            color: '#e92b77'
                                        }],
                                        globalCoord: false
                                    },
                                    opacity: 1 // 区域透明度
                                }
                            }
                        }]
                    }, ]
                };
            // 使用刚指定的配置项和数据显示图表。
            myChart_allTaskResult.setOption(option_allTaskResult);

        }
    }, function (ex) {
    });




    //历次任务执行次数趋势

    var dataTaskTimes={
        "taskId":taskid,
        "taskexecid":taskexecid
    };
    CommonUtil.requestService("/getAllTaskInfoSuccess", dataTaskTimes, true, "get", function (response, status) {
        if (response.success) {
            var task_times = response.data.task_times;
            task_times = CommonUtil.parseToJson(task_times);

            var myChart_allTaskTimes = echarts.init(document.getElementById('webTaskPie_allTaskTimes'));
            // 指定图表的配置项和数据
            // Generate data
            var category = [];
            var dottedBase = +new Date();
            var lineData = [];
            var barData = [];

            for (var idx in task_times) {
                category.push(task_times[idx].time);
                lineData.push(task_times[idx].times);
            }

            // for (var i = 0; i < 20; i++) {
            //     var date = new Date(dottedBase += 1000 * 3600 * 24);
            //     category.push([
            //         date.getFullYear(),
            //         date.getMonth() + 1,
            //         date.getDate()
            //     ].join('-'));
            //     var b = Math.random() * 200;
            //     var d = Math.random() * 200;
            //     barData.push(b)
            //     lineData.push(d + b);
            // }
            // option
            var option_allTaskTimes = {
                backgroundColor: '#0f375f',
                tooltip: {
                    text: '历次任务执行次数趋势',
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow',
                        label: {
                            show: true,
                            backgroundColor: '#333'
                        }
                    }
                },
                legend: {
                    data: ['line', 'bar'],
                    textStyle: {
                        color: '#ccc'
                    }
                },
                xAxis: {
                    data: category,
                    axisLine: {
                        lineStyle: {
                            color: '#ccc'
                        }
                    }
                },
                yAxis: {
                    splitLine: {show: false},
                    axisLine: {
                        lineStyle: {
                            color: '#ccc'
                        }
                    }
                },
                series: [{
                    name: 'line',
                    type: 'line',
                    smooth: true,
                    showAllSymbol: true,
                    symbol: 'emptyCircle',
                    symbolSize: 15,
                    data: lineData
                }, {
                    name: 'bar',
                    type: 'bar',
                    barWidth: 10,
                    itemStyle: {
                        normal: {
                            barBorderRadius: 5,
                            color: new echarts.graphic.LinearGradient(
                                0, 0, 0, 1,
                                [
                                    {offset: 0, color: '#14c8d4'},
                                    {offset: 1, color: '#43eec6'}
                                ]
                            )
                        }
                    },
                    data: lineData
                }, {
                    name: 'line',
                    type: 'bar',
                    barGap: '-100%',
                    barWidth: 10,
                    itemStyle: {
                        normal: {
                            color: new echarts.graphic.LinearGradient(
                                0, 0, 0, 1,
                                [
                                    {offset: 0, color: 'rgba(20,200,212,0.5)'},
                                    {offset: 0.2, color: 'rgba(20,200,212,0.2)'},
                                    {offset: 1, color: 'rgba(20,200,212,0)'}
                                ]
                            )
                        }
                    },
                    z: -12,
                    data: lineData
                }, {
                    name: 'dotted',
                    type: 'pictorialBar',
                    symbol: 'rect',
                    itemStyle: {
                        normal: {
                            color: '#0f375f'
                        }
                    },
                    symbolRepeat: true,
                    symbolSize: [12, 4],
                    symbolMargin: 1,
                    z: -10,
                    data: lineData
                }]
            };
            // 使用刚指定的配置项和数据显示图表。
            myChart_allTaskTimes.setOption(option_allTaskTimes);

        }
    }, function (ex) {
    });





    //历次案例执行结果趋势

    var dataScript={
        "taskId":taskid,
        "taskexecid":taskexecid
    };
    CommonUtil.requestService("/getAllTaskInfoSuccess", dataScript, true, "get", function (response, status) {
        if (response.success) {
            var scheme = response.data.scheme;
            scheme = CommonUtil.parseToJson(scheme);
            var allSchemeState=[];
            for (var idx in scheme) {
                if(scheme[idx].name=='PASS'){
                    allSchemeState['PASS']=parseInt((scheme[idx].count/scheme[idx].count)*100);
                    allSchemeState['ERROR']=0;
                }else if(scheme[idx].name=='ERROR'){
                    allSchemeState['ERROR']=parseInt((scheme[idx].count/scheme[idx].count)*100);
                }
            }
            var pass=allSchemeState['PASS'];
            var error=allSchemeState['ERROR'];


            var myChart_allSchemeResult = echarts.init(document.getElementById('webTaskPie_allSchemeResult'));
            // 指定图表的配置项和数据
            var option_allSchemeResult =
                option = {
                    backgroundColor: '#101736',
                    color: ['#00c2ff', '#f9cf67', '#e92b77'],
                    legend: {
                        show: true,
                        // icon: 'circle',//图例形状
                        bottom: 45,
                        center: 0,
                        itemWidth: 14, // 图例标记的图形宽度。[ default: 25 ]
                        itemHeight: 14, // 图例标记的图形高度。[ default: 14 ]
                        itemGap: 21, // 图例每项之间的间隔。[ default: 10 ]横向布局时为水平间隔，纵向布局时为纵向间隔。
                        textStyle: {
                            fontSize: 14,
                            color: '#ade3ff'
                        },
                        data: ['2019', '2018', '2017'],
                    },
                    radar: [{

                        indicator: [{
                            text: 'PASS',
                            max: 100
                        },
                            {
                                text: 'ERROR',
                                max: 100
                            },
                            {
                                text: 'FAIL',
                                max: 100
                            },
                            {
                                text: 'TRUE',
                                max: 100
                            },
                            {
                                text: 'FALSE',
                                max: 100
                            }
                        ],

                        textStyle: {
                            color: 'red'
                        },
                        center: ['50%', '50%'],
                        radius: 142,
                        startAngle: 90,
                        splitNumber: 3,
                        orient: 'horizontal', // 图例列表的布局朝向,默认'horizontal'为横向,'vertical'为纵向.
                        // shape: 'circle',
                        // backgroundColor: {
                        //     image:imgPath[0]
                        // },
                        name: {
                            formatter: '{value}',
                            textStyle: {
                                fontSize: 14, //外圈标签字体大小
                                color: '#5b81cb' //外圈标签字体颜色
                            }
                        },
                        splitArea: { // 坐标轴在 grid 区域中的分隔区域，默认不显示。
                            show: true,
                            areaStyle: { // 分隔区域的样式设置。
                                color: ['#141c42', '#141c42'], // 分隔区域颜色。分隔区域会按数组中颜色的顺序依次循环设置颜色。默认是一个深浅的间隔色。
                            }
                        },
                        // axisLabel:{//展示刻度
                        //     show: true
                        // },
                        axisLine: { //指向外圈文本的分隔线样式
                            lineStyle: {
                                color: '#153269'
                            }
                        },
                        splitLine: {
                            lineStyle: {
                                color: '#113865', // 分隔线颜色
                                width: 1, // 分隔线线宽
                            }
                        }
                    }, ],
                    series: [{
                        name: '雷达图',
                        type: 'radar',
                        itemStyle: {
                            emphasis: {
                                lineStyle: {
                                    width: 4
                                }
                            }
                        },
                        data: [{
                            name: '2019',
                            value: [pass, error, 10, 100, 10],
                            areaStyle: {
                                normal: { // 单项区域填充样式
                                    color: {
                                        type: 'linear',
                                        x: 0, //右
                                        y: 0, //下
                                        x2: 1, //左
                                        y2: 1, //上
                                        colorStops: [{
                                            offset: 0,
                                            color: '#00c2ff'
                                        }, {
                                            offset: 0.5,
                                            color: 'rgba(0,0,0,0)'
                                        }, {
                                            offset: 1,
                                            color: '#00c2ff'
                                        }],
                                        globalCoord: false
                                    },
                                    opacity: 1 // 区域透明度
                                }
                            },
                            symbolSize: 2.5, // 单个数据标记的大小，可以设置成诸如 10 这样单一的数字，也可以用数组分开表示宽和高，例如 [20, 10] 表示标记宽为20，高为10。
                            label: {                    // 单个拐点文本的样式设置
                                normal: {
                                    show: true,             // 单个拐点文本的样式设置。[ default: false ]
                                    position: 'top',        // 标签的位置。[ default: top ]
                                    distance: 2,            // 距离图形元素的距离。当 position 为字符描述值（如 'top'、'insideRight'）时候有效。[ default: 5 ]
                                    color: '#6692e2',          // 文字的颜色。如果设置为 'auto'，则为视觉映射得到的颜色，如系列色。[ default: "#fff" ]
                                    fontSize: 14,           // 文字的字体大小
                                    formatter:function(params) {
                                        return params.value;
                                    }
                                }
                            },
                            itemStyle: {
                                normal: { //图形悬浮效果
                                    borderColor: '#00c2ff',
                                    borderWidth: 2.5
                                }
                            },
                            // lineStyle: {
                            //     normal: {
                            //         opacity: 0.5// 图形透明度
                            //     }
                            // }
                        }, {
                            name: '2018',
                            value: [50, 20, 45, 30, 75],
                            symbolSize: 2.5,
                            itemStyle: {
                                normal: {
                                    borderColor: '#f9cf67',
                                    borderWidth: 2.5,
                                }
                            },
                            areaStyle: {
                                normal: { // 单项区域填充样式
                                    color: {
                                        type: 'linear',
                                        x: 0, //右
                                        y: 0, //下
                                        x2: 1, //左
                                        y2: 1, //上
                                        colorStops: [{
                                            offset: 0,
                                            color: '#f9cf67'
                                        }, {
                                            offset: 0.5,
                                            color: 'rgba(0,0,0,0)'
                                        }, {
                                            offset: 1,
                                            color: '#f9cf67'
                                        }],
                                        globalCoord: false
                                    },
                                    opacity: 1 // 区域透明度
                                }
                            },
                            // lineStyle: {
                            //     normal: {
                            //         opacity: 0.5// 图形透明度
                            //     }
                            // }
                        }, {
                            name: '2017',
                            value: [100, 80, 12, 50, 25],
                            symbolSize: 2.5,
                            itemStyle: {
                                normal: {
                                    borderColor: '#e92b77',
                                    borderWidth: 2.5,
                                }
                            },
                            areaStyle: {
                                normal: { // 单项区域填充样式
                                    color: {
                                        type: 'linear',
                                        x: 0, //右
                                        y: 0, //下
                                        x2: 1, //左
                                        y2: 1, //上
                                        colorStops: [{
                                            offset: 0,
                                            color: '#e92b77'
                                        }, {
                                            offset: 0.5,
                                            color: 'rgba(0,0,0,0)'
                                        }, {
                                            offset: 1,
                                            color: '#e92b77'
                                        }],
                                        globalCoord: false
                                    },
                                    opacity: 1 // 区域透明度
                                }
                            }
                        }]
                    }, ]
                };
            // 使用刚指定的配置项和数据显示图表。
            myChart_allSchemeResult.setOption(option_allSchemeResult);

        }
    }, function (ex) {
    });



    //历次案例执行次数趋势

    var dataSchemeTimes={
        "taskId":taskid,
        "taskexecid":taskexecid
    };
    CommonUtil.requestService("/getAllTaskInfoSuccess", dataSchemeTimes, true, "get", function (response, status) {
        if (response.success) {
            var scheme_times = response.data.scheme_times;
            scheme_times = CommonUtil.parseToJson(scheme_times);

            var myChart_allSchemeTimes = echarts.init(document.getElementById('webTaskPie_allSchemeTimes'));
            // 指定图表的配置项和数据
            // Generate data
            var category = [];
            var dottedBase = +new Date();
            var lineData = [];
            var barData = [];

            for (var idx in scheme_times) {
                category.push(scheme_times[idx].time);
                lineData.push(scheme_times[idx].times);
            }

            // for (var i = 0; i < 20; i++) {
            //     var date = new Date(dottedBase += 1000 * 3600 * 24);
            //     category.push([
            //         date.getFullYear(),
            //         date.getMonth() + 1,
            //         date.getDate()
            //     ].join('-'));
            //     var b = Math.random() * 200;
            //     var d = Math.random() * 200;
            //     barData.push(b)
            //     lineData.push(d + b);
            // }
            // option
            var option_allSchemeTimes = {
                backgroundColor: '#0f375f',
                tooltip: {
                    text: '历次任务执行次数趋势',
                    trigger: 'axis',
                    axisPointer: {
                        type: 'shadow',
                        label: {
                            show: true,
                            backgroundColor: '#333'
                        }
                    }
                },
                legend: {
                    data: ['line', 'bar'],
                    textStyle: {
                        color: '#ccc'
                    }
                },
                xAxis: {
                    data: category,
                    axisLine: {
                        lineStyle: {
                            color: '#ccc'
                        }
                    }
                },
                yAxis: {
                    splitLine: {show: false},
                    axisLine: {
                        lineStyle: {
                            color: '#ccc'
                        }
                    }
                },
                series: [{
                    name: 'line',
                    type: 'line',
                    smooth: true,
                    showAllSymbol: true,
                    symbol: 'emptyCircle',
                    symbolSize: 15,
                    data: lineData
                }, {
                    name: 'bar',
                    type: 'bar',
                    barWidth: 10,
                    itemStyle: {
                        normal: {
                            barBorderRadius: 5,
                            color: new echarts.graphic.LinearGradient(
                                0, 0, 0, 1,
                                [
                                    {offset: 0, color: '#14c8d4'},
                                    {offset: 1, color: '#43eec6'}
                                ]
                            )
                        }
                    },
                    data: lineData
                }, {
                    name: 'line',
                    type: 'bar',
                    barGap: '-100%',
                    barWidth: 10,
                    itemStyle: {
                        normal: {
                            color: new echarts.graphic.LinearGradient(
                                0, 0, 0, 1,
                                [
                                    {offset: 0, color: 'rgba(20,200,212,0.5)'},
                                    {offset: 0.2, color: 'rgba(20,200,212,0.2)'},
                                    {offset: 1, color: 'rgba(20,200,212,0)'}
                                ]
                            )
                        }
                    },
                    z: -12,
                    data: lineData
                }, {
                    name: 'dotted',
                    type: 'pictorialBar',
                    symbol: 'rect',
                    itemStyle: {
                        normal: {
                            color: '#0f375f'
                        }
                    },
                    symbolRepeat: true,
                    symbolSize: [12, 4],
                    symbolMargin: 1,
                    z: -10,
                    data: lineData
                }]
            };
            // 使用刚指定的配置项和数据显示图表。
            myChart_allSchemeTimes.setOption(option_allSchemeTimes);

        }
    }, function (ex) {
    });


</script>
</html>
