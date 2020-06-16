<?php

use Kuga\Service\ApiAccessLogService;
use \Phalcon\Cli\Task;
use \Kuga\Core\Model\ApiLogModel;
class CommandTask extends Task{
    /**
     * 任务最多执行1小时
     * @var int
     */
    private $maxExecuteTime = 3600;
    /**
     * 记录限制条数不超
     * @var int
     */
    private $limit = 65535;
    private $cacheData;

    private $isLogRuntime = true;
    private $_runEndMem =0;
    private $_runEndTime =0;
    private $_runStartTime = 0;
    private $_runStartMem =0;



    private $swooleHost = '127.0.0.1';
    private $swoolePort = 9510;
    private $serv;
    private $curl;
    /**
     * 媒体文件清理
     */
    public function mediaRecycleAction(){

    }
    private function _log($msg) {
        list($usec, $sec) = explode(" ", microtime());
        $u =   (float)$usec;
        echo date('H:i:s').'['.$u.']---';
        if (is_string($msg)) {
            $msg = $msg . "\n";
        } else {
            $msg = print_r($msg, true) . "\n";
        }
        echo $msg;
        $d = null;
        $msg = null;
        //unset($msg);
    }
    /**
     * 记录运行花费时间
     */
    private function _logRunTime($msg, $resetStart = true) {
        if ($this->isLogRuntime) {
            $this->_runEndMem  = memory_get_usage();
            $this->_runEndTime = microtime(true);
            $t = $this->_runEndTime - $this->_runStartTime;
            $m = $this->_runEndMem - $this->_runStartMem;
            $this->_log($msg . '--' . round($t, 4) . '秒,内存增加:' . $m . '，内存：' . ($this->_runEndMem / 1024));
            if ($resetStart) {
                $this->_runStartTime = microtime(true);
                $this->_runStartMem = memory_get_usage();
            }
        }
    }

    /**
     * 归档日志
     */
    public  function archiveApiLogAction(){
        $service = new \Kuga\Core\Service\ApiAccessLogService($this->getDI());
        $total   = $service->count();
        $totalPage = ceil($total / 1000);
        set_time_limit(0);
        $yestoday = strtotime('-1 day');
        $yestoday = time();
        //$yestoday = strtotime(date('Y-m-d 23:59:59',$yestoday));
        $ids = [];
        for($i=1;$i<=$totalPage;$i++){
            $list    = $service->getList($i,1000,0,$yestoday,false);
            if($list){
                foreach($list as $item){

                    $exist = ApiLogModel::count(['redisId=?1','bind'=>[1=>$item['id']]]);
                    if(!$exist){
                        $row = new ApiLogModel();
                        $row->mid = isset($item['memberId'])?$item['memberId']:0;
                        $row->responseTime = isset($item['responseTime'])?$item['responseTime']:0;
                        $row->requestTime  = $item['createTime'];
                        $row->userIp       = $item['ip'];
                        $row->params       = json_encode($item['params']);
                        $row->result       = isset($item['result'])?json_encode($item['result']):null;
                        $row->method       = $item['method'];
                        $row->redisId      = $item['id'];
                        $row->duration     = $item['duration']*1000;
                        $result = $row->create();
                        if(!$result){
                            echo $row->getMessages()[0]->getMessage()."\n";
                        }
                    }
                    $ids[] = $item['id'];
                }
            }
        }
        if(sizeof($ids)){
            $service->removeByIds($ids);
        }
    }
    public function serverAction(){
        $this->serv = new \Swoole\Server($this->swooleHost, $this->swoolePort);
        $this->serv->set(array(
            'worker_num' => 8,
            'daemonize' => false,
            'max_request' => 25000,
            'dispatch_mode' => 1,
            'debug_mode' => 0,
            'backlog'=>40960000,
            'task_worker_num' => 80,
            'log_file'=>'/tmp/swoole.log',
            'pid_file' => '/tmp/swoole.pid'

        ));
        $this->serv->on('connect',function($serv, $fd){
            echo "Client: connect\n";
        });
        $this->serv->on('receive',function($serv, $fd,$fromId, $data){
            $serv->task($data);
            //$serv->close($fd);
        });
        $this->serv->on('close',function($serv, $fd){
            echo "Client: close\n";
        });
        $this->serv->on('task',function($serv,$taskId,$fromId,$data){
            echo "TaskID:".$taskId.' from worker '.$fromId."\n";
            $result ='No Action';
            $command  = json_decode($data,true);
            if(!empty($command)){
                switch($command['action']){
                    case 'archiveApiLog':
                        $this->archiveApiLog();
                        break;
                }
            }else{
                return '客户端传参错误';
            }
        });
        $this->serv->on('finish',function($serv,$taskId,$data){
            echo 'TaskID:'.$taskId." finished\n";
            print_r($data);
            echo "\n";
        });
        $this->serv->start();
    }


}
