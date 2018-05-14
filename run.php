<?php
/**
 * Created by PhpStorm.
 * User: developer
 * Date: 4/3/18
 * Time: 4:53 PM
 */

require_once __DIR__.'/vendor/autoload.php';

if (!empty($argv)) {
    if (isset($argv[1])) {
        foreach (glob("src/Command/*.php") as $filename) {
            $className = str_replace('.php', '', basename($filename));

            $class = 'Command\\'.$className;
            if (isset($class::$name) && $class::$name === $argv[1]) {
                /** @var \Command\CommandInterface $command */
                $command = new $class;
                $command->run();
                break;
            }
        }
    }
} else {
    echo 'Empty params';
}

