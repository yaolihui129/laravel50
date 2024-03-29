<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\AutoTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\ReportChartService;
use App\Services\AutoTaskService;

class IndexController extends Controller {
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {
		return view ( "index");
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
	public function store() {
		//
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param int $id        	
	 * @return Response
	 */
	public function show($id) {
		//
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
	public function update($id) {
		//
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

    /**
     * WebUI
     * @param Request $request
     * @return string
     */
	public function getTaskPies(Request $request) {
		$user = $request->user ();
		$rcService = new ReportChartService ();
		$rows = $rcService->getWebTaskPieForIndex ( $user );
		return "{success:1,data:{web:'" . json_encode ( $rows ) . "'}}";
	}

	public function getApiTaskPies(Request $request){
        $user = $request->user ();
        $rcService = new ReportChartService ();
        $rows = $rcService->getApiTaskPieForIndex ( $user );
        return "{success:1,data:{web:'" . json_encode ( $rows ) . "'}}";
    }

    public function getAppTaskPies(Request $request){
        $user = $request->user ();
        $rcService = new ReportChartService ();
        $rows = $rcService->getAppTaskPieForIndex ( $user );
        return "{success:1,data:{web:'" . json_encode ( $rows ) . "'}}";
    }

    public function getDispatchTaskPies(Request $request){
        $user = $request->user ();
        $rcService = new ReportChartService ();
        $rows = $rcService->getDispatchTaskPieForIndex ( $user );
        return "{success:1,data:{web:'" . json_encode ( $rows ) . "'}}";
    }
	
	/**
	 *
	 * @param Request $request        	
	 */
	public function getTaskLines(Request $request) {
		$user = $request->user ();
		$cycle = $request->input ( 'cycle' );
		$rcService = new ReportChartService ();
		$rows = $rcService->getWebTaskLine ( $user, $cycle );
		return "{success:1,data:{web:'" . json_encode ( $rows ) . "'}}";
	}
	
	/**
	 *
	 * @param Request $request        	
	 */
	public function getSchemeChart(Request $request) {
		$user = $request->user ();
		$cycle = $request->input ( 'cycle' );
		$rcService = new ReportChartService ();
		$rows = $rcService->getSchemeChart ( $user ,$cycle);
		return "{success:1,data:{web:'" . json_encode ( $rows ) . "'}}";
	}
	

	/**
	 *
	 * @param Request $request
	 */
	public function getScriptChart(Request $request) {
		$user = $request->user ();
		$rcService = new ReportChartService ();
		$rows = $rcService->getScriptChart ( $user);
		return "{success:1,data:{web:'" . json_encode ( $rows ) . "'}}";
	}
}
