<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/23
 * Time: 下午2:25
 */
class TempTask extends Phalcon\Cli\Task
{
    function redisAction()
    {
        $redis = Articles::getRedis();
        $redis = McRedis::getInstance('127.0.0.1:6379');

        $test_key = 'test_a_redis';
        $result = $redis->set($test_key, 1);
        debug('set: ', $result);
        $result = $redis->get($test_key);
        debug('get: ', $result);
        $result = $redis->del($test_key);
        debug('del: ', $result);

        $lock_key = 'test_lock_redis';
        $lock = $redis->lock('test_lock_redis', 1);
        sleep(0.9);
        debug('lock: ', $redis->get($lock));
        $redis->unlock($lock);
        debug('unlock: ', $redis->sadd($lock, 1));
    }

    function daemonAction()
    {
        daemon();
        while (true) {
            debug(date('Y-m-d H:i:s', time()));
            sleep(1);
        }
    }

    function pushAction()
    {
        while (true) {
//            sleep(1);
            Articles::push('f', date('Y-m-d H:i:s'));
        }
    }

    function curlAction()
    {
        $result = curlGet('http://ex.haobtc.io/test/user_agent');
    }

    function mdAction()
    {
//        Articles::createArticles();
        $articles = Articles::find();
        foreach ($articles as $article) {
            $article->created_at = time() - 24 * 60 * 60;
            $article->update();
        }
        debug(Articles::findFirstById(1));
        debug(uncamelize('ModelTab'));
        $redis = McRedis::getInstance('127.0.0.1:6379');
        $result = $redis->keys('*aa1');
        debug($result);
        debug(voltFun::ajax_link('新建', 'myadmin'));
    }

    function publishAction()
    {
        $redis = McRedis::getInstance('127.0.0.1:6389');

        $redis->publish('msg_0', json_encode(['channel' => 'price', 'message_type' => 'price']));
    }

    function mailAction()
    {
        Mailer::send('test', ['2693925861@qq.com'], "hello mailer");
    }

    function sisAction()
    {
        $redis = McRedis::getInstance('127.0.0.1:6389');
        $redis->sadd('test_sis', 1);
        $result = $redis->sismember('test_sis', 1);
        debug($result);
    }

    function jsonAction()
    {
        $str = "hello world!";
//        $str = "{\"hello\":\"world\"}";
        debug(json_decode($str, true));
    }

    function catchAction()
    {
        $query = \QL\QueryList::getInstance();
        $data = $query->get('');
        debug('da: ', $data);
    }

    function ssdbAction()
    {
        $ssdb = Articles::getSsdb();
        $ssdb->set('a', 'ssdb//127.0.0.1:8888');
        $result = $ssdb->get('a');
        $ssdb->zset('test_a', 'key_a', 1);
        $ssdb->zset('test_a', 'key_b', 2);
        $result = $ssdb->zget('test_a', 'key_a');
        $result = $ssdb->zlist('', '', 100);

        $lock = 'test_lock';
        $set = $ssdb->set($lock, time() + 5);
        $expire = $ssdb->expire($lock, 20);

        $exist = $ssdb->exists($lock);

        debug($set, $exist);
//        sleep(5);
//        debug($ssdb->exists($lock));

        $redis = Articles::getRedis();
//        $lock = $redis->lock('hello',20);
        debug($lock);
        $redis->unlock($lock);
    }

    function curlMAction()
    {
        $urls = ['a' => 'https://www.random.org/integers/', 'b' => 'https://www.random.org/integers/', 'c' => 'https://www.random.org/integers/'];
        $body = ['a' => ['num' => 6, 'min' => 1, 'max' => 100, 'col' => 6, 'base' => 10, 'format' => 'plain', 'rnd' => 1],
            'b' => ['num' => 6, 'min' => 1, 'max' => 100, 'col' => 6, 'base' => 10, 'format' => 'plain', 'rnd' => 2],
            'c' => ['num' => 6, 'min' => 1, 'max' => 100, 'col' => 6, 'base' => 10, 'format' => 'plain', 'rnd' => 1]];

        $start = microtime(true);
        list($error_code, $results) = multiCurlGet($urls, $body);
        $end = microtime(true);
        $used_time = $end - $start;
        $numbers = [];
        foreach ($results as $result) {
            $data = preg_replace("/[\s]+/is", " ", $result);
            $rand_num = array_filter(explode(' ', $data));
            $numbers[] = $rand_num;
        }
        debug('multi: ', $numbers, $used_time);

        $ergodic = [];
        $start = microtime(true);
        foreach ($urls as $k => $url) {
            $res = get($url, $body[$k]);
            $data = $res['body'];

            $data = preg_replace("/[\s]+/is", " ", $data);
            $rand_num = array_filter(explode(' ', $data));
            $ergodic[] = $rand_num;
        }
        $end = microtime(true);
        $used_time = $end - $start;

        debug('ergodic: ', $ergodic, $used_time);

    }

    function calAction()
    {
        calStore(['per_get' => 8000, 'per_used' => 3000, 'years' => 3]);
        $articles = Articles::find();
        foreach ($articles as $article) {
            $article->administrator_id = mt_rand(1, 12);
            $article->save();
        }
    }

    function getAction()
    {
        $article = Articles::findLast();
        debug($article->administrator->username);
    }

    function tableAction()
    {
        $table = SwooleTable::getInstance();
        $table->column('id', Swoole\Table::TYPE_INT, 4);
        $table->column('name', Swoole\Table::TYPE_STRING, 64);
        $table->column('num', Swoole\Table::TYPE_FLOAT);
        $result = $table->create();
        $set = $table->set('1', ['id' => 1, 'name' => 'test1', 'num' => 6.5]);
        $get = $table->get('1');
        $count = $table->count();
        debug('res: ', $result, $set, $get, $count, $table->getMemorySize());
    }

    function ipAction()
    {
        $ip = '112.49.96.86';
        $result = IpLocation::find($ip,'ka');
        if (-1 == $result['code']) {
            debug($result['error']);
            return false;
        }
        $real_location = $result['location'];
        debug($real_location);
    }
}