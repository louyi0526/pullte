<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/24
 * Time: 10:27
 */

namespace app\admin\controller;


use app\admin\common\controller\Base;
use app\admin\common\model\Role as RoleModel;
use app\admin\common\model\Right as RightModel;
use Think\Db;
use think\facade\Request;

class Role extends Base
{
    //角色列表界面
    public function roleList()
    {
        $this->isLogin();
        $roleList = RoleModel::select();
        $this->view->assign('roleList',$roleList);

        $this->view->assign('title','用户管理');
        $this->view->assign('empty','<span style="color:red;">没有数据</span>');

        return $this->view->fetch('roleList');
    }

    //角色添加界面
    public function roleAdd()
    {
        $this->isLogin();
        return $this->view->fetch('roleAdd');
    }

    //角色添加操作
    public function doAdd()
    {
        $data = Request::post();
        $rule ='app\admin\common\validate\Role';//自定义的验证规则
        $res = $this->validate($data,$rule); //开始验证

        if ($res !== true){
            return ['status' => 0, 'msg' => $res];
        }

        if (RoleModel::create($data)){
            return ['status'=> 1,'msg'=>'角色添加成功'];
        }
        return ['status'=> -1,'msg'=>'角色添加失败'];
    }

    //删除角色操作
    public function doDelete()
    {
        $roleId = Request::post('id');

        if (RoleModel::where('id',$roleId)->delete()){
            return ['status'=> 1,'msg'=>'角色删除成功'];
        }
        return ['status'=> -1,'msg'=>'角色删除失败'];

    }

    //角色权限配置编辑界面
    public function roleEdit()
    {
        $this->isLogin();
        //获取修改角色的信息
        $roleId = Request::param('id');

        $roleInfo = RoleModel::where('id',$roleId)->find();
        $this->view->assign('roleInfo',$roleInfo);

        //获取所有权限信息
        $rightInfo = RightModel::all();
        $rightInfo = $this->tree($rightInfo);


        //获取角色权限表信息
        $data=array();
        foreach ($rightInfo as $value){
            $count = Db::table('roleright')
                ->where('role_id',$roleId)
                ->where('right_id',$value['id'])
                ->count();
            if ($count){
                $value['roleright']=1;
            }else{
                $value['roleright']=0;
            }
            $data[]=$value;
        }

        $this->view->assign('rightInfo',$data);

        return $this->view->fetch('roleEdit');
    }

    //角色权限配置编辑操作
    public function doEdit(){

        $roleInfo = Request::post();

        $role_id = $roleInfo['role_id'];
        $count = Db::table('roleright')
            ->where('role_id',$role_id)
            ->count();

        if ($count){
            Db::table('roleright')->where('role_id',$role_id)->delete();
        }

        $right=$roleInfo['right'];
        $data=array();
        foreach ($right as $value){
            $tmp = explode("_",$value);
            $right_name = Db::table('right')->field('pid,right_name')->where('id',$tmp[0])->find();
            $p_right_name='';
            if ($right_name['pid'] > 0){
                $p_right_name = Db::table('right')->field('right_name')->where('id',$right_name['pid'])->find();
                $right_name['right_name']=$p_right_name['right_name'].'/'.$right_name['right_name'];
            }
            $data[]=array(
                'role_id'=>$role_id,
                'right_id'=>$tmp[0],
                'level'=>$tmp[1],
                'module'=>$right_name['right_name'],
            );
        }

        if (Db::table('roleright')->data($data)->insertAll()){
            return ['status'=> 1,'msg'=>'角色权限配置成功'];
        }
        return ['status'=> -1,'msg'=>'角色权限配置失败'];
    }
}