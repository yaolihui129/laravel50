<?php namespace App\Http\Controllers\Exam;

use App\Http\Controllers\Controller;
use App\Services\ExamService;
use Illuminate\Http\Request;

class ExamController extends Controller {


    public function login()
    {
        return view('exam.index');

    }

    public function getAllUser()
    {
        $query=new ExamService();
        $res=$query->getAllUser();
        if($res){
            $arr['success']=1;
            $arr['data']=$res;
        }else{
            $arr['success']=0;
            $arr['data']='';
    }
        return json_encode($arr);

    }

    public function checkLogin(Request $request)
    {
        $user=$request->input('user');
        $pwd=$request->input('pwd');
        $choose=$request->input('choose');
        $query=new ExamService();
        $res=$query->checkLogin($user,$pwd,$choose);
        $arr=array();
        if($res==0){
            $arr['success']=0;
            $arr['msg']='登录成功，开始考评';
        }else if($res==1){
            $arr['success']=1;
            $arr['msg']='不存在的用户，请联系管理员添加';

        }else if($res==2){
            $arr['success']=2;
            $arr['msg']='密码不正确，请重新输入';
        }
        return json_encode($arr);
    }


    public function question(Request $request)
    {
        $fieldCode=$request->input('fieldCode');
        $productCode=$request->input('productCode');
        $choose=$request->input('choose');
        $query=new ExamService();
        $res=$query->getQuestion($fieldCode,$productCode,$choose);
        if($res){
            $arr['flag']=0;
            $arr['desc']='获取试题成功';
            $arr['data']=$res;
        }else{
            $arr['flag']=1;
            $arr['desc']='获取试题失败';
            $arr['data']='';
        }

        return json_encode($arr);

    }


    /**
     * Display a listing of the resource.
     *
     * @return void
     */
	public function index()
	{
		//
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
	public function create()
	{
		//
	}

    /**
     * Store a newly created resource in storage.
     *
     * @return void
     */
	public function store()
	{
		//
	}

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return void
     */
	public function show($id)
	{
		//
	}

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return void
     */
	public function edit($id)
	{
		//
	}

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @return void
     */
	public function update($id)
	{
		//
	}

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return void
     */
	public function destroy($id)
	{
		//
	}

}
