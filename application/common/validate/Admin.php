<?php
/**
 * 管理员的场景验证
 * Created by PhpStorm.
 * User: chenwenliang
 * Date: 2018/8/8
 * Time: 9:04
 */

namespace app\common\validate;


use think\Validate;

class Admin extends Validate
{
    //验证规则
    protected $rule = [
        'admin_name|管理员账号'   => 'require',
        'admin_password|密码'     => 'require',
        'old_password|原密码'     => 'require',
        'new_password|新密码'     => 'require',
        'admin_nickname|昵称'     => 'require',
        'admin_email|邮箱'        => 'require|email',
        'admin_code|验证码'       => 'require',
        'con_password|确认密码'   => 'require',
        'admin_telephone|手机号'  => 'mobile',
//                'admin_telephone|手机号'  => ,
        'admin_role|角色'         => 'in:0,1|require',
        'admin_status|状态'       => 'in:0,1,2|require',
        '__token__|表单令牌'      =>   'token'
    ];
    /*
     * 登录场景验证
     */
    public function sceneLogin (){
        return $this->only(['admin_username', 'admin_password']);
    }

    /*
     * 添加管理员验证
     */
    public function sceneAdd(){
        return $this->only(['admin_name',
                            'admin_password',
                            'admin_com_password',
                            'admin_nickname',
                            'admin_email',
                            'admin_telephone',
                            'admin_role',
                            'admin_status']);
    }

    /*
     * 修改管理员信息
     */
    public function sceneEdit(){
        if(session('admin.role') == 1){
            return $this->only(['admin_email', 'admin_nickname', 'admin_telephone', 'admin_role' , 'admin_status']);
        }
        return $this->only(['admin_email', 'admin_nickname', 'admin_telephone']);
    }

    /*
     * 修改密码
     */
    public function sceneChangePassword(){
        return $this->only([ 'new_password', 'con_password'])
            ->append('con_password','confirm:new_password');
    }

}