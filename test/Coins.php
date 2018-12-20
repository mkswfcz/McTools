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

$coins = Coins::find(['chinese_name' => '瑞波bi', 'limit' => 1]);
//$connect = Coins::getDbConnect();
//var_dump($connect);
//$construct = Coins::getDataBaseColumns('coins');
//foreach ($coins as $value) {
//   var_dump($value->otc_fee);
//}


//$coin = new Coins();
//$coin->id = 1;
//$coin->chinese_name = '瑞波bi';
//$coin->code= 'rp';
//var_dump($coin->save());
//var_dump($coin);
//var_dump(count($coins));
//foreach ($coins as $coin) {
//    var_dump($coin->marked_word);
//}
//$redis = Coins::getRedis('127.0.0.1:6389');
//$redis->set('test_a',1);
//$v = $redis->get('test_a');
//echo $v.PHP_EOL;
foreach ($coins as $coin) {
    var_dump($coin->delete());
}