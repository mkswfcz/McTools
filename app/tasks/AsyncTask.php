<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/23
 * Time: 下午4:45
 */
class AsyncTask extends Phalcon\Cli\Task
{
    private $task_maps = array();

    function getRedis()
    {
        $endpoint = getConfig('cache')->redis->endpoint;
        return McRedis::getInstance($endpoint);
    }

    function manage()
    {
        debug('execute manage...');
    }

    function popAndExecute()
    {
        debug('Pop and Exec!');
    }

    function doTask()
    {
        while (true) {
            if (pidDisappear(posix_getppid())) {
                break;
            } else {
                $this->popAndExecute();
            }
        }
    }

    function childProcess()
    {
        $pid = pcntl_fork();
        if (-1 == $pid) {
            die("Child Process Fork Error!");
        } elseif ($pid) {
            return $pid;
        }
        $this->doTask();
        exit(0);
    }

    function getMainProcessKey()
    {
        return 'async_main_process_cache_key';
    }

    function monitor()
    {
        $pid = pcntl_fork();
        if (-1 == $pid) {
            die("Monitor fork failed!");
        } elseif ($pid) {
            $redis = $this->getRedis();
            $current_pid = posix_getpid();
            $redis->set($this->getMainProcessKey(), $current_pid);
            return $current_pid;
        }
        $pid = posix_getpid();
        debug("Monitor Process: {$pid} Start!");
        while (true) {
            sleep(6);
            $this->manage();
            if (pidDisappear(posix_getppid())) {
                break;
            }
        }
        debug("Monitor Process: {$pid} Exit!");
        exit(0);
    }

    function startAction()
    {
        $pid = pcntl_fork();
        if (-1 == $pid) {
            die("Main fork failed!");
        } elseif ($pid) {
            $current_pid = posix_getpid();
            debug("Terminal {$current_pid}Exit!");
            exit(0);
        }

        $current_pid = $this->monitor();
        debug("Main Process {$current_pid} start!");
        $async = getConfig('async');
        while (true) {
            $flag = false;
            $real_processes = array_count_values($this->task_maps);
            foreach ($async as $queue => $count) {
                if (isset($real_processes[$queue]) && $count <= $real_processes[$queue]) {
                    sleep(1);
                    continue;
                } else {
                    $flag = $queue;
                    break;
                }
            }
            if ($flag) {
                $child_pid = $this->childProcess();
                if ($child_pid > 0) {
                    $this->task_maps[$child_pid] = $flag;
                }
            }
            $pid = pcntl_wait($status, WNOHANG | WUNTRACED);
            if ($pid > 0) {
                if (isset($this->task_maps[$pid])) {
                    debug("Child Process {$pid} Exit!");
                    unset($this->task_maps[$pid]);
                }
            }
        }

    }

    function stopAction()
    {
        $redis = $this->getRedis();
        $pid = $redis->get($this->getMainProcessKey());
        $result = posix_kill($pid, SIGKILL);
        if ($result) {
            debug("Main Process {$pid} Exit!");
        }
    }
}