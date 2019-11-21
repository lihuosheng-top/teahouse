<?php

namespace app\api\controller;
use think\Controller;
use think\Cache;


/**
 * 微信模板消息
 * Class WxTplMsg
 * @package app\api\controller
 */
class WxTplMsg extends Controller
{

    protected $appId;
    protected $appSecret;

    protected $error;

    /**
     * 构造函数
     * WxBase constructor.
     * @param $appId
     * @param $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
    }

    /**
     * 写入日志记录
     * @param $values
     * @return bool|int
     */
    protected function doLogs($values)
    {
        return log_write($values);
    }

    /**
     * 发送模板消息
     * @param array $param
     * @return bool
     * @throws \app\common\exception\BaseException
     */
    public function sendTemplateMessage($param)
    {
        // 微信接口url
        $accessToken = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token={$accessToken}";
        // 构建请求
        $params = [
            'touser' => $param['touser'],
            'template_id' => $param['template_id'],
            'page' => $param['page'],
            'form_id' => $param['form_id'],
            'data' => $this->createData($param['data'])
        ];
        $result = $this->post($url, $this->jsonEncode($params));
        // 记录日志
        $this->doLogs(['describe' => '发送模板消息', 'url' => $url, 'params' => $params, 'result' => $result]);
        // 返回结果
        $response = $this->jsonDecode($result);
        if (!isset($response['errcode'])) {
            $this->error = 'not found errcode';
            return false;
        }
        if ($response['errcode'] != 0) {
            $this->error = $response['errmsg'];
            return false;
        }
        return true;
    }

        /**
     * 获取access_token
     * @return mixed
     * @throws BaseException
     */
    protected function getAccessToken()
    {
        $cacheKey = $this->appId . '@access_token';
        if (!Cache::get($cacheKey)) {
            // 请求API获取 access_token
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appId}&secret={$this->appSecret}";
            $result = $this->get($url);
            $data = $this->jsonDecode($result);
            if (array_key_exists('errcode', $data)) {
                throw new BaseException(['msg' => "access_token获取失败，错误信息：{$result}"]);
            }
            // 记录日志
            $this->doLogs([
                'describe' => '获取access_token',
                'url' => $url,
                'appId' => $this->appId,
                'result' => $result
            ]);
            // 写入缓存
            Cache::set($cacheKey, $data['access_token'], 6000);    // 7000
        }
        return Cache::get($cacheKey);
    }

    /**
     * 生成关键字数据
     * @param $data
     * @return array
     */
    private function createData($data)
    {
        $params = [];
        foreach ($data as $key => $value) {
            $params[$key] = [
                'value' => $value,
                'color' => '#333333'
            ];
        }
        return $params;
    }


    /**
     * 模拟GET请求 HTTPS的页面
     * @param string $url 请求地址
     * @return string $result
     */
    protected function get($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    /**
     * 模拟POST请求
     * @param $url
     * @param array $data
     * @param bool $useCert
     * @param array $sslCert
     * @return mixed
     */
    protected function post($url, $data = [], $useCert = false, $sslCert = [])
    {
        $header = [
            'Content-type: application/json;'
        ];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        if ($useCert == true) {
            // 设置证书：cert 与 key 分别属于两个.pem文件
            curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'PEM');
            curl_setopt($curl, CURLOPT_SSLCERT, $sslCert['certPem']);
            curl_setopt($curl, CURLOPT_SSLKEYTYPE, 'PEM');
            curl_setopt($curl, CURLOPT_SSLKEY, $sslCert['keyPem']);
        }
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    /**
     * 数组转json
     * @param $data
     * @return string
     */
    protected function jsonEncode($data)
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * json转数组
     * @param $json
     * @return mixed
     */
    protected function jsonDecode($json)
    {
        return json_decode($json, true);
    }

    /**
     * 返回错误信息
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }


}