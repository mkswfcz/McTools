<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/12/19
 * Time: 下午9:53
 */
require "BaseModel.php";

class Coins extends BaseModel
{

}

$coins = Coins::find(['code' => 'XRP']);
//$connect = Coins::getConnect();
//var_dump($connect);
//$construct = Coins::getDataBaseColumns('coins');
//foreach ($coins as $value) {
//   var_dump($value->otc_fee);
//}


//$coin = new Coins();
//$coin->id = 1;
//$coin->save();
