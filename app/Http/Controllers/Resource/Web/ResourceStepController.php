<?php namespace App\Http\Controllers\Resource\Web;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Services\ProjectService;
use App\Services\ResourceStepService;
use App\Services\SysDictService;
use Illuminate\Http\Request;

class ResourceStepController extends Controller {


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        //返回机器管理首页
        return view ( "resource.web.resourcetasStep" );
    }

    public function getTaskList(Request $request) {
        $proService = new ProjectService ();
        $wl = $proService->getProjectDataAuth ();
        $secho = $request->input ( 'sEcho' );
        $iDisplayStart = $request->input ( 'iDisplayStart' );
        $iDisplayLength = $request->input ( 'iDisplayLength' );
        $search = json_decode ( $request->input ( 'search' ), true );
        $user = $request->user ();
        $atService = new ResourceStepService();
        return $atService->getTaskList ( $secho, $iDisplayStart, $iDisplayLength, $user, $search ,$wl);
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
        $user = $request->user ();
        $task = $request->all ();
        $atService = new ResourceStepService();
        $atService->insert ( $task, $user );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(Request $request, $id)
    {
        //
        //
        $user = $request->user ();
        $atService = new ResourceStepService ();
        $task = $atService->getTaskById ( $id, $user );
        return "{success:1,task:" . json_encode ( $task ) . "}";
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id) {
        //
        $task = $request->all ();
        $taskName=$request->input('taskName');
        $MacIP=$request->input('MacIP');
        $belongTo=$request->input('belongTo');
        $ateService = new ResourceStepService ();
        $rows = $ateService->getTaskExecState ( $id,$taskName,$MacIP,$belongTo );
        if ($rows) {
            $user = $request->user ();
            $ateService->update ( $id, $task, $user,$taskName,$MacIP,$belongTo );
        } else{
            return "{success:0,error:'存在相同的MAC地址或资源名称，请重新输入.'}";
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        //
        $user = $request->user ();
        $atService = new ResourceStepService ();
        $res=$atService->delete ( $id, $user );
    }




    public function checkAdd(Request $request)
    {
        $taskName=$request->input('taskName');
        $MacIP=$request->input('MacIP');
        $belongTo=$request->input('belongTo');
        $query=new ResourceStepService();
        $res=$query->checkAdd($taskName,$MacIP,$belongTo);
        if($res==1){
            $arr['success'] = 1;
            $arr['data'] = '已存在步骤ID，请重新填写';
        } else if($res==2) {
            $arr['success'] = 2;
            $arr['data'] = '已存在步骤名称，请重新填写';
        }else if($res==0){
            $arr['success'] = 0;
        }
        echo json_encode($arr);
    }





    public function checkDel(Request $request)
    {
        $id=$request->input('id');
        $query=new ResourceStepService();
        $res=$query->checkDel($id);
        if(empty($res)){
            $arr['success'] = 1;
        }else {
            $arr['success'] = 0;
        }
        echo json_encode($arr);
    }
    public function checkEdit(Request $request)
    {
        $id=$request->input('id');
        $query=new ResourceStepService();
        $res=$query->checkEdit($id);
        if(empty($res) ){
            $arr['success'] = 1;
        }else {
            $arr['success'] = 0;
        }
        echo json_encode($arr);
    }

}
