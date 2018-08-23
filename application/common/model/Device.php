<?php
namespace app\common\model;
use think\Model;
use think\model\concern\SoftDelete;

class Device extends Model
{
    //软删除
    use SoftDelete;

    //关联供应商表
    public function supplier()
    {
        return $this->belongsTo('Supplier', 'supplier_id','supplier_id')
            ->field('supplier_id,supplier_name');
    }
    //增
    public function add($data)
    {
        $validate = new \app\common\validate\Device();
        if (!$validate->scene('add')->check($data)) {
            return $validate->getError();
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
            return "设备信息更新失败！";
        }
    }

    //删
    public function del($data){
        //开始事务
        $this->startTrans();
        try{
            //删除
            $adminInfo = $this->where('device_id',$data['device_id'])->find();
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
    //改
    public function edit($data)
    {
        $validate = new \app\common\validate\Device();
        if (!$validate->scene('edit')->check($data)) {
            return $validate->getError();
        }
        $deviceInfo = $this->where('device_id', $data['device_id'])->find();
//        $deviceInfo = $this->where('device_id',$data['device_id'])->select();
        $deviceInfo->device_name = $data['device_name'];
        $deviceInfo->device_location= $data['device_location'];
        $deviceInfo->device_agreement= $data['device_agreement'];
        $deviceInfo->device_status= $data['device_status'];
        $deviceInfo->supplier_id = $data['supplier_id'];
        //开始事务
        $this->startTrans();
        try{
            $deviceInfo->save();
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