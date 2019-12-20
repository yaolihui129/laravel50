<?php

namespace App\Http\Controllers\Automation;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AutoExecReportService;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AutoExecReportController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return View
     */
	public function index(Request $request) {
		//
		$user = Auth::user ();
		$aerService = new AutoExecReportService ();
		$taskType = $request->input ( 'taskType' );
		$keyId = $request->input ( 'keyId' );
		switch ($taskType) {
			case 1 : // 普通任务和虚拟
				$rows = $aerService->getTaskExecReportStep ( $keyId, $user, 1 );
				break;
			case 2 : // 定时任务
				$rows = $aerService->getTimerTaskExecReportStep ( $keyId, $user, 1 );
				break;
		}
		$pages = array (
				"step" => (empty ( $rows ) ? "" : $rows [0]),
				"taskType" => $taskType,
				"keyId" => $keyId 
		);
		return view ( "automation.report.report" )->with ( $pages );
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
	public function create() {
		//
	}

    /**
     * Store a newly created resource in storage.
     *
     * @return void
     */
	public function store() {
		//
	}

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param $keyId
     * @return Response
     */
	public function show(Request $request, $keyId) {
		//
		$user = Auth::user ();
		$aerService = new AutoExecReportService ();
		$taskType = $request->input ( 'taskType' );
		switch ($taskType) {
			case 1 : // 普通任务
				$rows = $aerService->getTaskExecReportStep ( $keyId, $user );
				break;
			case 2 : // 定时任务
				$rows = $aerService->getTimerTaskExecReportStep ( $keyId, $user );
				break;
		}
		if (! empty ( $rows ))
			return "{success:1,data:" . json_encode ( $rows ) . "}";
		return "{success:0,error:'报告未生成，暂时不能查看'}";
	}

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return void
     */
	public function edit($id) {
		//
	}

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return void
     */
	public function update($id) {
		//
	}

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return void
     */
	public function destroy($id) {
		//
	}
	
}
