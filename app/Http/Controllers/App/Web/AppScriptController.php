<?php

namespace App\Http\Controllers\App\Web;

use App\Http\Controllers\Controller;
use App\Services\AppScriptService;
use Illuminate\Http\Request;
use App\Services\FlowProcessService;
use Illuminate\Support\Facades\Auth;
use App\Services\FlowService;
use App\Services\ProductService;
use App\Services\AutoScriptService;
use Illuminate\Support\Facades\Log;
use App\Services\UEUploadService;
use App\Services\AttachmentService;
use App\Services\ProjectService;

class AppScriptController extends Controller
{
   // private $root = '/database/schemes/appTest/script/';
    //private $root = '/public/appscript/';
    private $root = '/public/';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        return view("app.web.appscript");
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //
        $productId = $request->input('productId');
        $projectId = $request->input('projectId');
        if ($productId && $projectId) {
            $script = array(
                "productId" => $productId,
                "projectId" => $projectId
            );
            $asService = new AppScriptService();
            $user = $request->user();
            $scriptFiles = $request->input('scriptFiles');
            $proService = new ProjectService ();
            $projects = $proService->getProjectLinkById($productId, $user); //
            if (!empty ($projects)) {
                $scriptPath = $this->root;
                foreach ($projects as $project) {
                    $scriptPath .= '';
                }
                foreach ($scriptFiles as $scriptFile) {
                    $script ["attId"] = $scriptFile ["attid"];
                    $script ["scriptName"] = str_replace($scriptFile ["type"], "", $scriptFile ["title"]);
                    $script ["file"] = $scriptFile ["url"];
                    $script ["type"] = $scriptFile ["type"];
                    $asService->insert($scriptPath, $script, $user);
                }
            }
        } else {
            return "{success:0,error:'项目或者产品不明确，请刷新后重新创建'}";
        }
    }

    /**
     *
     * @param Request $request
     */
    public function storeParams(Request $request)
    {
        $user = $request->user();
        $scriptId = $request->input('scriptId');
        $script = array(
            "scriptId" => $scriptId
        );
        $asService = new AppScriptService();
        $projectId = $asService->getProjectByScriptId($scriptId, $user);
        if (!empty ($projectId)) {
            $proService = new ProjectService ();
            $projects = $proService->getProjectLinkById($projectId, $user);
            $scriptPath = $this->root;
            foreach ($projects as $project) {
                $scriptPath .= $project->chrProjectName . "/";
            }
            $scriptFiles = $request->input('scriptFiles');
            foreach ($scriptFiles as $scriptFile) {
                $script ["attId"] = $scriptFile ["attid"];
                $script ["filename"] = $scriptFile ["title"];
                $script ["file"] = $scriptFile ["url"];
                $asService->updateParams($scriptPath, $script, $user);
            }
        }
    }

    /**
     *
     * @param Request $request
     */
    public function storeImages(Request $request)
    {
        $scriptId = $request->input('scriptId');
        $script = array(
            "scriptId" => $scriptId
        );
        $filePath = $this->root;
        $asService = new AppScriptService();
        $scriptFiles = $request->input('scriptFiles');
        $user = $request->user();
        foreach ($scriptFiles as $scriptFile) {
            $script ["attId"] = $scriptFile ["attid"];
            $script ["filename"] = $scriptFile ["title"];
            $script ["file"] = $scriptFile ["url"];
            $asService->updateFiles($filePath, $script, $user);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        //
        return view('app.web.appscript_edit');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit(Request $request, $id)
    {
        //
        $user = $request->user();
        $asService = new AppScriptService();
        $code = $asService->readScriptContent($id, $user);
        return view('app.web.appscript_edit')->withScriptcode($code);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
        $user = $request->user();
        $code = $request->input('code');
        $asService = new AppScriptService();
        $asService->updateScriptContent($id, $code, $user);
        return "{success:1}";
    }

    public function update2(Request $request)
    {
        //
        $user = $request->user();
        $code = $request->input('code');
        $id = $request->input('id');
        $asService = new AppScriptService();
        $asService->updateScriptContent($id, $code, $user);
        return "{success:1}";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request, $ids)
    {
        //
        $user = $request->user();
        $asService = new AppScriptService();
        $asService->delete($ids, $user);
    }

    /**
     *
     * @param Request $request
     */
    public function uploader(Request $request)
    {
        try {
            $user = $request->user();
            header("content-type:text/html; charset=uft-8");
            if (isset ($_GET ['index'])) {
                return view("common.webuploader.webuploader")->withArgs($request->all());
            }
            $optType = $_GET ["optType"];
            $dirName = "";
            switch ($optType) {
                case "scriptParam" :
                    $filepath = base_path("config") . '\webuploader\uploaderscriptargs.json';
                    break;
                case "scriptImage" :
                    $filepath = base_path("config") . '\webuploader\uploaderscriptimages.json';
                    break;
                default :
                    $filepath = base_path("config") . '\webuploader\uploaderscript.json';
                    break;
            }
            $CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents($filepath)), true);
            $action = $_GET ['action'];
            switch ($action) {
                case 'config' :
                    $result = json_encode($CONFIG);
                    break;
                /* 上传文件 */
                case 'uploadfile' :
                    $asService = new AppScriptService();
                    $fileJson = $asService->uploadfile($action, $CONFIG, true);
                    if ($fileJson ['state'] == "SUCCESS") { // 存储成功后 将文件信息存储到数据库中
                        $file = array(
                            "file" => $fileJson ["url"],
                            "fileName" => $fileJson ["title"],
                            "fileType" => $fileJson ["type"],
                            "fileSize" => $fileJson ["size"]
                        );
                        $attService = new AttachmentService ();
                        $fileJson ["attid"] = $attService->insert($file, $user);
                    }
                    $result = json_encode($fileJson);
                    break;
                default :
                    $result = json_encode(array(
                        'state' => '请求地址出错'
                    ));
                    break;
            }
        } catch (\Exception $e) {
            throw ($e);
        }

        /* 输出结果 */
        if (isset ($_GET ["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET ["callback"])) {
                echo htmlspecialchars($_GET ["callback"]) . '(' . $result . ')';
            } else {
                echo json_encode(array(
                    'state' => 'callback参数不合法'
                ));
            }
        } else {
            echo $result;
        }
    }

    /**
     * 获取脚本树
     *
     * @param Request $request
     */
    public function getScriptTree(Request $request)
    {
        $nodeId = $request->input('id');
        $user = $request->user();
        if (!$nodeId) {
            $pduService = new ProjectService ();
            $products = $pduService->getProductTree($user);
            return json_encode($products);
        } else {
            $scriptService = new AppScriptService();
            $cases = $scriptService->getScriptByParent($nodeId, $user);
            return json_encode($cases);
        }
    }

    /**
     * 获取脚本列表
     *
     * @param Request $request
     */
    public function getScriptList(Request $request)
    {
        // $isParent = $request->input ( 'isParent' );
        // $nodeId = $request->input ( 'nodeId' );
        $proService = new ProjectService ();
        $wl = $proService->getProjectDataAuth();
        $secho = $request->input('sEcho');
        $iDisplayStart = $request->input('iDisplayStart');
        $iDisplayLength = $request->input('iDisplayLength');
        $search = json_decode($request->input('search'), true);
        $user = $request->user();
        $asService = new AppScriptService();
        return $asService->getScriptJsonByParent($secho, $iDisplayStart, $iDisplayLength, $user, $search, $wl);
    }

    /**
     * 获取历史脚本列表
     *
     * @param Request $request
     */
    public function getAnnalsScriptList(Request $request)
    {
        $secho = $request->input('sEcho');
        $iDisplayStart = $request->input('iDisplayStart');
        $iDisplayLength = $request->input('iDisplayLength');
        $scriptId = $request->input('scriptId');
        $attId = $request->input('attId');
        $asService = new AppScriptService();
        $res = $asService->getAnnalsScriptList($scriptId, $attId, $secho, $iDisplayStart, $iDisplayLength);
        return $res;
    }

    /**
     *
     * @param Request $request
     */
    public function getScriptText(Request $request)
    {
        $attId = $request->input('attId');
        $nowAttId = $request->input('nowAttId');
        $asService = new AppScriptService();
        $origScript = $asService->getScriptText($attId);
        $changeScript = $asService->getScriptText($nowAttId);
        $pages = array(
            "original" => $origScript ["content"],
            "originalVer" => $origScript ["version"],
            "changed" => $changeScript ["content"],
            "changedVer" => $changeScript ["version"]
        );
        return view("app.web.appscript_diff")->with($pages);
    }

    /**
     *
     * @param Request $request
     */
    public function rollBack(Request $request)
    {
        $attId = $request->input('attId');
        $nowAttId = $request->input('nowAttId');
        $scriptId = $request->input('scriptId');
        $backMemo = $request->input('backMemo');
        $user = Auth::user();
        $asService = new AppScriptService();
        $asService->rollBack($attId, $nowAttId, $scriptId, $backMemo, $user);
        return "{success:1}";
    }

    /**
     *
     * @param Request $request
     */
    public function getBackLogList(Request $request)
    {
        $secho = $request->input('sEcho');
        $iDisplayStart = $request->input('iDisplayStart');
        $iDisplayLength = $request->input('iDisplayLength');
        $scriptId = $request->input('scriptId');
        $scriptRelateId = $request->input('scriptRelateId');
        $asService = new AppScriptService();
        if ($scriptRelateId) {
            return $asService->getScriptVerBackLogList($secho, $iDisplayStart, $iDisplayLength, $scriptId, $scriptRelateId);
        }
        return $asService->getScriptBackLogList($secho, $iDisplayStart, $iDisplayLength, $scriptId);
    }


    //选项设置视图
    public function index_choose($script, $scriptName)
    {
        //
        //$script=664;
        return view("app.web.choose")->with("script", $script)->with("scriptName", $scriptName);
    }

    //完善信息
    public function choose(Request $request)
    {
        $scriptid = $request->input('scriptid');
        $name = $request->input('name');
        $package_name = $request->input('package_name');
        $version = $request->input('version');
        $type = $request->input('type');
        $classification = $request->input('classification');
        $remarks = $request->input('remarks');
        $log = $request->input('log');
        $description = $request->input('description');
        $time = date("Y-m-d H-i-s", time());
        $query = new AppScriptService();
        $check = $query->check($scriptid);
        if ($check) {
            $res = $query->appupdate($scriptid, $name, $package_name, $version, $type, $classification, $remarks, $log, $description, $time);
            if ($res) {
                $arr['success'] = 1;
            } else {
                $arr["success"] = 0;
            }
            echo json_encode($arr);
        } else {
            $res = $query->choose($scriptid, $name, $package_name, $version, $type, $classification, $remarks, $log, $description, $time);
            if ($res) {
                $arr['success'] = 1;
            } else {
                $arr["success"] = 0;
            }
            echo json_encode($arr);
        }

    }
}
