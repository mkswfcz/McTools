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
        $admin = Administrators::findLast();
        if (isNull($admin)) {
            $user = new Administrators();
            $user->username = $username;
            $user->password = $password;
            $user->role = $role;
            $user->created_at = time();
            $user->updated_at = time();
            $result = $user->save();
            return $result;
        }
        return false;
    }
}