<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/12/14
 * Time: 下午4:07
 */

namespace admin;
class TranslateController extends \BaseController
{
    function indexAction()
    {
        $this->view->name = 'lang';
        $trans_languages = \TransLanguages::find();
        $languages = [];
        foreach ($trans_languages as $trans_language) {
            $languages[$trans_language->country_zh] = $trans_language->code;
        }
        $this->view->langs = $languages;
    }

    function translateAction()
    {
        $lang = $this->request('lang');
        $content = $this->request('content');

        $target_content = translate($content, $lang);
        return $this->respJson(0, 'success', ['lang' => $lang, 'target_content' => $target_content]);
    }
}