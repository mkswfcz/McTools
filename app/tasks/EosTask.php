<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/11/13
 * Time: 下午6:03
 */
class EosTask extends Phalcon\Cli\Task
{
    public static function getApi()
    {
        return (new \BlockMatrix\EosRpc\ChainFactory)->api();
    }

    function tryAction()
    {
        $api = self::getApi();
        echo $api->getInfo() . PHP_EOL;
        echo $api->getAccount('yuntaocao123') . PHP_EOL;
        echo $api->getCurrencyBalance("eosio.token", "yuntaocao123");
        echo $api->getBlock("1337") . PHP_EOL;
        echo $api->getBlockHeaderState("0016e48707b181d93117b07451d9837526eba34a9a37125689fb5a73a5d28a38") . PHP_EOL;
        echo $api->getAccount("yuntaocao123") . PHP_EOL;
        echo $api->getCode("eosio.token") . PHP_EOL; //此方法废弃
        echo $api->getTableRows("eosio", "eosio", "producers", ["limit" => 10]) . PHP_EOL;
        echo $api->getCurrencyBalance("eosio.token", "atticlabeosb") . PHP_EOL;
        echo $api->getCurrencyStats("eosio.token", "EOS") . PHP_EOL;

        #支持的actions...
        echo $api->getAbi("eosio.token") . PHP_EOL; //等价于getCode
        echo $api->getProducers(10) . PHP_EOL;

        #二进制和json数据转化
        echo $api->abiJsonToBin("eosio.token", "transfer", ["blockmatrix1", "blockmatrix1", "7.0000 EOS", "Testy McTest"]) . PHP_EOL;
        echo $api->abiBinToJson("eosio.token", "transfer", "10babbd94888683c10babbd94888683c701101000000000004454f53000000000c5465737479204d6354657374") . PHP_EOL;
    }

    function transferAction()
    {
        #区块信息,签名参数,转化成二进制,发布到交易到主链
        echoTip(microtime());
        $api = self::getApi();
        $block_info = json_decode($api->getInfo(), true);
        $head_block_num = $block_info['head_block_num'];
        $block_detail = $api->getBlock($head_block_num);

        $details = json_decode($block_detail, true);
        debug($block_detail);
        echoTip(microtime());
    }

    function newAccountAction()
    {
        $api = self::getApi();
        $bin = $api->abiJsonToBin("eosio.token", "transfer", ["atticlabeosb", "blockmatrix1", "7.0000 EOS", "Testy McTest"]) . PHP_EOL;

        $balance_b = $api->getCurrencyBalance("eosio", "eoshuobipool") . PHP_EOL;
        $balance_a = $api->getCurrencyBalance("eosio.token", "atticlabeosb") . PHP_EOL;
        debug('bin: ', $bin);
        debug('balance: ', $balance_a, $balance_b);
        debug($api->getRequiredKeys());
        debug($api->getRequiredKeys());
        debug($api->getAbi('eosio.token'));
        debug($api->signTransaction());
    }

    function jungleAction()
    {
        $api = self::getApi();
        $info = json_decode($api->getInfo(), true);
        $head_block_num = $info['head_block_num'];
        $chain_id = $info['chain_id'];
        $block = json_decode($api->getBlock($head_block_num), true);
        $latest_block_num = $block['block_num'];
        $ref_block_prefix = $block['ref_block_prefix'];

        $balance = $api->getCurrencyBalance("eosio.token", 'eoslaomaocom');
        $binary = \GuzzleHttp\json_decode($api->abiJsonToBin("eosio.token", "transfer", ["yuntaocao123", "yuntaocao123", "0.5000 EOS", "hi c"]), true);

//        debug($info, $balance);
//        debug($binary['binargs'], $head_block_num, $latest_block_num, $ref_block_prefix,$chain_id);
        $args_a = [
            "available_keys" => [
                "EOS78makdvqpeJDdqtsxG7PXWDPp3fdvqZxAbfhzLqwGUZ9wecp1R",
                "EOS69GqsDi4Fqv3MYh3Y2sxkHe1XQYvRywamTToajcocRiUuQVCkZ"
            ],
            "transaction" => [
                "actions" => [
                    "account" => "eosio.token",
                    "authorization" => [
                        [
                            "actor" => "cwalletlucky",
                            "permission" => "active"
                        ]
                    ],
                    "data" => $binary,
                    "name" => "transfer"
                ],
                "context_free_actions" => [],
                "context_free_data" => [],
                "delay_sec" => 0,
                "expiration" => "2018-11-21T07:35:24",
                "max_kcpu_usage" => 0,
                "max_net_usage_words" => 0,
                "ref_block_num" => $latest_block_num,
                "ref_block_prefix" => $ref_block_prefix,
                "signatures" => []
            ]
        ];
        $args_b = [
            "ref_block_num" => $latest_block_num,
            "ref_block_prefix" => $ref_block_prefix,
            "expiration" => "2018-11-21T07:35:24",
            "actions" => [
                "account" => "cwalletlucky",
                "name" => "transfer",
                "authorization" => [[
                    "actor" => "cwalletlucky",
                    "permission" => "active",
                ]],
                "data" => $binary,
            ],
            "signatures" => [],
            ["EOS78makdvqpeJDdqtsxG7PXWDPp3fdvqZxAbfhzLqwGUZ9wecp1R"],
            $chain_id
        ];
        $un_lock = $api->unlock('/get_required_keys',$args_a);
//        $result = $api->wallet('/get_required_keys', $args_a);
        file_put_contents('/Users/apple/docker_eos/transaction.json',json_encode($args_b));
        debug('unlock: ',$un_lock);
//        debug('require_keys: ', $result);
    }
}