<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/18
 * Time: 下午4:31
 */
class Administrators extends BaseModel
{
    function getRedirectUrl()
    {
        return '/admin/articles';
    }

    static function register($username, $password, $role = '')
    {
        $admin = Administrators::findLast();
        if (isNull($admin)) {
            $user = new Administrators();
            $user->username = $username;
            $user->password = md5($password);
            $user->role = $role;
            $user->created_at = time();
            $user->updated_at = time();
            $result = $user->save();
            if ($result) {
                return $user;
            }
        }
        return false;
    }

    function toLoginJson()
    {
        $json = $this->toJson();
        $json = array_merge($json, [
            'redirect_url' => $this->redirect_url
        ]);
        return $json;
    }
}