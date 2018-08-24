<?php
/**
 * Created by PhpStorm.
 * User: chenwenliang
 * Date: 2018/8/17
 * Time: 9:19
 * 公共控制器
 */

namespace app\admin\controller;

1111
use think\Controller;

class Base extends Controller
{
    //初始化函数
    //用于判断session是否过期和是否登录
    public function initialize()
    {
        //如果没登录或者是身份过期
        if(!session('?admin.id')){
            $res = response_tpl('400', '身份过期');
            $this->redirect('admin/index/login');
        }
    }
}