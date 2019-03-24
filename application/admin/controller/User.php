<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/23
 * Time: 20:36
 */
namespace app\admin\controller;

use app\admin\common\controller\Base;
use app\admin\common\model\User as UserModel;
use app\admin\common\model\Role as RoleModel;
use Think\Db;
use think\facade\Request;
use think\facade\Session;
class User extends Base
{
    //登录界面
    public function login()
    {
        return $this->view->fetch();
    }

    //登录提交操作
    public function checkLogin()
    {
        $data = Request::post();


        $where[]=['login_name','=',$data['login_name']];
        $where[]=['password','=',md5($data['password'])];

        $rel = UserModel::field('id,login_name')->where($where)->find();
        if ($rel){

            Session::set('id',$rel['id']);
            Session::set('login_name',$rel['login_name']);
//            Session::set('admin_role',$rel['role']);
            return ['status'=>1,'msg'=>'登录成功'];
        }
        return ['status'=>-1,'msg'=>'用户或密码错误!'];

    }

    //用户管理界面
    public function userList()
    {
        $data['id']=Session::get('id');
//        $data['admin_role']=Session::get('admin_role');

//        if ($data['admin_role'] == 'all'){
//            $userList = UserModel::where('id !='.$data["id"])->select();

        $join = [
            ['userrole ur','a.id=ur.user_id'],
            ['role r','ur.role_id=r.id'],
        ];

        $userList = UserModel::field('a.id,a.login_name,r.role_name,a.create_time,a.login_time,a.last_login_time')->alias('a')->join($join)->order('ur.role_id asc,ur.id asc')->paginate(3);
//        }else{
//            //获取当前用户信息
//            $userList = UserModel::where('id',$data['admin_id'])->select();
//        }

        $this->view->assign('title','用户管理');
        $this->view->assign('empty','<span style="color:red;">没有数据</span>');
        $this->view->assign('userList',$userList);

        return $this->view->fetch('userList');
    }

    //添加用户界面
    public function userAdd()
    {
        $where[]=['status','=',1];
        $where[]=['id','>',1];
        $role = RoleModel::where($where)->select();
        $this->view->assign('role',$role);
        return $this->view->fetch('userAdd');
    }

    //添加用户操作
    public function doAdd()
    {
        $data = Request::post();

        $rule ='app\admin\common\validate\User';//自定义的验证规则
        $res = $this->validate($data,$rule); //开始验证

        if ($res !== true){
            return ['status' => 0, 'msg' => $res];
        }

        $data['password'] = md5($data['password']);

        //添加用户表
        $user_id = UserModel::create($data);

        if (!empty($user_id)) {

            //添加用户角色表userRole
            $role['role_id'] = $data['role_id'];
            $role['user_id'] = $user_id['id'];
            Db::table('userrole')->insert($role);

            return ['status' => 1, 'msg' => '添加用户成功'];
        }
        return ['status'=> -1,'msg'=>'添加用户失败'];
    }

    //删除用户操作
    public function doDelete()
    {

    }
}