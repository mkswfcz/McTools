<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/12/6
 * Time: 下午4:48
 */
//1.监控进程维持稳定的进程数,回收资源
//2.业务进程执行
class pTeamTask extends \Phalcon\Cli\Task
{
    public $process_number;

    public $task_id_bind = null;

    public $monitor_id;

    public $queue;

    public $process_num;

    static public $child_processes = array();

    function setQueues()
    {
        $queue_name = 'test_queue';
        $this->queue = $queue_name;
        $this->process_num = 10;
    }

    function isForkFail($pid)
    {
        return -1 == $pid;
    }

    function isParent($pid)
    {
        if ($pid > 0) {
            debug('pid: ',$pid);
        }
        return $pid > 0;
    }

    function isChild($pid)
    {
        return 0 == $pid;
    }

    function getPid()
    {
        return posix_getpid();
    }

    function getRedis()
    {
        $cache = getConfig('cache');
        $end_point = $cache->redis->endpoint;
        return McRedis::getInstance($end_point);
    }

    function isMainProcessExit()
    {
        return posix_getppid() <= 1 && false == @pcntl_getpriority(posix_getppid());
    }

    function monitor()
    {
        $pid = pcntl_fork();
        if ($this->isForkFail($pid)) {
            die("Monitor fork error!");
        } elseif ($this->isParent($pid)) {
            $this->monitor_id = $pid;
            $pid = $this->getPid();
            echoTip("Parent Process Return Continue! pid: {$pid} ");
            return;
        }

        while (true) {
            if ($this->isMainProcessExit()) {
                echoTip("Monitor 进程 退出!");
                break;
            } else {
//                echoTip("select and clear timeout process! ");
                usleep(1000);
            }
        }

        exit(0);
    }

    static function hello($someone)
    {
//        echo "hello world! $someone" . PHP_EOL;
    }

    function popTask()
    {
        $message = ['class' => 'pTeamTask', 'method' => 'hello', 'args' => ['tomas']];
        return $message;
    }

    function executeTask()
    {
        $message = $this->popTask();
        $clazz = $message['class'];
        $method = $message['method'];
        $params = $message['args'];
        call_user_func($clazz . '::' . $method, ...$params);
    }

    function getWorkProcessKey()
    {
        return 'work_process_cache_key_' . $this->queue;
    }

    function createWorker()
    {
        $pid = pcntl_fork();
        if ($this->isForkFail($pid)) {
            die("Worker fork error!");
        } elseif ($this->isParent($pid)) {
            echoTip("Create Worker! pid: {$pid}");
            return $pid;
        }

        while (true) {
            if ($this->isMainProcessExit()) {
                echoTip("Monitor 进程 退出 子进程退出循环!");
                break;
            } else {
                $this->executeTask();
            }
        }
        exit(0);
    }

    function isNeedCreateWorker()
    {
        $value_counts = array_count_values(self::$child_processes);
        if (isset($value_counts[$this->queue])) {
            if ($value_counts[$this->queue] < $this->process_num) {
                return true;
            }
            return false;
        }
        sleep(5);
        debug('current: ', self::$child_processes);
        return true;
    }

    function recoverChildProcess()
    {
        $pid = pcntl_wait($status, WUNTRACED | WNOHANG);
        if ($pid) {
            if (isset(self::$child_processes[$pid])) {
                debug("Worker Pid {$pid} exit!");
                unset(self::$child_processes[$pid]);
            }
        }
    }


    function startAction()
    {
        $pid = pcntl_fork();

        if ($this->isForkFail($pid)) {
            die("Monitor Fork fail!");
        } elseif ($pid > 0) {
            debug("return pid: ", posix_getpid());
            return $pid;
        } else {

            echoTip("Monitor pid: {$pid}");
            $this->setQueues();

            $this->monitor();

            while (true) {

                if ($this->isNeedCreateWorker()) {

                    $worker_pid = $this->createWorker();
                    if ($worker_pid) {
                        debug("Worker Pid {$worker_pid} create!");
                        self::$child_processes[$worker_pid] = $this->queue;
                    }

                }
//                debug("Wait Recover ChildProcess!");
                $this->recoverChildProcess();
                usleep(1000);
            }
            echoTip("main exit!");
        }
    }

    function testAction()
    {
        $redis = $this->getRedis();
        $value = $redis->set('test_a', 1);
        $value = $redis->get('test_a');
        debug($value);
    }
}