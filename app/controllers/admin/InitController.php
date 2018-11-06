<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/10/18
 * Time: 下午4:45
 */

namespace admin;

use PHPQRCode\QRcode;

class InitController extends \BaseController
{
    function indexAction()
    {
        $this->view->welcome = 'Welcome to Mc!';
        $qrfile = qr('http://www.baidu.com', APP_ROOT . '/public/code.png', APP_ROOT . '/public/title.jpeg');
        $qrfile = str_replace(APP_ROOT.'/public', '', $qrfile);
        $this->view->code = "<br><img src='{$qrfile}'>";
    }
}