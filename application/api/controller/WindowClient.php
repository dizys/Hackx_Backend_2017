<?php
/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2017/11/4
 * Time: 2:26
 */

namespace app\api\controller;


use app\base\controller\Api;
use app\base\model\Dishes;
use app\base\model\MsgStream;
use BaiduAI\FaceIdentify;
use MicrosoftAI\FaceDetect;
use think\Db;

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
        $gdname = controller("GuessDishName", "domain");
        $calorie = null;
        $name = $gdname->guess($dish_image, $calorie);
        if($name == false || is_null($name)){
            return e('cant recognize',1);
        }
        if($name == "非菜"){
            return e('cant recognize',1);
        }
        $dish = Dishes::get(['name'=>$name]);
        if(is_null($dish)){
            return e($name,2);
        }
        return s([
            'name'=>$name,
            'price'=>$dish->price,
            'calorie'=>$calorie,
            'protein'=>$dish->protein,
            'fat'=>$dish->fat,
            'carbohydrate'=>$dish->carbohydrate
        ]);
    }

    public function charge()
    {
        if($this->checkAccess()){
            return e('access denied');
        }
        $data=new FaceIdentify();
        $ret = $data->run(input("post.user_image"));
        if(is_null($ret) || !is_array($ret) || count($ret)==0){
            return e("cant recognize", 1);
        }
        $id=$ret['result'][0]['uid'];
        $data = Db::name('user')
            ->where("id",$id)
            ->select();
        if(count($data)==0){
            return e("cant recognize", 2);
        }
        $balance=$data[0]['balance'];
        $charge=input("post.charge");
        $balance_after=$balance-$charge;
        if($balance_after<0){
            return e("no money",3);
        }
        Db::name('user')
            ->where("id",$id)
            ->update(['balance'=>$balance_after]);
        $data = Db::name('user')
            ->where("id",$id)
            ->select();
        $name=$data[0]['name'];
        $balance=$data[0]['balance'];
        $phone=$data[0]['phone'];

        $prefilename = date('Ymd',time())."/";
        $new_file = ROOT_PATH . 'public' . DS . 'uploads'. DS .'users' . DS . $prefilename;
        if(!file_exists($new_file))
        {
            //检查是否有该文件夹，如果没有就创建，并给予最高权限
            mkdir($new_file, 0700);
        }
        $filename = time().".jpg";
        $new_file = $new_file.$filename;
        $res = file_put_contents($new_file, base64_decode(input('post.user_image')));
        if (!$res){
            return e("cant save file", 4);
        }
        $fd = new FaceDetect();
        //$res = $fd->run(config('app.uploads_users_url').$prefilename.$filename);
        $res = $fd->run("http://n1.itc.cn/img8/wb/recom/2016/06/20/146643163192078632.JPEG");
        if(is_null($res) || isset($res->error)){
            return e("cant recognize", 5);
        }
        if(!is_array($res) || count($res)==0){
            return e("cant recognize", 6);
        }
        $emotion = runEmotion($res[0]->faceAttributes->emotion);
        unlink($new_file);
        return s([
            'name' => $name,
            'balance'=>$balance,
            'phone'=>$phone,
            'emotion'=>$emotion,
            'emotion_opts'=>$res[0]->faceAttributes->emotion
        ]);
    }

}