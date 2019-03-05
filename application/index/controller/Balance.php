<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/18 0018
 * Time: 14:04
 * 余额支付
 */
namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Db;
use think\Session;
use app\index\controller\Xgcontent;

class Balance extends Controller
{
    //商品余额支付
    public function balance_payment(Request $request)
    {
        if ($request->isPost()) {
            $order_num = $request->only(['order_num'])['order_num'];
            //验证支付密码
            $user_id = $request->only(["member_id"])["member_id"];
            $user_info = Db::name("member")
                ->field("pay_password,member_wallet")
                ->where("id", $user_id)
                ->find();//用户信息
            $password = $request->only("passwords")["passwords"]; //输入的密码
            if (password_verify($password,$user_info["pay_password"])) {
                //真实支付的价钱
                $money = Db::name("order")->where("parts_order_number", $order_num)->sum("order_amount");
                if(!empty($money)){
                    $money =round($money,2);
                }else{
                    $money =0;
                }
                        $user_wallet = $user_info["member_wallet"];//升值进来的钱
                        if ($money > $user_wallet) {
                                $member_recharge_money =$user_info["member_recharge_money"];//充值进来的钱
                        } else {
                            $select_data = Db::name("order")
                                ->where("parts_order_number", $order_num)
                                ->select();
                            //对订单状态进行修改
                            $data['status'] = 2;
                            $data["pay_time"] = time();
                            $data['pay_type_content'] = "余额支付";
                            $result= Db::name("order")
                                    ->where("parts_order_number",$order_num)
                                    ->update(["status"=>2,"pay_time"=>time()]);
                            //如果修改成功则进行钱抵扣
                            if ($result > 0) {
                                return ajax_success('支付成功', ['status' => 1]);
                            } else {
                                return ajax_error('验证失败了', ['status' => 0]);
                            }
                        }
                }else {
                    return ajax_error('密码错误', ['status' => 0]);
                }
            }
        }



    /**
     **************李火生*******************
     * @param Request $request
     * Notes:校验支付密码
     **************************************
     */
    public function check_password(Request $request){
        //验证支付密码
        $user_id = $request->only(["member_id"])["member_id"];
        $user_info = Db::name("member")
            ->field("pay_password")
            ->where("member_id", $user_id)
            ->find();//用户信息
        $password = $request->only("passwords")["passwords"]; //输入的密码
        if (password_verify($password,$user_info["pay_password"])){
            return ajax_success("支付密码正确",["status"=>1]);
        }else{
            return ajax_error("支付密码错误",["status"=>0]);
        }
    }

}