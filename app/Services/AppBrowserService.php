<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class AppBrowserService {
	/**
	 * 获取所有浏览器
	 */
	public function getBrowsers() {
		return DB::select ( "select id,chrBrowserName as browserName,chrBrowserENName browserENName from app_exec_phone where intFlag=0" );
	}
	
	/**
	 * 根据浏览器ID 查询指定的某些浏览器
	 *
	 * @param unknown $browserIds        	
	 */
	public function getBrowserByID($browserIds) {
		return DB::select ( "select id,chrBrowserENName browserENName from app_exec_phone
				where id in ($browserIds)" );
	}
	
}

?>