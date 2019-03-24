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
use think\facade\Request;

class Right extends Base
{
    //权限列表界面
    public function rightList()
    {
        $rightList = RightModel::all();

        $rightList = $this->tree($rightList);
        $this->view->assign('rightList',$rightList);
        return $this->view->fetch('rightList');
    }

    //添加权限界面
    public function rightAdd()
    {
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

        $data['password'] = md5($data['password']);

        if (RightModel::create($data))
        {
            return ['status'=>1,'msg'=>'权限添加成功'];
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