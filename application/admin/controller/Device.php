<?php

namespace app\admin\controller;

use think\Db;

/**
 * @title 测试设备管理
 * @description 接口说明
 * @group 接口分组


 */
class Device extends Base
{
    /**
     * @title 测试设备管理接口
     * @description 接口说明
     * @author 开发者
     * @url /admin/device
     * @method GET
     * @method POST
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
     * @title 设备主页接口
     * @description 接口说明
     * @author 开发者
     * @url /admin/device/
     * @method GET|POST


     * @return device_id:设备ID
     * @return device_name:设备名称
     * @return device_location:设备所在地
     * @return device_status:设备状态
     * @return device_agreement:设备协议
     * @return supplier_name:供应商名称
     * @return create_time:添加时间
     *
     */

    //设备管理首页
    public function index()
    {
        try{
            //先查供应商表
            //查询条件
          //  $this->listenSql();
            $condition = input('keywords');
            $maps = [];
            //如果有查询

            $supp = model('Supplier')->where('supplier_name','like','%'.$condition.'%')
                ->column('supplier_id');
            if ($supp !=null){
                $map=['supplier_id','in',$supp];
                $maps=[$map];
            }


            elseif(strstr('在线',$condition)){
                $map = ['device_status', '=', 0];
                $maps =[$map];
            }
            elseif (strcmp('离线',$condition) == 0){
                $map = ['device_status', '=', 1];
                $maps =[$map];
            }
            elseif ($condition != null){
                //组合查询条件
                $map1 = ['device_name', 'like', '%'.$condition.'%'];
                $map2 = ['device_id', 'like', '%'.$condition.'%'];
//                $map4 = ['supplier_id', 'like', '%'.$condition.'%'];
                $maps = [$map1, $map2];
            }
            //排序规则
            $rule = [
            ];
            //关联查询供应商表
            $data = model('Device')/*->with('supplier')*/
//                ->select();
                ->whereOr($maps)
                ->order($rule)->paginate('10');

//            dump( $data['supplier']);
//            $this->listenSql();
            $sum = model('Device')->count();

            $viewData = [
                'deviceInfo' => $data,
                'sum'         => $sum,
                'condition'   => $condition
            ];
            $this->assign($viewData);
            return view();
        }catch (\Exception $e){
            return return_exception($e);
        }
    }


    /**
     * @title 添加接口
     * @description 接口说明
     * @author 开发者
     * @url /admin/device/
     * @method GET|POST


     * @param name:device_name type:string require:1 default: other: desc:设备名称
     *@param name:device_location type:string require:1 default: other: desc:设备所在地
     * @param name:device_status type:enum require:1 default:在线 other: desc:设备状态
     * @param name:device_agreement type:string require: default: other: desc:设备协议
     * @param name:supplier_name type:int require:1 default: other:与供应商表关联 desc:供应商名称
     *
     * @return device_id:设备ID
     * @return device_name:设备名称
     * @return device_location:设备所在地
     * @return device_status:设备状态
     * @return device_agreement:设备协议
     * @return supplier_name:供应商名称
     * @return create_time:添加时间
     *
     */
//    添加设备
    public function add()
    {
//        try{
        if (request()->isPost()) {
            //获取设备信息
            $data = [
                'device_name'       => input('post.device_name'),      //设备名称
                'device_location'   => input('post.device_location'),   //设备位置
                'device_agreement'  => input('post.device_agreement'),  //协议
                'device_status'     => input('post.device_status'),     //状态
                'supplier_id'       => input('post.supplier_id')        //设备商ID
            ];
            $result = model('Device')->add($data);
            if ($result == 1) {
//                $this->redirect('admin/device/index');
                $this->success('200','设备信息添加成功','admin/device/index');
            } else {
                $this->error('400',$result);
            }
        }
        //获得供应商的信息
        $data = model('Supplier')->field('supplier_id,supplier_name')->select();
//            dump($data);
        $supplierInfo = [
            'supplierInfo' => $data,
        ];
        $this->assign($supplierInfo);
        return view();
//        }
//        catch (\Exception $e){
//            return return_exception($e);
//        }
    }

    /**
     * @title 修改接口
     * @description 接口说明
     * @author 开发者
     * @url /admin/device/
     * @method GET|POST

     * @param name:device_name type:string require:1 default:与当前要修改的设备ID对应的设备名称 other:不显示当前设备ID desc:设备名称
     *@param name:device_location type:string require:1 default:对应的设备所在地 other: desc:设备所在地
     * @param name:device_status type:enum require:1 default:对应的设备状态 other: desc:设备状态
     * @param name:device_agreement type:string require: default:对应的设备协议 other: desc:设备协议
     * @param name:supplier_name type:int require:1 default:对应的供应商名称 other:与供应商表关联 desc:供应商名称
     * @return device_id:设备ID
     * @return device_name:设备名称
     * @return device_location:设备所在地
     * @return device_status:设备状态
     * @return device_agreement:设备协议
     * @return supplier_name:供应商名称
     * @return update_time:更新时间
     *
     */
    public function edit()
    {
        if (request()->isPost()) {
            $data = [
                'device_id'             =>  input('post.id'),
                'device_name'           => input('post.device_name'),
                'device_location'       => input('post.device_location'),
                'device_agreement'      => input('post.device_agreement'),
                'device_status'         =>  input('post.device_status'),
                'supplier_id'           => input('post.supplier_id')
            ];
            $result = model('Device')->edit($data);
//            $this->redirect('admin/device/index');
            if ($result==1) {
//                $this->redirect('admin/device/index');
                $this->success('200','设备编辑成功！', 'admin/device/index');
            } else {
//                $this->redirect('admin/device/index');
                $this->error('400',$result);
            }
        }
        $device_id = input('device_id');
        $deviceInfo = model('Device')->where('device_id',$device_id)->find();
        //获得供应商的信息
        $data = model('Supplier')->field('supplier_id,supplier_name')->select();
//            dump($data);
        $viewData = [
            'deviceInfo' => $deviceInfo,
            'supplierInfo' => $data
        ];
        $this->assign($viewData);
        return view();
    }


    /**
     * @title 删除接口
     * @description 接口说明
     * @author 开发者
     * @url /admin/device/
     * @method GET|POST

     * @param name:device_id type:int require:1 default:要删除信息的设备ID other: desc:设备ID
     * @param name:device_name type:string require:1 default:与设备ID对应的设备名称 other: desc:设备名称
     *@param name:device_location type:string require:1 default:对应的设备所在地 other: desc:设备所在地
     * @param name:device_status type:enum require:1 default:对应的设备状态 other: desc:设备状态
     * @param name:device_agreement type:string require: default:对应的设备协议 other: desc:设备协议
     * @param name:supplier_name type:int require:1 default:对应的供应商名称 other: desc:供应商名称
     * @return update_time:更新时间
     * @return delete_time:删除时间
     *
     */
    //      删除设备
    public function del()
    {
        $data = [
            'device_id' => input('id'),
        ];
        //使用软删;
        $result = model("Device")->del($data);

        if ($result == 1) {
//            return "删除成功！";
            $this->success('200','设备删除成功!');
        } else {
//            return "删除失败！";
            $this->success('200',$result);

        }
    }


}
