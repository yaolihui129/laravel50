<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\ApiTaskExecService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\AutoTaskExecService;
use Illuminate\Support\Facades\Auth;
use App\Services\AutoBrowserService;
use App\Services\AutoJobsService;

class ApiTaskExecController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request) {
        //
        $execInfo = $request->all ();
        $ateService = new ApiTaskExecService();
        $user = Auth::user ();
        $rows = $ateService->getTaskExecStateByUser ( $execInfo, $user );
        if (empty ( $rows )) { // 若不存在正在执行的普通任务
            $ret = $ateService->insert ( $execInfo, $user );
            return "{success:1}";
        } else
            return "{success:0,error:'任务正在运行，已锁定...'}";
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id) {
        //
        $ateService = new ApiTaskExecService();
        $user = Auth::user ();
        $row = $ateService->getTaskExecInfo ( $id, $user );
        return "{success:1,data:" . json_encode ( $row ) . "}";
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id) {
        //
        // $execInfo = $request->all ();
        // $ateService = new AutoTaskExecService ();
        // $user = Auth::user ();
        // $ateService->update ( $execInfo, $id, $user );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id) {
        //
    }

    public function look(){
        return view("emails.report");
    }

    //接口任务运行方法
    public  function apitasks(){
//        exec("cmd /c python nv123/script/runTest.py",$Array,$ret);
//        var_dump($Array);
//        echo $ret;
        exec("cmd /c python D:/ApacheAppServ/www/testworm/database/schemes/nv123/script/runTest.py",$Array,$ret);
        var_dump($Array);
        echo $ret;
    }
}
