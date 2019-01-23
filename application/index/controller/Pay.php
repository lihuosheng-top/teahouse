<?php

namespace app\index\controller;
use think\Controller;
use think\Request;
use  think\Db;

include('../extend/WxpayAPI/lib/WxPay.Api.php');
include('../extend/WxpayAPI/example/WxPay.NativePay.php');
include('../extend/WxpayAPI/lib/WxPay.Notify.php');
//include('../extend/WxpayAPI/lib/WxPay.Data.php');
include('../extend/WxpayAPI/example/log.php');

class Pay extends  Controller{
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:小程序活动支付
     **************************************
     * @param Request $request
     */
    function index(Request $request) {
        $open_ids = $request->param("open_id");//open_id
        $activity_name = $request->param("activity_name");//名称
        $cost_moneny = $request->param("cost_moneny");//金额
        $order_numbers =$request->param("order_number");//订单编号
        //         初始化值对象
        $input = new \WxPayUnifiedOrder();
        //         文档提及的参数规范：商家名称-销售商品类目
        $input->SetBody($activity_name);
        //         订单号应该是由小程序端传给服务端的，在用户下单时即生成，demo中取值是一个生成的时间戳
//        $input->SetOut_trade_no(time().'');
        $input->SetOut_trade_no($order_numbers);
        //         费用应该是由小程序端传给服务端的，在用户下单时告知服务端应付金额，demo中取值是1，即1分钱
        $input->SetTotal_fee($cost_moneny*100);
        $input->SetNotify_url("https://teahouse.siring.com.cn/notify.php");//需要自己写的notify.php
        $input->SetTrade_type("JSAPI");
        //         由小程序端传给后端或者后端自己获取，写自己获取到的，
        $input->SetOpenid( $open_ids);
        //$input->SetOpenid($this->getSession()->openid);
        //         向微信统一下单，并返回order，它是一个array数组
        $order = \WxPayApi::unifiedOrder($input);
        //         json化返回给小程序端
        header("Content-Type: application/json");
        echo $this->getJsApiParameters($order);
    }
    private function getJsApiParameters($UnifiedOrderResult)
    {    //判断是否统一下单返回了prepay_id
        if(!array_key_exists("appid", $UnifiedOrderResult)
            || !array_key_exists("prepay_id", $UnifiedOrderResult)
            || $UnifiedOrderResult['prepay_id'] == "")
        {
            throw new \WxPayException("参数错误");
        }
        $jsapi = new \WxPayJsApiPay();
        $jsapi->SetAppid($UnifiedOrderResult["appid"]);
        $timeStamp = time();
        $jsapi->SetTimeStamp("$timeStamp");
        $jsapi->SetNonceStr(\WxPayApi::getNonceStr());
        $jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);
        $jsapi->SetSignType("MD5");
        $jsapi->SetPaySign($jsapi->MakeSign());
        $parameters = json_encode($jsapi->GetValues());
        return $parameters;
    }
//这里是服务器端获取openid的函数
//    private function getSession() {
//        $code = $this->input->post('code');
//        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.WxPayConfig::APPID.'&secret='.WxPayConfig::APPSECRET.'&js_code='.$code.'&grant_type=authorization_code';
//        $response = json_decode(file_get_contents($url));
//        return $response;
//    }

    /**
     **************李火生*******************
     * @param Request $request
     * Notes:小程序活动支付成功回来修改状态
     **************************************
     */
    public function notify(Request $request){
        $input = new \WxPayOrderQuery();
        $input->SetTransaction_id();
        $result = \WxPayApi::orderQuery($input);
        \Log::DEBUG("query:" . json_encode($result));
        if(array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS")
        {
            Db::name("activity_order")->where("parts_order_number",$result["out_trade_no"])->update(["status"=>1]);
            Db::name("activity_order")->where("parts_order_number",$result["transaction_id"])->update(["status"=>1]);
        }else{
            Db::name("activity_order")->where("parts_order_number",$result["out_trade_no"])->delete();
            Db::name("activity_order")->where("parts_order_number",$result["transaction_id"])->delete();
        }
    }

//    public function Queryorder($transaction_id)
//    {
//        $input = new \WxPayOrderQuery();
//        $input->SetTransaction_id($transaction_id);
//        $result = \WxPayApi::orderQuery($input);
//        if(array_key_exists("return_code", $result)
//            && array_key_exists("result_code", $result)
//            && $result["return_code"] == "SUCCESS"
//            && $result["result_code"] == "SUCCESS")
//        {
//            Db::name("activity_order")->where("parts_order_number",$result["out_trade_no"])->update(["status"=>1]);
//            return true;
//        }
//        return false;
//    }
//
//
//    public function notify($data, &$msg)
//    {
//        Db::name("activity_order")->where("parts_order_number","20190123146389")->update(["status"=>1]);
//        $notfiyOutput = array();
//        if(!array_key_exists("transaction_id", $data)){
//            $msg = "输入参数不正确";
//            return false;
//        }
//        //查询订单，判断订单真实性
//        if(!$this->Queryorder($data["transaction_id"])){
//            $msg = "订单查询失败";
//            return false;
//        }
//        Db::name("activity_order")->where("parts_order_number","20190123146389")->update(["status"=>1]);
//        return true;
//    }






}