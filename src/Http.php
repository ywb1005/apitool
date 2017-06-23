<?php
/**
 * Created by PhpStorm.
 * User: xz
 * Date: 2017/05/10
 * Time: 9:19
 */
class Http
{
    //请求的资源地址
    protected $url;

    //curl资源
    private $ch;

    //会话设置数组
    private $options = [];

    //请求方式
    private $method;

    //请求协议
    private $isHttps;

    //url带的参数
    private $urlParams;

    //post参数
    private $params;

    //请求头
    private $header;

    public function __construct($data){
        $this->ch       = curl_init();
        $this->parseParams($data);
        $this->isHttps  = $this->getProtocol();
        $this->options  = $this->setOptions();
    }

    /**
     * 获取请求协议
     * @return bool
     */
    private function getProtocol(){
        $str = substr($this->url, 0, 5);
        if($str == 'https'){
            return true;
        }
        return false;
    }

    /**
     * 设置会话参数
     * @return array
     */
    private function setOptions(){
        $option = [
            CURLOPT_URL => $this->url.'?'.http_build_query($this->urlParams),
            CURLOPT_RETURNTRANSFER => 1
        ];
        switch ($this->method){
            case 'get':
                $option = $option + [
                        CURLOPT_HEADER => 0
                    ];
                break;
            case 'post':
                $option = $option + [
                    CURLOPT_POST => 1,
                    CURLOPT_POSTFIELDS => $this->params
                ];
                break;
            case 'put':
                $option = $option + [

                ];
                break;
            case 'delete':
                $option = $option + [

                ];
                break;
        }
        if($this->isHttps){
            $option = $option + [
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ];
        }
        return $option;
    }

    /**
     * 模拟请求
     * @return mixed
     */
    public function http_req(){
        curl_setopt_array($this->ch, $this->options);
        $content = curl_exec($this->ch);
        if(!$content){
            return curl_error($this->ch);
        }
        curl_close($this->ch);
        return $content;
    }

    /**
     * 解析参数
     * @param $data
     */
    private function parseParams($data){
        foreach ($data as $key => $value){
            switch ($key){
                case 'url':
                    $this->url = $value;
                    break;
                case 'method':
                    $this->method = $value;
                    break;
                case 'urlParams':
                    $this->urlParams = $value;
                    break;
                case 'params':
                    $this->params = $value;
                    break;
                case 'header':
                    $this->header = $value;
                    break;
            }
        }
    }
}

//$req = new http($data);
//$arr = json_decode($req->http_req(), true);
//$str = doc::create($arr);
//echo $str;
