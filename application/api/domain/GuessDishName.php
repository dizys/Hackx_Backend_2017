<?php
/**
 * Created by PhpStorm.
 * User: Dizy
 * Date: 2017/11/4
 * Time: 8:33
 */

namespace app\api\domain;


use BaiduAI\ImageClassifyDish;

class GuessDishName
{
    public function guess($image, &$calorie){
        $icd = new ImageClassifyDish();
        $ret = $icd->run($image);
        if(is_null($ret)||isset($ret->error_code)){
            return false;
        }
        $results = $ret->result;
        if(count($results)!=0){
            $calorie = $results[0]->calorie;
            return $results[0]->name;
        }else{
            return false;
        }
    }
}