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

//把span字符串替换成a
function replace($data)
{
    return str_replace('span', 'a', $data);
}

/*
 * @param e [Exception] 系统异常对象
 * @param path [String] 发生错误的程序方法
 * @param selfdefine_exp_msg [String] 异常错误信息
 * @return [Array]
 */
function return_exception($e, $selfdefine_exp_msg=null, $datas = null){
    if( $selfdefine_exp_msg!=null ){
        $exp_msg = $selfdefine_exp_msg;
    }else{
        $exp_msg = '[文件:'.$e->getFile().'][行数:'.$e->getLine().'][信息:'.$e->getMessage().']';
    }
    //如果为运营阶段，异常会写入日志文件
    if( config('IS_SHOW_SYSTEM_EXP') === false ){
        recored_system_log($exp_msg);
    }
    return array(
        'res_code'  =>  500,
        'msg'       =>  '系统异常',
        'exp_msg'   =>  (config('IS_SHOW_SYSTEM_EXP') ? $exp_msg : '查看详细异常信息请到配置文件开启权限'),
        'datas'     =>  $datas
    );
}

/*
 * 返回数据模板
 * @param res_code 返回的code值
 * @param msg 返回的信息
 * @param url 路径
 * @param datas 其他内容
 * @return [Array]
 */
function response_tpl($res_code, $msg, $url = null, $datas=null){
    return array(
        'code'      =>  $res_code,
        'msg'       =>  $msg,
        'datas'     =>  $datas,
        'url'       =>  $url,
    );
}

