<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/24
 * Time: 16:00
 */

namespace app\admin\controller;


use app\admin\common\controller\Base;
use app\admin\common\model\Right as RightModel;
use think\Db;
use think\facade\Request;
use think\facade\Session;

class Right extends Base
{
    //权限列表界面
    public function rightList()
    {
        $this->isLogin();
        $rightList = RightModel::all();

        $rightList = $this->tree($rightList);
        $this->view->assign('rightList',$rightList);
        return $this->view->fetch('rightList');
    }

    //添加权限界面
    public function rightAdd()
    {
        $this->isLogin();
        $level = RightModel::where('level != 3')->select();
        $this->view->assign('level',$level);
        return $this->view->fetch('rightAdd');
    }

    //添加权限操作
    public function doAdd()
    {
        $data = Request::post();

        //自定义的验证规则
        $rule ='app\admin\common\validate\Right';
        $res = $this->validate($data,$rule); //开始验证
        if ($res !== true){
            return ['status' => 0, 'msg' => $res];
        }

        $rightId = RightModel::create($data);

        if ($rightId)
        {

            /*
             * 权限添加成功后
             * 1.root用户角色直接添加此权限
             * 2.session用户角色不是root时,session用户角色需要添加此权限
             * */

            $right_name=RightModel::field('right_name')->where('id',$data['pid'])
                ->find();
            $roleRightData['role_id']= 1;
            $roleRightData['level']= $data['level'];
            $roleRightData['right_id']= $rightId['id'];
            $roleRightData['module']= $right_name['right_name'].'/'.$data['right_name'];

            if (Db::table('roleright')->insert($roleRightData)) {

                if (Session::get('role_id') == 1) {
                    return ['status'=>1,'msg'=>'权限添加成功'];
                }else {
                    $roleRightData['role_id']=Session::get('role_id');
                    if (Db::table('roleright')->insert($roleRightData)){
                        return ['status'=>1,'msg'=>'权限添加成功'];
                    }
                }

            }

        }
        return ['status'=>0,'msg'=>'权限删除失败'];
    }

    //删除权限操作
    public function doDelete()
    {
        $rightId = Request::post('id');

        if (RightModel::where('id',$rightId)->delete()){
            return ['status'=> 1,'msg'=>'权限删除成功'];
        }
        return ['status'=> -1,'msg'=>'权限删除失败'];
    }
}