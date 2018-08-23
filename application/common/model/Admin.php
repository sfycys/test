<?php
/**
 * Created by PhpStorm.
 * User: chenwenliang
 * Date: 2018/8/8
 * Time: 9:03
 */

namespace app\common\model;


use think\Exception;
use think\Model;
use think\model\concern\SoftDelete;

class Admin extends Model
{
    //使用软删除
    use SoftDelete;
    /*
     * 登录验证
     */
    public function login($data){
        //进行场景校验
        $validate = new \app\common\validate\Admin();
        //校验失败返回
        if(!$validate->scene('login')->check($data)){
            return $validate->getError();
        }else{
            //查询对应的用户名
          $result = $this->where('admin_name',$data['admin_name'])->find();
                        //没该用户
                        if(!$result){
                            return '账号不存在！';
                        }else{
                            //密码错误
                            if($result['admin_password'] != $data['admin_password']){
                                return '密码错误！';
                            }
                            else{
                                //被禁用
                                if($result['admin_status'] == 0){
                                    return '该用户被禁用！';
                                }
                                else{
                                    //验证成功，记录用户信息
                        $sessionData = [
                            'id'        => $result['admin_id'],
                            'name'      => $result['admin_name'],
                            'nickname' =>  $result['admin_nickname'],
                            'role'  => $result['admin_role'],
                        ];
                        session('admin',$sessionData);
                        return 1;
                    }
                }
            }
        }
    }

    /*
     * 添加验证
     */
    public function add($data){

        //场景验证
        $validate = new \app\common\validate\Admin();
        if(!$validate->scene('add')->check($data)){
            return $validate->getError();
        }
        //判断用户名、邮箱、电话是否存在
        $name_exits = $this->where('admin_name',$data['admin_name'])->find(); //查看用户名是否已经存在
        if($name_exits){
            return "用户名已经存在！";
        }
        $tel_exits = $this->where('admin_telephone',$data['admin_telephone'])->find(); //查看手机号是否已经存在
        if($tel_exits){
            return "电话已经存在！";
        }
        $email_exits = $this->where('admin_email',$data['admin_email'])->find(); //查看邮箱是否已经存在
        if($email_exits){
            return "邮箱已经存在！";
        }
        //开始事务
        $this->startTrans();

        try{
            $this->allowField(true)->save($data);
            //提交
            $this->commit();
            return 1;
        }catch (\Exception $e){
            //出错回滚
            $this->rollback();
            return "管理员创建失败";
        }
    }


    /*
     * 删除指定管理员
     */
    public function del($data){

        //开始事务
        $this->startTrans();
        try{
            //删除
            $adminInfo = $this->where('admin_id','in',$data['admin_id'])->find();
            $adminInfo->delete();
            //提交事务
            $this->commit();
            return 1;
        }catch (\Exception $e){
            //回滚
            $this->rollback();
            return "删除失败！";
        }
    }

    /*
     * 编辑(修改)指定的管理员的信息
     */
    public function edit($data){
        $validate = new \app\common\validate\Admin();
        if(!$validate->scene('edit')->check($data)){
            return $validate->getError();
        }
        //查询数据库数据
        $adminInfo = $this->where('admin_id',$data['admin_id'])->find();
        $adminInfo->admin_nickname = $data['admin_nickname'];
        $adminInfo->admin_telephone = $data['admin_telephone'];
        $adminInfo->admin_email    = $data['admin_email'];
        if(session('admin.role') == 1){
            $adminInfo->admin_role   = $data['admin_role'];
            $adminInfo->admin_status    = $data['admin_status'];
        }
//       dump($adminInfo);
//        更新数据
//        $result = $this->where('admin_id',$data['id'])->save($adminInfo);
        //开始事务
        $this->startTrans();
        try{
            $adminInfo->save();
            $this->commit();
            return 1;
        }catch (\Exception $e){
            $this->rollback();
            return "修改失败!";
        }
    }

    /*
     * 修改密码
     */
    public function changePassword($data){
        /*
         * 所以要先确认自己的密码是否正确
         * 才能进一步修改
         */

        $rightPass = $this->where('admin_name', session('admin.username'))->
        where('admin_password', $data['own_password'])->find();
        if(!$rightPass){
            return "自身密码错误！";
        }
//        dump($data);
        $validate = new \app\common\validate\Admin();
        if(!$validate->scene('changePassword')->check($data)){
            return $validate->getError();
        }

        $adminInfo = $this->where('admin_id', $data['id'])->find();

        $adminInfo->admin_password = $data['new_password'];
        //开始事务
        $this->startTrans();
        try{
            $adminInfo->save();
            $this->commit();
            return 1;
        }catch (\Exception $e){
            $this->rollback();
            return "修改密码失败";
        }
    }
}