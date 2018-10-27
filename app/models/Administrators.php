<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/18
 * Time: 下午4:31
 */
class Administrators extends BaseModel
{
    static function register($username, $password, $role = '')
    {
        $user = Administrators::findLast();
        print_r($user);
        if (isNull($user)) {
            debug('nul user');
        } else {
            debug('exist');
        }
        return false;
    }
}