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
    /**
     * 一级评论
     * @author fyk
     * @time   2019/08/27
     */
    public function video($store_id,$vid)
    {
        return self::all(['store_id'=>$store_id,'topic_id'=>$vid,'is_look'=>1,'to_uid' => 0,'status'=>1])
                -> toArray();

    }
    /**
     * 二级评论
     * @author fyk
     * @time   2019/08/27
     */
    public function level($store_id,$vid,$tid)
    {
        return self::all(['store_id'=>$store_id,'topic_id'=>$vid,'to_uid'=>$tid,'is_look'=>1,'status'=>1])
            -> toArray();

    }

    /**
     * 评论总数
     * @author fyk
     * @time   2019/08/27
     */
    public function comment_count($sid,$vid)
    {
        $data = VideoComment::where(['store_id'=>$sid,'topic_id'=>$vid]) -> count();
        return $data;
    }
}