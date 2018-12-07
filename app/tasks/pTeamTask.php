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
                echoTip("select and clear timeout process! ");
                usleep(1000);
            }
        }

        exit(0);
    }

    static function hello($someone)
    {
        echo "hello world! $someone" . PHP_EOL;
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

    function createWorker()
    {
        $pid = pcntl_fork();
        if ($this->isForkFail($pid)) {
            die("Worker fork error!");
        } elseif ($this->isParent($pid)) {
            $pid = $this->getPid();
            echoTip("Create Worker! pid: {$pid}");
            return;
        }

        while (true) {
            if ($this->isMainProcessExit()) {
                echoTip("Monitor 进程 退出!");
                break;
            } else {
                $this->executeTask();
            }
        }
    }

    function startAction()
    {
        $pid = $this->getPid();
        echoTip("main pid: {$pid}");
        $this->setQueueName();

        $this->monitor();


        echoTip("main exit!");
    }
}