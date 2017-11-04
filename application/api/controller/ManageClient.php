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

    public function face_set()
    {
        $id=$_POST["id"];
        $name=$_POST["name"];
        $balance=$_POST["balance"];
        $phone=$_POST["phone"];
        $data=['id'=>$id,
            'name'=>$name,
            'balance'=>$balance,
            'phone'=>$phone
        ];
        Db::name('user')->insert($data);
        $data=new FaceSet();
        $ret = $data->run(input("post.id"),input("post.image"));
        return s([
            'ret' => $ret
        ]);
    }


    public function face_identify()
    {
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