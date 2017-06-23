<?php
/**
 * Created by PhpStorm.
 * User: xz
 * Date: 2017/05/17
 * Time: 15:04
 */
return [
    'url' => 'http://pegasus.dev',
    'api' => [
        [
            'path'      => '',
            'group'     => 'Shop',
            'name'      => 'index',
            'title'     => '首页接口',
            'version'   => '1.0.0',
            'method'    => 'get',
            'urlParams' => [
                'version' => '2.3.0'
            ],
            'params'    => [],
            'header'    => [],
            'file'      => [
                'root' => '/www/apiTools',
                'path' => '/test.php',
                'class' => 'test',
                'function' => 'test1'
            ]
        ]
    ]
];