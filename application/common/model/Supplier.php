<?php
namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;


class Supplier extends Model
{

    //软删除
    use SoftDelete;
    //关联设备表
    public function devices()
    {
        return $this->hasMany('Device','device_id','supplier_id');
}

    public function add($data)
    {
        $validate = new \app\common\validate\Supplier();
        if (!$validate->scene('add')->check($data)) {
            return $validate->getError();
        }

        //判断用户名、邮箱、电话是否存在
        $name_exits = $this->where('supplier_name',$data['supplier_name'])->find(); //查看用户名是否已经存在
        if($name_exits){
            return "供应商已经存在！";
        }


        //开始事务
        $this->startTrans();
        try{
            $this->allowField(true)->save($data);
            //执行事务
            $this->commit();
            return 1;
        }catch (\Exception $e){
            //事务回滚
            $this->rollback();
            return "供应商信息更新失败！";
        }
    }
    public function edit($data)
    {
        $validate = new \app\common\validate\Supplier();
        if (!$validate->scene('edit')->check($data)) {
            return $validate->getError();
        }
        $supplierInfo = $this->where('supplier_id', $data['supplier_id'])->find();
//        $supplierInfo = $this->where('supplier_id',$data['supplier_id'])->select();
        $supplierInfo->supplier_name = $data['supplier_name'];
        $supplierInfo->contact_name= $data['contact_name'];
        $supplierInfo->contact_email= $data['contact_email'];
        $supplierInfo->contact_phone= $data['contact_phone'];
        $supplierInfo->contact_tel = $data['contact_tel'];
        //开始事务
        $this->startTrans();
        try{
            $supplierInfo->allowField(true)->save();
            //执行事务
            $this->commit();
            return 1;
        }catch (\Exception $e){
            //事务回滚
            $this->rollback();
            return "设备信息更新失败！";
        }
    }

    //删
    public function del($data)
    {
        $adminInfo = $this->where('supplier_id',$data['supplier_id'])->find();
        //开始事务
        $this->startTrans();
        try{
            $adminInfo->together('devices')->delete();
            //执行事务
            $this->commit();
            return 1;
        }catch (\Exception $e){
            //事务回滚
            $this->rollback();
            return "设备信息更新失败！";
        }
    }
}