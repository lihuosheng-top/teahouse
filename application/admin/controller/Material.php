<?php
/**
 * Created by PhpStorm.
 * User: GY
 * Date: 2019/2/20
 */
namespace  app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\paginator\driver\Bootstrap;
use think\Session;
use think\View;


class  Material extends  Controller{


    /**
     **************GY*******************
     * @param Request $request
     * Notes:视频直播
     **************************************
     * @return \think\response\View
     */
    public function direct_seeding(){
        $store_id = Session::get("store_id");
        $data = Db::name("video_frequency")->where("store_id",$store_id)->paginate(20 ,false, [
            'query' => request()->param(),
        ]);
        return view("direct_seeding",["data"=>$data]);
    }

    /**
     **************GY*******************
     * @param Request $request
     * Notes:视频直播添加编辑设备
     **************************************
     */
    public  function  direct_seeding_add(Request $request){
        if($request->isPost()){
            $store_id = Session::get("store_id");
            $data = $request->param();
            $data['store_id'] = $store_id;
            $bool = Db::name("video_frequency")->insert($data);
            if ($bool) {
                $this->success("添加成功", url("admin/Material/direct_seeding"));
            } else {
                $this->error("添加失败", url("admin/Material/direct_seeding"));
            }
        }
        $store_id = Session::get("store_id");
        $store_name = Db::name("store_house")->where("store_id",$store_id)->select(); //仓库
        $direct = Db::name("direct_seeding")->where("store_id",$store_id)->where("status",1)->select();  //分类
        return  view("direct_seeding_add",["store_name"=>$store_name,"direct"=>$direct]);
    }


        /**
     **************GY*******************
     * @param Request $request
     * Notes:视频直播编辑设备
     **************************************
     */
    public  function  direct_seeding_edit($id){
        $data = Db::name("video_frequency")->where("id",$id)->select();
        $store_id = Session::get("store_id");
        $store_name = Db::name("store_house")->where("store_id",$store_id)->select(); //仓库
        $direct = Db::name("direct_seeding")->where("store_id",$store_id)->select();  //分类
        return  view("direct_seeding_edit",["store_name"=>$store_name,"direct"=>$direct,"data"=>$data]);
    }

    /**
     **************GY*******************
     * @param Request $request
     * Notes:视频直播更新设备
     **************************************
     */
    public  function  direct_seeding_update(Request $request){
        if($request->isPost()){
        $data = $request->param();
        $bool = Db::name("video_frequency")->where("id",$data['id'])->update($data);
        if ($bool) {
            $this->success("更新成功", url("admin/Material/direct_seeding"));
        } else {
            $this->error("更新失败", url("admin/Material/direct_seeding"));
        }
    }
        
    }

        /**
     **************GY*******************
     * @param Request $request
     * Notes:视频直播更新设备
     **************************************
     */
    public  function  direct_seeding_delete($id){
        $bool = Db::name("video_frequency")->where("id",$id)->delete();
        if ($bool) {
            $this->success("删除成功", url("admin/Material/direct_seeding"));
        } else {
            $this->error("删除失败", url("admin/Material/direct_seeding"));
        }      
    }



    /**
     **************GY*******************
     * @param Request $request
     * Notes:直播分类
     **************************************
     */
    public function direct_seeding_classification(){
        $store_id = Session::get("store_id");
        $direct_data = Db::name("direct_seeding") 
                ->where("store_id",$store_id)
                ->select();
        $url = 'admin/Material/direct_seeding_classification';
        $pag_number = 20;
        $data = paging_data($direct_data,$url,$pag_number);
        return view("direct_seeding_classification",["data"=>$data]);
    }

