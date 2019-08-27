<?php
namespace app\api\model;

use think\Model;

class VideoComment extends Model
{
    protected $table = "tb_video_comment";
    protected $resultSetType = 'collection';

    public function validateFailException($fail = true)
    {
        return parent::validateFailException($fail); // TODO: Change the autogenerated stub
    }

    //一级评论
    public function video($store_id,$vid)
    {
        return self::all(['store_id'=>$store_id,'topic_id'=>$vid,'is_look'=>1,'to_uid' => 0,'status'=>1])
                -> toArray();

    }
    //二级评论
    public function level($store_id,$vid,$tid)
    {
        return self::all(['store_id'=>$store_id,'topic_id'=>$vid,'to_uid'=>$tid,'is_look'=>1,'status'=>1])
            -> toArray();

    }
}