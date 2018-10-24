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

    function getTaskListKey()
    {
        return 'async_task_list_key';
    }

    function manageExecuteKey()
    {
        return 'manage_execute_task_process_ids';
    }


    function getRedis()
    {
        $endpoint = getConfig('cache')->redis->endpoint;
        return McRedis::getInstance($endpoint);
    }

    function manage()
    {
        $redis = $this->getRedis();
        $pids = $redis->zrange($this->manageExecuteKey(), 0, 9, true);
        foreach ($pids as $pid => $score) {
            $time = time() - $score;
            if ($time > 8) {
                debug("Task 用时: {$time},进程{$pid} 退出!");
                $redis->zrem($this->manageExecuteKey(), $pid);
                posix_kill($pid, SIGKILL);
            }
        }

    }

    function popAndExecute()
    {
        try {
            $redis = $this->getRedis();
            $list_key = $this->getTaskListKey();

            $task_id = $redis->zrange($list_key, 0, 0);
            if (!empty($task_id)) {
                $task = $redis->get(current($task_id));
                $task = json_decode($task, true);
                debug('Pop: ', $task_id, $task);
                $redis->zrem($list_key, $task_id);
                $redis->del($task_id);
                if (!empty($task) && is_array($task)) {

                    $manage_key = $this->manageExecuteKey();
                    $pid = posix_getpid();
                    $redis->zadd($manage_key, time(), $pid);
                    $method = $task['class'] . '::' . $task['method'];
                    $arguments = $task['arguments'];
                    call_user_func($method, ...$arguments);
                    $redis->zrem($manage_key, $pid);
                }
            } else {
                debug('task null!');
                sleep(3);
            }

        } catch (Exception $e) {
            debug($e->getMessage());
        }
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

    function clearTask()
    {
        $redis = $this->getRedis();
        $keys = $redis->zrange($this->getTaskListKey(), 0, -1);
        foreach ($keys as $key) {
            $redis->del($key);
        }
        $redis->del($this->getTaskListKey());
    }

    function testAction()
    {
        $this->clearTask();
        $redis = $this->getRedis();
        $task = ['class' => 'Articles', 'method' => 'f', 'arguments' => ['a', 'b']];
        $task_id = 'task_id_' . uniqid('mc');
        $redis->zadd($this->getTaskListKey(), time(), $task_id);
        $task_str = json_encode($task);
        var_dump($task_str);
        var_dump($task_id);
        $result = $redis->set($task_id, $task_str, 1);
        debug($task_id, $task, $result,'get: ',$redis->get($task_id));
    }

    function execAction()
    {
//        $this->popAndExecute();
        $redis = $this->getRedis();
        debug($redis->get('task_id_mc5bd0275b23292'));
    }
}