<?php
/**
 * Created by PhpStorm.
 * User: chenwenliang
 * Date: 2018/8/12
 * Time: 23:10
 * 功能：后台首页
 */

namespace app\admin\controller;
use think\Controller;


/**
 * @title 测试后台首页
 * @description 接口说明
 * @group 接口分组
 * @header name:key require:1 default:1 other: desc:秘钥(区别设置)
 */


class Home extends Controller
{
    //后台首页
    public function index(){
        return view();
    }


    /**
     * @title 退出接口
     * @description 接口说明
     * @author 开发者
     * @url admin/home/loginOut
     * @method GET

     * @param name:admin_id type:varchar require:1  other: desc:用户号
     *·
     */


    //用户退出
    public function loginOut(){
        //清空session
        session(null);
        //成功返回对应的页面
        if(session('?admin.id')){
            $this->error('退出失败');
//            return response_tpl('400', '退出失败!');
        }else{
            $this->success('200',"退出成功", 'admin/index/login');
//            return response_tpl('200', '退出成功！','admin/index/login');
        }
    }
}