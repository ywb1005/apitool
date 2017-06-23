<?php
/**
 * Created by PhpStorm.
 * User: xz
 * Date: 2017/05/17
 * Time: 11:12
 */
require_once __DIR__.'/src/Loader.php';
$app = new Handler();
$result = $app->doParse();
if(!empty($result)){
    foreach ($result as $key => $value){
        $str = 'api: ';
        if($value['status']){
            echo $str.$value['name'].',文档生成成功!'."\n";
        }else{
            echo $str.$value['name'].',文档生成失败!'."\n";
        }
    }
}