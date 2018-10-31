<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/30
 * Time: 下午9:40
 */
require "test_base.php";

/**验证身份证号格式
 * @param $id_card
 * @return bool
 */
function check_id_card($id_card)
{
    $id_card = strtoupper($id_card);
    $reg = "/(^\d{15}$)|(^\d{17}([0-9]|X)$)/";
    if (!preg_match($reg, $id_card)) {
        return false;
    }
    #15:/6-address/2-96/2-月/2-日/3-顺序号
    if (15 == strlen($id_card)) {
        $reg = "/^(\d{6})+(\d{2})+(\d{2})+(\d{2})+(\d{3})$/";
        @preg_match($reg, $id_card, $splits);
        $birth = '19' . $splits[2] . '/' . $splits[3] . '/' . $splits[4];
        if (!strtotime($birth)) {
            return false;
        } else {
            return true;
        }
    } elseif (18 == strlen($id_card)) {
        $reg = "/^(\d{6})+(\d{4})+(\d{2})+(\d{2})+(\d{3})([0-9]|X)$/";
        @preg_match($reg, $id_card, $splits);
        $birth = $splits[2] . '/' . $splits[3] . '/' . $splits[4];
        if (!strtotime($birth)) {
            return false;
        } else {
            #验证校验位
            $arr_int = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
            $arr_ch = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
            $sign = 0;
            for ($i = 0; $i < 17; $i++) {
                $b = (int)$id_card[$i];
                $w = $arr_int[$i];
                $sign += $b * $w;
            }
            $n = $sign % 11;
            $check_number = $arr_ch[$n];
            if ($check_number === substr($id_card, 17, 1)) {
                return true;
            }
        }
    }
    return false;
}

function curl($url, $headers = array())
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if (0 == strpos($url, "https://")) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

/**查询身份信息
 * @param $id_card
 * @return array|bool|mixed
 */
function authId($id_card)
{
    $host = "https://api10.aliyun.venuscn.com";
    $path = "/id-card/query";
    $appCode = '51*******************4';
    $headers = array();
    array_push($headers, "Authorization:APPCODE " . $appCode);
    $params = "number={$id_card}";
    $url = $host . $path . "?" . $params;
    $result = check_id_card($id_card);
    if (!$result) {
        return ['error_code' => -1, 'error_reason' => '身份证格式错误'];
    }
    $result = curlGet($url, $headers);
    return $result;
}

debug(authId('340**************14'));