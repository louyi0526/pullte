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
    public function index()
    {
        return $this->view->fetch('login');
    }

    //登录提交操作
    public function checkLogin()
    {
        $data = Request::post();


        $where[]=['login_name','=',$data['login_name']];
        $where[]=['password','=',md5($data['password'])];

        $rel = UserModel::field('id,login_name')->where($where)->find();

        if ($rel){

            Session::set('admin_id',$rel['id']);
            Session::set('login_name',$rel['login_name']);
            $roleId = Db::table('userrole')
                ->where('user_id',$rel['id'])
                ->find();
            Session::set('role_id',$roleId['role_id']);

            $user = UserModel::field('login_time')->where('id',$rel['id'])->find();
            $data=array(
                'login_time'=>time(),
                'last_login_time'=>$user['login_time']
            );
            UserModel::where('id',$rel['id'])->update($data);

            return ['status'=>1,'msg'=>'登录成功'];
        }
        return ['status'=>-1,'msg'=>'用户或密码错误!'];

    }

    //用户列表界面
    public function userList()
    {
        $this->isLogin();

        $admin_id = Session::get('admin_id');

        if ($admin_id == 1){
            $where[]=['a.id','>',1];
        }else {
            $where[]=['a.id','=',$admin_id];
        }

        $join = [
            ['userrole ur','a.id=ur.user_id'],
            ['role r','ur.role_id=r.id'],
        ];
        if ($admin_id == 1) {
            $userList = UserModel::
                field('a.id,a.login_name,r.role_name,a.create_time,a.login_time,a.last_login_time')
                ->alias('a')
                ->join($join)
                ->where($where)
                ->order('ur.role_id asc,ur.id asc')
                ->paginate(3);
        }else{
            $userList = UserModel::
            field('a.id,a.login_name,a.mobile,r.role_name,a.create_time,a.login_time,a.last_login_time')
                ->alias('a')
                ->join($join)
                ->where($where)
                ->order('ur.role_id asc,ur.id asc')
                ->find();
        }


        $this->view->assign('title','用户管理');
        $this->view->assign('empty','<span style="color:red;">没有数据</span>');
        $this->view->assign('userList',$userList);
        if ($admin_id == 1) {
            return $this->view->fetch('userList');
        }else{
            return $this->view->fetch('userOne');
        }
    }

    //添加用户界面
    public function userAdd()
    {
        $this->isLogin();
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

    //用户编辑界面
    public function userEdit()
    {
        $this->isLogin();
        $id = Request::param('id');
        $where[]=['status','=',1];
        $where[]=['id','>',1];
        $role = RoleModel::where($where)->select();

        $join = [
            ['userrole ur','ur.user_id= a.id'],
        ];

        $userInfo = UserModel::field('a.id,a.login_name,ur.role_id')
            ->alias('a')
            ->where('a.id',$id)
            ->join($join)
            ->find();

        $this->view->assign('role',$role);
        $this->view->assign('userInfo',$userInfo);
        return $this->view->fetch('userEdit');
    }

    //用户编辑操作
    public function doEdit()
    {
        $info = Request::post();
        $user_id = $info['user_id'];
        unset($info['user_id']);

        if (Db::table('userrole')->where('user_id',$user_id)->update($info)){
            return ['status' => 1, 'msg' => '修改用户成功'];
        }
        return ['status' => -1, 'msg' => '修改用户失败'];
    }

    //个人用户编辑操作
    public function doEditOne()
    {
        $info = Request::post();
        $user_id = $info['user_id'];
        unset($info['user_id']);

        if (!empty($info['password'])) {

            if ($info['password_confirm'] != $info['password']){
                return ['status' => 0, 'msg' => '密码不相同'];
            }
            unset($info['password_confirm']);
            $info['password']=md5($info['password']);
        }

       if (UserModel::where('id',$user_id)->update($info)){
            return ['status' => 1, 'msg' => '修改成功'];
        }
        return ['status' => -1, 'msg' => '修改失败'];
    }

    //删除用户操作
    public function doDelete()
    {

    }

    //退出登录
    public function logout()
    {
        Session::clear();
        $this->redirect('/admin/user');
    }
}