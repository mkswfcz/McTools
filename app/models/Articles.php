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

    function getUpdatedAt()
    {
        debug('date: ', $this->updated_at);
        return date('Y-m-d H:i:s', $this->updated_at);
    }

    function getAuthor()
    {
        return $this->administrator->username;
    }

    static function createArticles()
    {
        $libs = ['大', '小', '光', '树'];
        $article = new Articles();
        $article->title = $libs[self::randIndex($libs)] . $libs[self::randIndex($libs)];
        $article->content = md5(date('Y-m-d H:i:s'));
        $article->created_at = time();
        $article->updated_at = time();
        $result = $article->save();
        return $result;
    }

    static function f()
    {
        $article = Articles::findLast();
        debug('f: ', func_get_args(), $article);
    }

    function getRedirectUrl()
    {
        return '/admin';
    }


}