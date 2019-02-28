<?php

/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/2/21
 * Time: 15:21
 */
namespace app\index\controller;

use think\console\Input;
use think\Controller;
use think\Db;
use think\Request;
use think\Image;


class Manage extends Controller
{



    /**
     * [问题列表]
     * 郭杨
     */
    public function problem_list()
    {

        $list = Db::name("problem")->select();
        if (!empty($list)) {
            return ajax_success('传输成功', $list);
        } else {
            return ajax_error("数据为空");

        }


        

    }


    /**
     * [问题列表描述]
     * 郭杨
     */
    public function problem_data(Request $request)
    {
        if ($request->isPost()) {
            $pid = $request->only(["pid"])["pid"];
            $problem_data = db("common_ailment")->where("pid",$pid)->select();
            if (!empty($problem_data)) {
                return ajax_success('传输成功', $problem_data);
            } else {
                return ajax_error("数据为空");

            }

        }

    }



    /**
     * [协议合同显示]
     * 郭杨
     */
    public function agreement_contract()
    {     
        $protocol = Db::name("protocol")->select();
        if (!empty($protocol)) {
            return ajax_success('传输成功', $protocol);
        } else {
            return ajax_error("数据为空");

        }

    }


    /**
     * [消息提醒]
     * 郭杨
     */
    public function message_reminder()
    {     
        $reminder = Db::name("remind")->select();
        if (!empty($reminder)) {
            return ajax_success('传输成功', $reminder);
        } else {
            return ajax_error("数据为空");

        }

    }


    /**
     * [关于我们]
     * 郭杨
     */
    public function about_us()
    {     
        $about = Db::name("about_us")->select();
        if (!empty($about)) {
            return ajax_success('传输成功', $about);
        } else {
            return ajax_error("数据为空");

        }

    }


     /**
     * [茶圈收藏列表详细]
     * 郭杨
     */
    public function enshrine_data(Request $request)
    {
        if ($request->isPost()) {
            $member_id = $request->only(["member_id"])["member_id"];
            $activity_id = db("enshrine")->where("member_id",$member_id)->where("type",1)->field("activity_id")->select();
            
            if(!empty($activity_id)){
                foreach($activity_id as $k => $val)
                {       
                    $activity[$k] = Db::name("teahost")->field('id,activity_name,classify_image,cost_moneny,start_time,commodity,label,marker,participats,peoples,address,pid')->where("id",$val["activity_id"])->where("label", 1)->order("start_time")->find();
                }              
                foreach($activity as $key => $value){      
                    $rest = db("goods_type")->where("id", $value["pid"])->field("name,pid,icon_image")->find();
                    $retsd = db("goods_type")->where("id",$rest["pid"])->field("name,color")->find();
                    $activity[$key]["names"] = $rest["name"];
                    $activity[$key]["named"] = $retsd["name"];
                    $activity[$key]["color"] = $retsd["color"];
                    $activity[$key]["icon_image"] = $rest["icon_image"];
                    $activity[$key]["start_time"] = date('Y-m-d H:i',$activity[$key]["start_time"]);                   
                }
            } 
            if (!empty($activity)) {
                return ajax_success('传输成功', $activity);
            } else {
                return ajax_error("数据为空");

            }

        }

    }


    /**
     * [添加茶圈收藏]
     * 郭杨
     */
    public function collect(Request $request)
    {
        if ($request->isPost()) {
            $data["member_id"] = $request->only(["member_id"])["member_id"];
            $data["activity_id"] = $request->only(["activity_id"])["activity_id"];
            $data["type"] = 1;
            $bools = db("enshrine")->insert($data);
            if (!empty($bools)) {
                return ajax_success('添加成功',1);
            } else {
                return ajax_error("添加失败",0);

            }

        }

    }



    /**
     * [供求收藏列表详细]
     * 郭杨
     */
    public function demand_data(Request $request)
    {
        if ($request->isPost()) {
            $member_id = $request->only(["member_id"])["member_id"];
            $demand_data = db("enshrine")->where("member_id",$member_id)->where("type",2)->field("activity_id")->select();
            if (!empty($demand_data)) {
                return ajax_success('传输成功', $demand_data);
            } else {
                return ajax_error("数据为空");

            }

        }

    }


    /**
     * [添加供求收藏]
     * 郭杨
     */
    public function demand_collect(Request $request)
    {
        if ($request->isPost()) {
            $data["member_id"] = $request->only(["member_id"])["member_id"];
            $data["activity_id"] = $request->only(["activity_id"])["activity_id"];
            $data["type"] = $request->only(["type"])["type"];
            $bools = db("enshrine")->insert($data);
            if (!empty($bools)) {
                return ajax_success('添加成功',1);
            } else {
                return ajax_error("添加失败",0);

            }

        }

    }
}