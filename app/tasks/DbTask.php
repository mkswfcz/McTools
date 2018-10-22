<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/22
 * Time: 上午10:11
 */
class DbTask extends Phalcon\Cli\Task
{

    function isMysql()
    {
        $config = getConfig('database');
        return $config->adapter == 'Mysql' ? true : false;
    }

    function getDbName()
    {
        $app_name = getAppName();
        $app_name = uncamelize($app_name);
        return $app_name;
    }

    function getDatabase()
    {
        $database = getConfig('database');
        return $database;
    }

    function getBaseSql($db_name = '')
    {
        if (isNull($db_name)) {
            $db_name = $this->getDbName();
        }
        $database = $this->getDatabase();
        $base_sql = 'psql -U postgres -h ' . $database->host . ' -p ' . " {$database->port} -d " . $db_name . ' -c ';
        if ($this->isMysql()) {
            $base_sql = 'mysql -uroot -p -h' . $database->host . ' -P' . $database->port . ' -e ';
        }
        return $base_sql;
    }

    function execute($sql, $db_name = '')
    {
        $base_sql = $this->getBaseSql($db_name);
        $sql = $base_sql . '\'' . $sql . '\'';
        debug('sql: ', $sql);
        return exec($sql);
    }

    #TODO 创建数据库
    function initAction()
    {
        $db_name = $this->getDbName();
        $database = getConfig('database');
        if ($this->isMysql()) {
            $sql = $this->getBaseSql();
            $init_sql = $sql . '\'create database mc_tools \'';
        } else {
            $init_sql = "psql -U postgres -h 127.0.0.1 -p " . " {$database->port} " . "-c 'create database {$db_name}'";
        }
        debug("{$database->adapter}: create database {$db_name}!");
        system($init_sql);
    }

    function dropAction()
    {
        $database = $this->getDatabase();
        $dbname = $this->getDbName();
        echo "are you sure drop table {$dbname}?" . PHP_EOL;
        $input = trim(fgetc(STDIN));
        if (strtolower($input) == 'y') {
            if ($this->isMysql()) {
                $drop_sql = $this->getBaseSql() . '\'drop database ' . $dbname . '\'';
            } else {
                $drop_sql = "psql -U postgres -c 'drop database {$dbname}'";
            }
            debug("{$database->adapter} drop database {$dbname}!");
            system($drop_sql);
        } else {
            echo "cancel drop table {$dbname}!\n";
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
        file_put_contents($sql_dir . '/' . $file_name . '_' . date('Y_m_d') . '.sql', '', LOCK_EX);
    }

    function parseSqls($sql_str)
    {
        $sql_str = str_replace(['  ', "\n"], '', $sql_str);
        $sqls = explode(';', $sql_str);
        $sql_handles = [];
        foreach ($sqls as $sql) {
            if (!isNull($sql)) {
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
            '创建sql文件' => $base . 'sqlFile',
            '执行sql' => $base . 'execSql'
        ];
        foreach ($manuals as $exec => $command) {
            debug($exec . ': ', $command);
        }
    }
}