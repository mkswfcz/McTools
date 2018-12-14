<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/12/14
 * Time: 上午11:41
 */
class LangFetchTask extends Phalcon\Cli\Task
{
    function queryAction()
    {
        $ql = \QL\QueryList::getInstance();
        $res = $ql->get('https://ctrlq.org/code/19899-google-translate-languages')->find('tbody')->find('td')->texts();

        $data = [];
        foreach ($res as $i => $value) {
            if (preg_match('/Language|Code/', $value)) {
                unset($res[$i]);
            } else {
                $data[] = $value;
            }
        }

        $matches = [];
        foreach ($data as $index => $value) {
            if (0 != ($index % 2)) {
                $matches[$data[$index - 1]] = $value;
            }
        }

        foreach ($matches as $country => $code) {
            $trans_language = TransLanguages::findFirstByCountryEn($country);
            if ($trans_language) {
                debug('continue: ', $trans_language);
                continue;
            } else {
                $trans_language = new TransLanguages();
                $trans_language->country_en = $country;

                $trans_language->country_zh = translate($country, 'zh-CN');
                if ('zh-TW' == $code) {
                    $trans_language->country_zh = '繁体中文';
                }
                $trans_language->code = $code;
                $trans_language->save();
            }
        }
    }
}