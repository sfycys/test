<?php

namespace app\admin\controller;

use think\Db;

/**
 * @title 测试供应商管理
 * @description 接口说明
 * @group 接口分组
 */

class Supplier extends Base
{
    /**
     * @title 测试供应商管理接口
     * @description 接口说明
     * @author 开发者
     * @url /admin/supplier
     * @method GET
     *
     *
     *
     * @return name:名称
     * @return mobile:手机号
     * @return list_messages:消息列表@
     * @list_messages message_id:消息ID content:消息内容
     * @return object:对象信息@!
     * @object attribute1:对象属性1 attribute2:对象属性2
     * @return array:数组值#
     * @return list_user:用户列表@
     * @list_user name:名称 mobile:手机号 list_follow:关注列表@
     * @list_follow user_id:用户id name:名称
     */
    //监听数据库
    protected function listenSql(){
        Db::listen(function ($sql, $time, $explain) {
// 记录SQL
            echo $sql . ' [' . $time . 's]';
// 查看性能分析结果
            dump($explain);
        });
    }

    /**
     * @title 供应商主页接口
     * @description 接口说明
     * @author 开发者
     * @url /admin/supplier/
     * @method GET|POST
     *
     * @return supplier_id:供应商ID
     * @return supplier_name:供应商名称
     * @return contact_name:联系人姓名
     * @return contact_email:联系邮箱
     * @return contact_tel:手机号码
     * @return contact_phone:联系电话
     * @return create_time:添加时间
     *
     */
    //设备商页面
    public function index()
    {
        try{
            //获取查询条件
            $condition = input('keywords');
            $maps = [];
            //如果有查询，获取查询条件
            if($condition){
                $map1 = ['supplier_name', 'like', '%'.$condition.'%'];
                $map2 = ['supplier_id', 'like', '%'.$condition.'%'];
                $map3 = ['contact_name', 'like', '%'.$condition.'%'];
                $maps = [$map1, $map2, $map3];
            }
            $rule = [];
            $data = model('Supplier')
                ->whereOr($maps)->order($rule)->paginate('10');
            $sum = model('Supplier')->count();
            $viewData = [
                'supplierInfo' => $data,
                'sum' => $sum
            ];
            $this->assign($viewData);
            return view();
        }catch (\Exception $e){
            return_exception($e);
        }
    }
    /**
     * @title 添加接口
     * @description 接口说明
     * @author 开发者
     * @url /admin/supplier/
     * @method GET|POST

     * @param name:supplier_name type:string require:1 default: other: desc:供应商名称
     *@param name:contact_name type:string require:1 default: other: desc:联系人姓名
     * @param name:contact_email type:string require: default: other: desc:联系邮箱
     * @param name:contact_tel type:string require: default: other: desc:手机号码
     * @param name:contact_phone type:string require: default: other: desc:联系电话
     * @return supplier_id:供应商ID
     * @return supplier_name:供应商名称
     * @return contact_name:联系人姓名
     * @return contact_email:联系邮箱
     * @return contact_tel:手机号码
     * @return contact_phone:联系电话
     * @return create_time:添加时间
     * *
     */

    //    添加设备商
    public function add()
    {
        if (request()->isPost()) {
            $data = [
                'supplier_name' => input('post.supplier_name'),
                'contact_name' => input('post.contact_name'),
                'contact_email' => input('post.contact_email'),
                'contact_phone' => input('post.contact_phone'),
                'contact_tel' => input('post.contact_tel')
            ];
            $result = model('Supplier')->add($data);
            if ($result == 1) {
//                $this->redirect('admin/supplier/index');
                $this->success('200','供应商信息添加成功','admin/supplier/index');
            } else {
                $this->error('400',$result);
            }
        }
        return view();
    }

    /**
     *
     * @title 修改接口
     * @description 接口说明
     * @author 开发者
     * @url /admin/supplier/
     * @method GET|POST
     *
     * @param name:supplier_name type:string require:1 default:与当前要修改的供应商ID对应的供应商名称 other:不显示当前供应商ID desc:供应商名称
     *@param name:contact_name type:string require:1 default:对应的联系人姓名 other: desc:联系人姓名
     * @param name:contact_email type:string require: default:对应的联系邮箱 other: desc:联系邮箱
     * @param name:contact_tel type:string require: default:对应的手机号码 other: desc:手机号码
     * @param name:contact_phone type:string require: default:对应的联系电话 other: desc:联系电话

     * @return supplier_name:供应商名称
     * @return contact_name:联系人姓名
     * @return contact_email:联系邮箱
     * @return contact_tel:手机号码
     * @return contact_phone:联系电话
     * @return update_time:更新时间
     *
     */
    //编辑设备商信息
    public function edit()
    {
        if (request()->isAjax()) {
//            供应商数据
            $data = [
                'supplier_id'   => input('post.supplier_id'),   //id
                'supplier_name' => input('post.supplier_name'), //名称
                'contact_name'  => input('post.contact_name'),  //联系人姓名
                'contact_email' => input('post.contact_email'), //邮箱
                'contact_phone' => input('post.contact_phone'), //电话
                'contact_tel'   => input('post.contact_tel')    //手机号
            ];
            $result = model('Supplier')->edit($data);
            if ($result==1) {
                //              $this->redirect('admin/supplier/index');
                $this->success('供应商编辑成功！', 'admin/supplier/index');
            } else {
                $this->error($result);
            }
        }
        $supplier_id = input('supplier_id');
        //       input('id as device_id')
        //      $deviceInfo = model('Device')->find();
        $supplierInfo = model('Supplier')->where('supplier_id',$supplier_id)->find();
        $viewData = [
            'supplierInfo' => $supplierInfo
        ];
        $this->assign($viewData);
        return view();
    }

    /**
     *
     * @title 删除接口
     * @description 接口说明
     * @author 开发者
     * @url /admin/supplier/
     * @method GET|POST
     *
     *
     *
     *@param name:supplier_id type:int require:1 default:要删除信息的供应商ID other: desc:供应商ID
     * @param name:supplier_name type:string require:1 default:与供应商ID对应的供应商名称 other: desc:供应商名称
     *@param name:contact_name type:string require:1 default:对应的联系人姓名 other: desc:联系人姓名
     * @param name:contact_email type:string require: default:对应的联系邮箱 other: desc:联系邮箱
     * @param name:contact_tel type:string require: default:对应的手机号码 other: desc:手机号码
     * @param name:contact_phone type:string require: default:对应的联系电话 other: desc:联系电话


     * @return update_time:更新时间
     * @return delete_time:删除时间
     *
     */
    //      删除设备商信息
    public function del()
    {
        $data = [
            'supplier_id' => input('id'),
        ];
        $result = model('Supplier')->del($data);
        $result1= model('Device')->where('supplier_id',$data['supplier_id'])
            ->useSoftDelete('delete_time',time())->delete();
        if ($result) {
            $this->success('200','供应商删除成功!');
        }else {
            $this->error('400','供应商删除失败!');
        }
    }

}
