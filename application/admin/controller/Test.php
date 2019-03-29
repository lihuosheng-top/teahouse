<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/21 0021
 * Time: 11:27
 */
namespace app\admin\controller;

use think\Controller;
use think\Db;

class Test extends  Controller{
    /*图标库*/
    public function selecticon(){
        return $this->fetch('icon');
    }
    /**
     **************李火生*******************
     * @param Request $request
     * Notes:页面选择
     **************************************
     * @return mixed
     */
    public function selectUrl(){
        $uniacid = input('appletid');
        $tplid = input('tplid_only'); //模板id
        if(!$tplid){
            $tplid = Db::table('ims_sudu8_page_diypagetpl')->where("uniacid",$uniacid)->where("status",1)->find()['id'];
        }
        $pageid = explode(",",Db::table('ims_sudu8_page_diypagetpl')->where("uniacid",$uniacid)->where("id",$tplid)->field("pageid")->find()['pageid']); //当前模板拥有的页面id
        $diypage = Db::table('ims_sudu8_page_diypage')->where("uniacid",$uniacid)->where("id","in",$pageid)->field("id,tpl_name")->select();

        $article = Db::table('ims_sudu8_page_products')->where("uniacid",$uniacid)->where("type","showArt")->field("id,title")->select();
        $pro = Db::table('ims_sudu8_page_products')->where("uniacid",$uniacid)->where("type","neq","showArt")->where("type","neq","showPic")->where("type","neq","wxapp")->field("id,title,type,is_more")->select();
        if($pro){
            foreach ($pro as $k => $v) {
                if($v['is_more'] == 1){
                    $pro[$k]['type'] = "showPro_lv";
                }
            }
        }
        $pic = Db::table('ims_sudu8_page_products')->where("uniacid",$uniacid)->where("type","showPic")->field("id,title")->select();
        $cates = Db::table('ims_sudu8_page_cate')->where("uniacid",$uniacid)->where("cid",0)->field("id,name,type")->select();
        if($cates){
            foreach ($cates as $k => $v) {
                if($v['type'] == "showPro"){
                    $cates[$k]['type'] = "listPro";
                }
                if($v['type'] == "showPic" || $v['type'] == "showArt"){
                    $cates[$k]['type'] = "listPic";
                }
                $subcate = Db::table('ims_sudu8_page_cate')->where("uniacid",$uniacid)->where("cid",$v['id'])->field("id,name,type")->select();

                foreach ($subcate as $ki=> $vi) {
                    if($vi['type'] == "showPro"){
                        $subcate[$ki]['type'] = "listPro";
                    }
                    if($vi['type'] == "showPic" || $vi['type'] == "showArt"){
                        $subcate[$ki]['type'] = "listPic";
                    }
                }
                $cates[$k]['subcate'] = $subcate;
            }

        }

        $this->assign("diypage",$diypage);
        $this->assign("article",$article);
        $this->assign("pro",$pro);
        $this->assign("pic",$pic);
        $this->assign("cates",$cates);
        $this->assign("uniacid",$uniacid);
        return $this->fetch('selecturl');
    }

}