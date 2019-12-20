<?php namespace App\Console\Commands;

use Illuminate\Http\Request;
use App\Services\AutoTaskExecService;
use App\Services\TestCommandService;
use App\Services\TimerTaskService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class TestCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'queue:log';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.log for everyFiveMinutes';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		//
        $connection = $this->argument('connection');
        $daemon = $this->option('daemon');
        //$this->runWormer($connection, $daemon);
        $this->getTimeTaskJob($connection,$daemon);
        //$this->updateTest($connection,$daemon);
	}


	public function runWormer($connection,$daemon)
    {
        $sleep = 3;
        //while(true){
            if($daemon){
                $nowTime=date('Y-m-d H:i:s');
                //if($nowTime=='2018-07-11 15:05:00'){
                    Log::info('start_job_success'.$nowTime);
                //}
            }
        //}
    }


    public function getTimeTaskJob($connection,$daemon)
    {
        Log::info('开始jobCommand->->->->->->');
        if($daemon){
            //根据结束时间起码大于开始时间一天的逻辑获取定时任务,sql中时间+1,防止出现当天重复获取
            $query=new TestCommandService();
            $res=$query->getTimeTaskJob();
            Log::info('获取时间段内job成功');
            $ttService = new TimerTaskService ();
            $ateService = new AutoTaskExecService ();
            //循环所获任务,组装并放置job中等待执行
            foreach ($res as $ret){
                $titask=$ret;

                //把数组转换为对象,此处特殊处理,为了下方insert()调用$request->user(),自定义组件$user对象
                $user=(object)null;
                $arr=array(
                    'id'=>$ret->userid,
                    'intCompanyID'=>$ret->companyID
                );
                $user=(object)$arr;
                $tiTaskId=$titask->tiTaskIds;
                $state=$titask->state;
                //获取当天时间
                $today=date('Y-m-d');
                //判断当天是否为定时任务区间内,如果是开始获取任务重新放置job中
                if($today>=$titask ->execBeginDate && $today<=$titask->execEndDate && $state !=1){
                    // 将任务放入队列
                    Log::info('今天:'.$today.'------------------------');
                    Log::info('执行时间:'.$titask->execTime);
                    Log::info('时间段:'.$titask ->execBeginDate."/".$titask->execEndDate);
                    Log::info('执行任务:'.$tiTaskId);
                    Log::info('开始存放任务至队列');
                    $execInfo = array(
                        "taskId" =>   $titask->taskid,
                        "selBrowsers" => explode ( ";", $titask->selBrowsers),
                        "emails" => $titask->emails
                    );
                    $rows = $ateService->getTiTaskExecStateByUser($tiTaskId, $user);
                    if (empty ($rows)) { // 若不存在正在执行的定时任务
                        $timer = array(
                            "execRate" => $titask->execRate,
                            "execTime" => $titask->execTime,
                            "execBeginDate" => $today,
                            "execEndDate" => $titask->execEndDate,
                            "lastDate" => ""
                        );
                        Log::info('timer参数组装成功,开始insert()放入job');
                        $ret_new = $ateService->insert($execInfo, $user, $tiTaskId, $timer);
                        //更新auto_timer_tasks表
                        $ttService->updateFlag($tiTaskId);
                        Log::info('成功放置队列,等待运行');
						
						
						

                        //根据taskid获取最新的reportID
                        //$taskID=$tiTaskId;
                        //$reportIds=$query->getReportIdByTaskId($taskID);
                        //$reportId=$reportIds[0]->id;


                        //根据taskid获取当前执行次数，并自增1
                        //$counts=$query->getintExecCount($taskID);
                        //Log::info('intExecCount:'.$counts[0]->intExecCount);

                        //foreach ($counts as $countsTime){
                            //$count=$countsTime->intExecCount;
                            //$countTimes=$count++;
                            //Log::info('$count:'.$count);
                        //}
                        //Log::info('countTimes:'.$count);
                        //$execTime=$today.' '.$titask->execTime;
                        //Log::info($execTime.'/'.$taskID.'/'.$count);
                        //更新report状态 时间 次数及获取报告所用reportID
                        //$re=$query->update($execTime,$taskID,$count);



                        echo "{success:1}";
                    } else
                        echo "{success:0,error:'任务正在运行，已锁定...'}";
                }else{
                    Log::info($tiTaskId.'定时任务在'.$today.'无需要执行');
                }

            }

        }
        Log::info('jobCommand结束->->->->->->');
    }


    public function updateTest($connection,$daemon)
    {

        Log::info('开始jobCommand->->->->->->');
        if($daemon){
            //根据结束时间起码大于开始时间一天的逻辑获取定时任务,sql中时间+1,防止出现当天重复获取
            $query=new TestCommandService();
            $res=$query->getTimeTaskJob();
            Log::info('获取时间段内job成功');
            $ttService = new TimerTaskService ();
            $ateService = new AutoTaskExecService ();
            //循环所获任务,组装并放置job中等待执行
            foreach ($res as $ret){
                Log::info('开始循环获取job参数');
                $id=$ret->taskid;
                $tiTaskId=$ret->tiTaskIds;
                $today=date('Y-m-d');
                $ttService = new TimerTaskService ();
                //把数组转换为对象,此处特殊处理,为了下方insert()调用$request->user(),自定义组件$user对象
                $user=(object)null;
                $arr=array(
                    'id'=>$ret->userid,
                    'intCompanyID'=>$ret->companyID
                );
                $user=(object)$arr;

                $state=$ret->state;
                //if($today>=$ret ->execBeginDate && $today<=$ret->execEndDate ) {

                    //查询出的job参数为对象,需要转换为数组
                    Log::info('组装job参数');
                    $tiTask = array(
                        'execRate' => $ret->execRate,
                        'execTime' => $ret->execTime,
                        'execBeginDate' => $today,
                        'execEndDate' => $ret->execEndDate,
                        'lastDate' => '',
                        'selBrowsers' => array($ret->selBrowsers),
                        'emails' => $ret->emails,
                        'titaskName' => $ret->titaskName,
                        'titaskType' => $ret->titaskType,
                        'projectId' => $ret->projectId,
                        'oldTaskIds' => array($ret->oldTaskIds),
                        'taskIds' => array($ret->taskid)
                    );


                    $jobs = $ttService->getTimerJobState($id, $user);
                    if (!empty ($jobs) && $jobs [0]->state) { // 正在执行
                        echo "{success:0,error:'定时任务正在执行，暂时不允许更改'}";
                    } else { // 定时任务在队列中处于排队过程中
                        Log::info('开始更新');
                        $ttService->update($id, $tiTask, $user, $jobs);
                        echo "{success:1}";
                        Log::info($tiTaskId.'定时任务进入队列成功，在'.$today.$ret->execTime.' '.'等待执行');
                    }
                //}else{
                    //Log::info($tiTaskId.'定时任务在'.$today.'无需要执行');
                //}
            }

        }
        Log::info('结束jobCommand->->->->->->');

    }










    /**
     *
     * @param unknown $connection
     * @param unknown $queue
     * @param unknown $delay
     * @param unknown $memory
     * @param unknown $daemon
     * @param unknown $tries
     */
    public function daemon($connection, $queue, $delay, $memory, $daemon, $tries)
    {
        while (true) {
        }
    }

    function getArguments()
    {
        /*
         * return [ [ 'example', InputArgument::REQUIRED, 'An example argument.' ] ];
         */
        return array(
            array(
                'connection',
                InputArgument::OPTIONAL,
                'The name of connection',
                null
            )
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array(
                'queue',
                null,
                InputOption::VALUE_OPTIONAL,
                'The queue to listen on'
            ),

            array(
                'daemon',
                null,
                InputOption::VALUE_NONE,
                'Run the worker in daemon mode'
            ),

            array(
                'delay',
                null,
                InputOption::VALUE_OPTIONAL,
                'Amount of time to delay failed jobs',
                0
            ),

            array(
                'force',
                null,
                InputOption::VALUE_NONE,
                'Force the worker to run even in maintenance mode'
            ),

            array(
                'memory',
                null,
                InputOption::VALUE_OPTIONAL,
                'The memory limit in megabytes',
                128
            ),

            array(
                'sleep',
                null,
                InputOption::VALUE_OPTIONAL,
                'Number of seconds to sleep when no job is available',
                3
            ),

            array(
                'tries',
                null,
                InputOption::VALUE_OPTIONAL,
                'Number of times to attempt a job before logging it failed',
                1
            )
        );
    }

}
