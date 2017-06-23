<?php
/**
 * Created by PhpStorm.
 * User: xz
 * Date: 2017/05/17
 * Time: 16:25
 */

class Handler
{
    protected $docArray;
    protected $apiArray;

    public function __construct()
    {
        $this->docArray = require_once __DIR__.'/../configs/docTemp.php';
        $this->apiArray = require_once __DIR__.'/../configs/apiTest.php';
    }

    /**
     * 解析配置
     * @return array
     */
    public function doParse(){
        foreach ($this->apiArray as $key => $value){
            if($key == 'url'){
                $url = $value ?? '';
            }
            if($key == 'api'){
                $api = $value ?? [];
            }
        }
        foreach ($api as $item){
            $request = [
                'url'       => $url.$item['path'],
                'method'    => $item['method'],
                'urlParams' => $item['urlParams'],
                'params'    => $item['params'],
                'header'    => $item['header']
            ];
            $http   = new Http($request);
            $res    = json_decode($http->http_req(), true);
            if($res){
                $doc = $this->addDocParams($item, Doc::create($res, 1));
            }
            $response[] = [
                'name'      => $item['title'],
                'status'    => $this->insertComment($doc, $item['file'])
            ];
        }
        return $response;
    }

    /**
     * 封装文档注释
     * @param $params
     * @param string $data
     * @return string
     */
    public function addDocParams($params, $data = ''){
        $str = "\n".'    /**'."\n";
        foreach ($this->docArray as $key => $value){
            if(is_array($value)){
                $str .= '     * '.$key;
                foreach ($value as $name){
                    switch ($name){
                        case 'method':
                            $str .= ' {'.$params[$name].'}';
                            break;
                        case 'path':
                            $str .= ' '.$params[$name];
                            break;
                        case 'title':
                            $str .= ' '.$params[$name];
                            break;
                    }
                }
                $str .= "\n";
            }else{
                switch ($value){
                    case 'group':
                    case 'name':
                    case 'version':
                        $str .= '     * '.$key;
                        $str .= ' '.$params[$value]."\n";
                        break;
                    case 'header':
                        if(!empty($params[$value])){
                            $str .= "     * {String} Authorization='Bearer token' need token"."\n";
                        }
                        break;
                    case 'param':
                        $arr = array_merge($params['urlParams'], $params['params']);
                        foreach ($arr as $k => $v){
                            $str .= '     * '.$key.' {type} '.$k."\n";
                        }
                        break;
                    case 'example':
                        $str .= '     * '.$key;
                        $str .= ' {json} Success-Response:'.$data;
                        break;
                }
            }
        }
        return $str."     **/\n";
    }

    /**
     * 插入文档注释
     * @param string $str
     * @param array $file
     * @return bool|string
     */
    public function insertComment($str = '', $file = []){
        if(empty($str)){
            return false;
        }
        $filePath = $file['root'].$file['path'];
        $newPath = __DIR__.'/../template'.$file['path'];
        require_once ($filePath);
        $class = new ReflectionClass($file['class']);
        if(!$class->hasMethod($file['function'])){
            return false;
        }
        $method = new ReflectionMethod($file['class'], $file['function']);
//        var_dump($method->getDocComment(),$method->getStartLine());die;
        $line = $method->getStartLine();
        if(!is_file($newPath)){
            copy($filePath, $newPath);
        }
        $string = $this->getInsertString($newPath, $line, $str);
        if($string){
            $fp = fopen($newPath, "w");
            $num = fwrite($fp, $string);
            fclose($fp);
            if($num){
                return true;
            }
        }
        return false;
    }

    /**
     * 准备写入文件的内容
     * @param $filePath
     * @param $line
     * @param $string
     * @return string
     */
    public function getInsertString($filePath, $line, $string){
        //重新组织文件内容,把文档注释加入进去
            $fp1 = fopen($filePath, 'r');
            $i = 1;
            $fileString = '';
            while (!feof($fp1)){
                $fileString .= fgets($fp1);
                if($i == $line - 1){
                    $fileString .= $string;
                }
                ++$i;
            }
            fclose($fp1);
            return $fileString;
    }
}