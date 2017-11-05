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
use app\base\model\User;

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

    public function face_set()
    {
        if($this->checkAccess()){
            return e('access denied');
        }
        $name=input("post.name");
        $balance=input("post.balance");
        $phone=input("post.phone");
        $user = new User();
        $user->name = $name;
        $user->balance = $balance;
        $user->phone = $phone;
        $user->save();
        $data=new FaceSet();
        $ret = $data->run($user->id,input("post.image"));
        return s([
            'ret' => $ret
        ]);
    }

    public function set_balance(){
        if($this->checkAccess()){
            return e('access denied');
        }
        $id = input("post.name");
        $name=input("post.name");
        $balance=input("post.balance");
        $userModel = new User();
        $user = $userModel->where("id = '$id' or name = '$name'")->find();
        if(is_null($user)){
            return e("user not found", 1);
        }
        $user->balance = $balance;
        $user->save();
        return s("OK");
    }


    public function face_identify()
    {
        if($this->checkAccess()){
            return e('access denied');
        }
        $data=new FaceIdentify();
        $ret = $data->run(input("post.image"));
        $id=$ret['result'][0]['uid'];
        $data = Db::name('user')
            ->where("id",$id)
            ->select();
        $name=$data[0]['name'];
        $balance=$data[0]['balance'];
        $phone=$data[0]['phone'];
        return s([
            'name' => $name,
            'balance'=>$balance,
            'phone'=>$phone
        ]);
    }

}