<?php

namespace App\Http\Controllers\App;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\BugService;
use App\Services\RunService;
use Illuminate\Http\Request;
use App\Services\AutoExecReportService;
use Illuminate\Support\Facades\Auth;
use App\Services\ProjectService;
use App\Services\SysDictService;
use Illuminate\Support\Facades\DB;

class AppController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    //App视图
    public function index(){
    return view ( "app.appscript" );
}


}
