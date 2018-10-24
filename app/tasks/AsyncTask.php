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

    function getPidDir()
    {
        $pid_dir = APP_ROOT . '/app/cache/pids';
        if (!is_dir($pid_dir)) {
            mkdir($pid_dir);
        }
        return $pid_dir;
    }

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
            $lock = $redis->lock('pop_execute_async_task');
            $list_key = $this->getTaskListKey();

            $result = $redis->zrange($list_key, 0, 0);
            if (!empty($result)) {
                $task_id = current($result);
                $zrem_result = $redis->zrem($list_key, $task_id);
                debug('zrem: ', $zrem_result, $list_key, $task_id);
                $task = $redis->get($task_id);
                $task = json_decode($task, true);
                debug('Pop: ', $task_id, $task);
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
                $redis->unlock($lock);
            } else {
                debug(date('Y-m-d H:i:s'),'task null!');
                sleep(1);
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

    function savePid($pid, $prefix)
    {
        $pid_dir = $this->getPidDir();
        file_put_contents($pid_dir . '/' . $prefix . '.pid', $pid, LOCK_EX);
    }

    function monitor()
    {
        $pid = pcntl_fork();
        if (-1 == $pid) {
            die("Monitor fork failed!");
        } elseif ($pid) {
            $current_pid = posix_getpid();
            $this->savePid($current_pid, 'monitor');
            return $current_pid;
        }
        $pid = posix_getpid();
        debug("Monitor Process: {$pid} Start!");
        while (true) {
            sleep(2);
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
        $pid_dir = $this->getPidDir();
        if (file_get_contents($pid_dir . '/monitor.pid')) {
            $this->stopAction();
        }
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
        $pid_dir = $this->getPidDir();
        $pid_file = $pid_dir . '/monitor.pid';
        $pid = file_get_contents($pid_file);
        $result = posix_kill($pid, SIGKILL);
        if ($result) {
            file_put_contents($pid_file,'');
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

        $task_str = json_encode($task);
        $result = $redis->set($task_id, $task_str);
        if ($result) {
            $add_result = $redis->zadd($this->getTaskListKey(), time(), $task_id);
            debug($task_id, $task, $add_result, $result, 'get: ', $redis->get($task_id));
        }
    }

    function execAction()
    {
//        $this->popAndExecute();
        $redis = $this->getRedis();
        debug($redis->get('task_id_mc5bd028db62d43'));
    }
}