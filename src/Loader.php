<?php
/**
 * Created by PhpStorm.
 * User: xz
 * Date: 2017/05/17
 * Time: 17:45
 */


class Loader
{
    public static function loadClass($class){
        $file = $class.'.php';
        if(is_file(__DIR__.'/'.$file)){
            require_once ($file);
        }
    }
}

spl_autoload_register(['Loader', 'loadClass']);