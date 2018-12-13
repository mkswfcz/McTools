<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/12/13
 * Time: 下午3:00
 */
class IpLocation
{

    static function find($ip = '127.0.0.1', $lang = 'en', $decimal = 'city')
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            return ['code' => -1, 'error' => 'unSupport Ip Format'];
        }
        if (strpos($ip, ':')) {
            return ['code' => -1, 'error' => 'Db unSupport IPV6 Format'];
        }

        $clazz = 'ipip\\db\\' . ucfirst($decimal);
        $city = new $clazz(APP_ROOT . '/app/libs/ipip.ipdb');
        $location = $city->find($ip, 'CN');
        $lang_location = [];
        foreach ($location as $area) {
            $lang_location [] = translate($area, strtolower($lang));
        }
        return ['code' => 0, 'location' => $lang_location];
    }
}