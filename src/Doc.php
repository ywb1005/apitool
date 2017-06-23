<?php

/**
 * Created by PhpStorm.
 * User: xz
 * Date: 2017/05/11
 * Time: 11:12
 */
class Doc
{

    //格式化之后的字符串
    public static $str = '';
    private static $type;

    const STRING = '     *';
    const FORMAT_DOC = 1;
    const FORMAT_JSON = 0;

    /**
     * 处理入口
     * @param $array
     * @param $type
     * @return string
     */
    public static function create($array, $type = self::FORMAT_JSON){
        self::$type = $type;
        self::parseArray($array, false, 1);
        $result = self::formatJson();
        return $result;
    }

    /**
     * @param $arr 数组
     * @param bool $flag 是否是索引数组
     * @param int $level 层级(用来判断缩进数)
     * @return bool
     */
    private static function parseArray($arr, $flag = false, $level = 1){
        $typeString = self::getFormatType(self::$type);
        if(!is_array($arr)){
            return false;
        }
        $i = 0;
        foreach ($arr as $key => $value){
            if(self::isIndexArray($value)){
                self::$str .= $typeString.self::getSpace($level).'"'.$key.'": ['."\n";
                self::parseArray($value, $flag = true, $level+1);
                if($i != count($arr)-1){
                    ++$i;
                    self::$str .= $typeString.self::getSpace($level).'],'."\n";
                }else{
                    self::$str .= $typeString.self::getSpace($level).']'."\n";
                }
            }else{
                if(is_array($value)){
                    //索引数组不需要key值
                    if(self::isIndexArray($arr)){
                        self::$str .= $typeString.self::getSpace($level).'{'."\n";
                    }else{
                        self::$str .= $typeString.self::getSpace($level).'"'.$key.'": {'."\n";
                    }
                    self::parseArray($value, $flag, $level+1);
                    //递归结束给加上结束符
                    if($key != count($arr)-1){
                        if($i != count($arr)-1){
                            ++$i;
                            self::$str .= $typeString.self::getSpace($level).'},'."\n";
                        }else{
                            self::$str .= $typeString.self::getSpace($level).'}'."\n";
                        }
                    }else{
                        if($i != count($arr)-1){
                            ++$i;
                            self::$str .= $typeString.self::getSpace($level).'},'."\n";
                        }else{
                            self::$str .= $typeString.self::getSpace($level).'}'."\n";
                        }
                    }
                }else{
                    if(self::isIndexArray($arr)){
                        if($key != count($arr)-1){
                            self::$str .= $typeString.self::getSpace($level).$value.','."\n";
                        }else{
                            self::$str .= $typeString.self::getSpace($level).$value."\n";
                        }
                    }else{
                        if(gettype($value) == 'string'){
                            $value = '"'.$value.'"';
                        }
                        //处理null
                        if(gettype($value) == 'NULL'){
                            $value = 'null';
                        }
                        //处理bool
                        if(gettype($value) == 'boolean'){
                            if($value){
                                $value = 'true';
                            }else{
                                $value = 'false';
                            }
                        }
                        if($i != count($arr)-1){
                            ++$i;
                            self::$str .= $typeString.self::getSpace($level).'"'.$key.'":'.$value.','."\n";
                        }else{
                            self::$str .= $typeString.self::getSpace($level).'"'.$key.'":'.$value."\n";
                        }
                    }
                }
            }
        }
    }

    /**
     * 获取缩进空格数
     * @param $num
     * @return string
     */
    private static function getSpace($num){
        $str = '';
        for($i = 0; $i < $num; $i++){
            $str .= '    ';
        }
        return $str;
    }

    /**
     * 判断是否是索引数组
     * @param $array
     * @return bool
     */
    private static function isIndexArray($array){
        if(is_array($array)) {
            $keys = array_keys($array);
            return $keys == array_keys($keys);
        }
        return false;
    }

    /**
     * 格式化json
     * @return string
     */
    private static function formatJson(){
        $typeString = self::getFormatType(self::$type);
        return "\n".$typeString." {\n".self::$str.$typeString." }\n";
    }

    /**
     * 获取类型
     * @return string
     */
    private static function getFormatType(){
        $str = '';
        if(self::$type == self::FORMAT_DOC){
            $str = self::STRING;
        }
        return $str;
    }
}
