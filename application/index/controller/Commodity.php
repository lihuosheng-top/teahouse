<?php
namespace app\index\controller;
use think\Controller;
use think\Request;

class Commodity extends Controller
{

    /**
     * 商品分类 
     * GY
     */
    public function commodity_index(Request $request)
    {
        if($request->isPost()) {            
            $goods_type = db("wares")->where("status", 1)->select();
            $goods_type = _tree_sort(recursionArr($goods_type), 'sort_number');
            foreach($goods_type as $key => $value)
            {
                $goods_type[$key]['child'] = db("goods")->where("pid",$goods_type[$key]['id'])->where("label",1)->select();
 
            }
            return ajax_success("获取成功",array("goods_type"=>$goods_type));
        }
        
    }





    /**
     * 分类推荐
     * GY
     */
    public function classify_recommend()
    {
        return view("classify_recommend");
    }




    /**
     * 商品列表
     * GY
     */
    public function commodity_list(Request $request)
    {

        if($request->isPost()){
            $goods_pid = $request->only(["id"])["id"];
            $goods = db("goods")->where("pid",$goods_pid)->select();

            foreach ($goods as $k => $v)
            {
                if($v["label"] == 1 ){
                    $goods_data[] = $v;
                    $goods_data[$k]["goods_show_images"] = (explode(",", $goods[$k]["goods_show_images"])[0]);

                }
 
            }
           
            if(!empty($goods_data) && !empty($goods_pid)){
                return ajax_success("获取成功",$goods_data);
            }else{
                return ajax_error("获取失败");
            }
        }

    }



    /**
     * 商品详情
     * GY
     */
    public function commodity_detail(Request $request)
    {
        if($request->isPost()){
            $goods_id = $request->only(["id"])["id"];  
            $goods = db("goods")->where("id",$goods_id)->select();
            $goods_standard = db("special")->where("goods_id", $goods_id)->select();
            $max_price =  db("special")->where("goods_id", $goods_id)->max("price");
            $min_price =  db("special")->where("goods_id", $goods_id)->min("price");


            if($goods[0]["goods_standard"] == 1){           
                $goods[0]["goods_standard"] = $goods_standard;
                $goods[0]["goods_show_image"] = (explode(",", $goods[0]["goods_show_images"])[0]);
                $goods[0]["goods_show_images"] = (explode(",", $goods[0]["goods_show_images"]));
                $goods[0]["max_price"] = $max_price;
                $goods[0]["min_price"] = $min_price;
            } else {
                $goods[0]["goods_show_image"] = (explode(",", $goods[0]["goods_show_images"])[0]);
                $goods[0]["goods_show_images"] = (explode(",", $goods[0]["goods_show_images"]));
            }
            if(!empty($goods) && !empty($goods_id)){
                return ajax_success("获取成功",$goods);
            }else{
                return ajax_error("获取失败");
            }
        }
        
    }
}
