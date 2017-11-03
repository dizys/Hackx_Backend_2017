<?php
/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2017/11/4
 * Time: 1:30
 */

namespace app\base\controller;


use think\Controller;

class Api extends Controller
{
    protected function _initialize()
    {

    }

    protected function checkAccess(){
        if(input('post.appkey_id') == config('app.appkey_id') && input('post.appkey_secret') == config('app.appkey_secret')){
            return false;
        }
        return true;
    }

}