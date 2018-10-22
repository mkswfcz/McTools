<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/22
 * Time: 上午10:11
 */
class DbTask extends Phalcon\Cli\Task
{

    function getDbName()
    {
        $app_name = getAppName();
        $app_name = uncamelize($app_name);
        return $app_name;
    }

    function getBaseSql($db_name = '')
    {
        if (isNull($db_name)) {
            $db_name = $this->getDbName();
        }
        $base_sql = 'psql -U postgres -d ' . $db_name . ' -c ';
        return $base_sql;
    }

    function execute($sql, $db_name = '')
    {
        debug('sql: ', $sql);
        $base_sql = $this->getBaseSql($db_name);
        $sql = $base_sql . '\'' . $sql . '\'';
        return exec($sql);
    }
    #TODO 创建数据库
    function initAction()
    {
        $db_name = $this->getDbName();
        $init_sql = "psql -U postgres -c 'create database {$db_name}'";
        debug("create database {$db_name}!");
        system($init_sql);
    }

    function dropAction()
    {
        $db_name = $this->getDbName();
        echo "are you sure drop table {$db_name}?" . PHP_EOL;
        $input = trim(fgetc(STDIN));
        if (strtolower($input) == 'y') {
            $drop_sql = "psql -U postgres -c 'drop database {$db_name}'";
            debug("drop database {$db_name}!");
            system($drop_sql);
        } else {
            echo "cancel drop table {$db_name}!\n";
        }
    }

    function sqlFileAction($params)
    {
        $sql_dir = APP_ROOT . '/db/sqls';
        $file_name = getValue(0, $params);
        if (!$file_name) {
            debug('Lack Of Params!');
            return false;
        }
        if (!is_dir($sql_dir)) {
            mkdir($sql_dir);
        }
        file_put_contents($sql_dir . '/' . $file_name .'_'.date('Y_m_d').'.sql', '', LOCK_EX);
    }

    function parseSqls($sql_str)
    {
        $sql_str = str_replace(['  ', "\n"], '', $sql_str);
        $sqls = explode(';', $sql_str);
        $sql_handles = [];
        foreach ($sqls as $sql) {
            if(!isNull($sql)) {
                $sql_handles[] = $sql;
            }
        }
        return $sql_handles;
    }


    function execSqlAction()
    {
        $files = glob(APP_ROOT . '/db/sqls/*.sql');
        foreach ($files as $file) {
            $sql = file_get_contents($file);
            $sql_handles = $this->parseSqls($sql);
            foreach ($sql_handles as $sql_handle) {
                $this->execute($sql_handle);
            }
        }
    }

    function helpAction()
    {
        $base = 'php cli.php db ';
        $manuals = [
            '初始化db' => $base . 'init',
            '删除db' => $base . 'drop',
            '创建sql文件'=>$base.'sqlFile',
            '执行sql' => $base . 'execSql'
        ];
        foreach ($manuals as $exec => $command) {
            debug($exec.': ',$command);
        }
    }
}