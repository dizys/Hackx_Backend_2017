<?php
namespace app\index\controller;

use BaiduAI\ImageClassifyDish;
use BaiduAI\Utils\AccessToken;
use think\Controller;

class Index extends Controller
{
    public function index()
    {
        $data = new ImageClassifyDish();
        $ret = $data->run(input("post.image"));
        return s([
            'ret' => $ret
        ]);
    }
}
