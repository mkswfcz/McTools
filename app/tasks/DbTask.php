<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/22
 * Time: 上午10:11
 */
class DbTask extends Phalcon\Cli\Task
{

    function getBaseSql()
    {
        $app_name = getAppName();
        $app_name = uncamelize($app_name);
        $base_sql = 'psql -U postgres -d ' . $app_name . ' -c ';
        return $base_sql;
    }

    function execute($sql)
    {
        $base_sql = $this->getBaseSql();
        debug('sql: ',$sql);
        $sql = $base_sql.'\''.$sql.'\'';
        $result = exec($sql);
        debug($result);
    }

    function testAction()
    {
        $article = Articles::findLast();
        debug($article);
        Database::getSql();
    }

    #TODO 创建数据库
    function initAction()
    {
        $base_sql = $this->getBaseSql();
//        $this->execute('select * from articles');
//        echo isNumericArray([0=>1,1=>2,2=>3]).PHP_EOL;
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
        file_put_contents($sql_dir . '/' . $file_name . '.sql', '', LOCK_EX);
    }

    function parseSqls($sql_str)
    {
        $sql_str = str_replace("\n", '', $sql_str);
        $sqls = explode(';', $sql_str);
        $sqls = array_filter($sqls);
        $sql_handles = [];
        foreach ($sqls as $sql) {
            $sql_handles[] = $sql . ';';
        }
        return $sql_handles;
    }


    function execSqlAction()
    {
        $files = glob(APP_ROOT . '/db/sqls/*.sql');
        foreach ($files as $file) {
            $sql = file_get_contents($file);
            $sql_handles = $this->parseSqls($sql);
//            debug($sql_handles);
            $this->getBaseSql();
        }
    }
}