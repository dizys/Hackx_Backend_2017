<?php
/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2017/11/4
 * Time: 2:47
 */

namespace app\api\controller;


use app\base\controller\Api;
use app\base\model\MsgStream;

class ManageClient extends Api
{
    public function read_from_window(){
        if($this->checkAccess()){
            return e('access denied');
        }
        $msgStream = new MsgStream();
        $streams = $msgStream->where(['type'=>0, 'status'=>0])->field('id, data')->select();
        foreach ($streams as &$stream){
            $stream->status = 1;
            $stream->save();
        }
        return s($streams);
    }

    public function send_to_window(){
        if($this->checkAccess()){
            return e('access denied');
        }
        $msgStream = new MsgStream();
        $msgStream->type = 1;
        $msgStream->status = 0;
        $msgStream->data = input('post.data');
        $msgStream->save();
        return s('OK');
    }

}