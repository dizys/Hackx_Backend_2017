<?php
/**
 * Created by PhpStorm.
 * User: 蛟川小盆友
 * Date: 2017/11/4
 * Time: 17:38
 */

namespace app\api\controller;


use app\base\controller\Api;
use think\Db;

class AndroidClient extends Api
{
    public function selectDishByUser(){
        $res=Db::name('dishes')
            ->where('name','in',function($query){
                $query->name('user_dish')
                    ->where("user_id",input("post.userid"))
                    ->field('dish_name');
            })
            ->select();
        return s([
            'ret' => $res
        ]);
    }
}