<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/10
 * Time: 下午8:41
 */
class Articles extends BaseModel
{
    static function randIndex($arrays = array())
    {
        return mt_rand(0, count($arrays) - 1);
    }

    static function createArticles()
    {
        $libs = ['大', '小', '光', '树'];
        $article = new Articles();
        $article->title = $libs[self::randIndex($libs)] . $libs[self::randIndex($libs)];
        $article->content = md5(date('Y-m-d H:i:s'));
        $result = $article->save();
        return $result;
    }

    static function f()
    {
        while (true) {
            sleep(1);
            $article = Articles::findLast();
            debug('f: ', func_get_args(),$article);
        }
    }
}