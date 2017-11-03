<?php
/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2017/11/4
 * Time: 2:26
 */

namespace app\api\controller;


use app\base\controller\Api;
use app\base\model\MsgStream;

class WindowClient extends Api
{
    public function read_from_manage(){
        if($this->checkAccess()){
            return e('access denied');
        }
        $msgStream = new MsgStream();
        $streams = $msgStream->where(['type'=>1, 'status'=>0])->field('id, data')->select();
        foreach ($streams as &$stream){
            $stream->status = 1;
            $stream->save();
        }
        return s($streams);
    }

    public function send_to_manage(){
        if($this->checkAccess()){
            return e('access denied');
        }
        $msgStream = new MsgStream();
        $msgStream->type = 0;
        $msgStream->status = 0;
        $msgStream->data = input('post.data');
        $msgStream->save();
        return s('OK');
    }

    public function get_dish_info(){
        if($this->checkAccess()){
            return e('access denied');
        }
        $inputs = input('post.');
        $dish_image = $inputs['dish_image'];
    }
}