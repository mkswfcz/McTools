<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/10
 * Time: 下午8:41
 */
class Articles extends BaseModel
{
    static function createArticles()
    {
        $libs = ['大','小','光','树'];
        $article = new Articles();
        $article->id = 1;
        $article->title = $libs[mt_rand(0,count($libs))].$libs[mt_rand(0,count($libs))];
        $article->content = md5(date('Y-m-d H:i:s'));
        $result = $article->save();
        return $result;
    }
}