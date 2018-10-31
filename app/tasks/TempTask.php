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
        $article = Articles::findFirstById(1);
        $article->title = 'hello';
        $article->update();
        debug(Articles::findFirstById(1));
        debug(uncamelize('ModelTab'));
        $redis = McRedis::getInstance('127.0.0.1:6379');
        $result = $redis->keys('*aa1');
        debug($result);
        debug(voltFun::ajax_link('新建','myadmin'));
    }
}