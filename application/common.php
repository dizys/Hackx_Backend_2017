<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function s($data, $msg = null){
    return json([
        'code'=>200,
        'data'=>$data,
        'msg'=>m_lang($msg)
    ]);
}

function e($msg, $code=0){
    return json([
        'code'=>400+$code,
        'data'=>null,
        'msg'=>m_lang($msg)
    ]);
}

function m_lang($msg){
    return is_null($msg)||$msg === ""?"":lang($msg);
}