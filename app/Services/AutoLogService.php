<?php

namespace App\Services;

use App\AutoLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoLogService {
	
	/**
	 *
	 * @param unknown $logs        	
	 * @param unknown $scriptId        	
	 * @param unknown $schemeId        	
	 * @param unknown $execTaskId        	
	 * @param unknown $jobId        	
	 * @param unknown $payload        	
	 */
	public function insert($logs, $scriptId, $schemeId, $execTaskId, $jobId, $reportId, $payload, $browsers) {
		DB::insert ( "insert into auto_logs (intOrderNo,chrResult,chrCmd,chrCmdParam,chrErrorMessage,
				fltDuring,chrImage,chrElementAlias,chrDescription,intLineNo,intLevel,chrStatus,intScriptID,
				intSchemeID,intExecTaskID,intJobID,intTaskID,intTimerTaskID,intReportID,intBrowserID) 
				values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)", [ 
				$logs ["No"],
				$logs ["result"],
				$logs ["cmd"],
				$logs ["cmdParam"],
				$logs ["errorMessage"],
				$logs ["during"],
				$logs ["image"],
				$logs ["elementAlias"],
				$logs ["description"],
				$logs ["lineNo"],
				$logs ["level"],
				$logs ["status"],
				$scriptId,
				$schemeId,
				$execTaskId,
				$jobId,
				$payload ["taskId"],
				$payload ["tiTaskId"],
				$reportId,
				$browsers ["browserId"] 
				//$logs[0].$logs[1].$logs[2].$logs[3].$logs[4].$logs[5].$logs[6].$logs[7].$logs[8].$logs[9]
		] );
	}
}

?>