    /**
     **************GY******************* 
     * @param Request $request
     * Notes:直播分类添加保存
     **************************************
     * @return \think\response\View
     */
    public function direct_seeding_classification_add(Request $request){
        if($request->isPost()){
            $store_id = Session::get("store_id");
            $data = input();
            $data['status'] = isset($data['status'])?$data['status']:0;
            $data['store_id'] = $store_id;
            $show_images = $request->file("icon_image");
            if ($show_images) {
                $show_images = $request->file("icon_image")->move(ROOT_PATH . 'public' . DS . 'uploads');
                $data["icon_image"] = str_replace("\\", "/", $show_images->getSaveName());
            }
            if(empty($data['title']) || empty($data['icon_image'])){
                $this->error("请仔细填写", url("admin/Material/direct_seeding_classification"));
            }
            
            $bool = Db::name("direct_seeding")->insert($data);
            if ($bool) {
                $this->success("添加成功", url("admin/Material/direct_seeding_classification"));
            } else {
                $this->error("添加失败", url("admin/Material/direct_seeding_classification"));
            }
        }
        return view("direct_seeding_classification_add");
        
    }


    /**
     **************GY******************* 
     * @param Request $request
     * Notes:直播分类编辑
     **************************************
     * @return \think\response\View
     */
    public function direct_seeding_classification_edit($id){
        $store_id = Session::get("store_id");
        $bool = Db::name("direct_seeding")->where("id",$id)->select();
        return view("direct_seeding_classification_edit",['bool'=>$bool]);
    }

    /**
     **************GY******************* 
     * @param Request $request
     * Notes:直播分类删除
     **************************************
     * @return \think\response\View
     */
    public function direct_seeding_classification_delete($id){

        $store_id = Session::get("store_id");
        $bool = Db::name("direct_seeding")->where("id",$id)->delete();
        if ($bool) {
            $this->success("删除成功", url("admin/Material/direct_seeding_classification"));
        } else {
            $this->error("删除失败", url("admin/Material/direct_seeding_classification"));
        }
    }

    /**
     **************GY******************* 
     * @param Request $request
     * Notes:直播分类更新
     **************************************
     * @return \think\response\View
     */
    public function direct_seeding_classification_update(Request $request){
        if($request->isPost()){
            $data = input();
            $id = $request->only(["id"])["id"];
            $data['status'] = isset($data['status'])?$data['status']:0;
            $show_images = $request->file("icon_image");
            if ($show_images) {
                $show_images = $request->file("icon_image")->move(ROOT_PATH . 'public' . DS . 'uploads');
                $data["icon_image"] = str_replace("\\", "/", $show_images->getSaveName());
            }
            $bool = Db::name("direct_seeding")->where("id",$id)->update($data);
            if ($bool) {
                $this->success("更新成功", url("admin/Material/direct_seeding_classification"));
            } else {
                $this->error("未更改数据", url("admin/Material/direct_seeding_classification"));
            }
        }
        
    }
       
    /**
     * [图片删除]
     * 郭杨
     */
    public function direct_seeding_classification_delete_image(Request $request)
    {
        if ($request->isPost()) {
            $id = $request->only(['id'])['id'];
            $image_url = db("direct_seeding")->where("id", $id)->field("icon_image")->find();
            if ($image_url['icon_image'] != null) {
                unlink(ROOT_PATH . 'public' . DS . 'uploads/' . $image_url['icon_image']);
            }
            $bool = db("direct_seeding")->where("id", $id)->field("icon_image")->update(["icon_image" => null]);
            if ($bool) {
                return ajax_success("删除成功");
            } else {
                return ajax_error("删除失败");
            }
        }
    }

    /**
     **************GY*******************
     * @param Request $request
     * Notes:防伪溯源
     **************************************
     * @return \think\response\View
     */
    public function anti_fake(){
        return view("anti_fake");
    }

    /**
     **************GY*******************
     * @param Request $request
     * Notes:温湿传感
     **************************************
     * @return \think\response\View
     */
    public function interaction_index(){
        return view("interaction_index");
    }

    /**
     **************GY*******************
     * @param Request $request
     * Notes:温湿传感添加编辑
     **************************************
     * @return \think\response\View
     */
    public function interaction_add(){
        return view("interaction_add");
    }

    
 }