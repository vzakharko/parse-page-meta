<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 14.5.18
 * Time: 13.33
 */

namespace Command;

use Service\MetaInformation;

/**
 * Class parse
 */
class parse implements CommandInterface
{
    static public $name = 'page:parse';

    public function __construct()
    {
    }

    public function run()
    {
        $metaInformation = new MetaInformation();
       // $result = $metaInformation->getUrlContent('https://pic2.me/wallpaper/223011.html');
        $result = $metaInformation->getUrlContent('https://bigpicture.ru/?p=1045149');

        print_r($result);
    }
}
