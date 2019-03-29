<?php
/**
 * 后台公共控制器
 */

namespace app\admin\common\controller;
use think\Controller;
use think\Db;
use think\facade\Session;

class Base extends Controller
{
    /**
     * 初始化方法
     */
    protected function initialize()
    {
        $this->roleRightInfo();
    }

    /**
     * 用户是否登录
     * 1.调用位置:后台:admin/index/index
     */
    public function isLogin()
    {
        if (!Session::has('admin_id')){
            $this->redirect('/admin/user',302);
        }
    }

    //树排序
    static public $treeList=array();
    public function tree($data,$pid=0)
    {
        foreach ($data as $key => $value){
            if ($value['pid'] == $pid){
                self::$treeList[]=$value;
                unset($data[$key]);
                self::tree($data,$value['id']);
            }

        }
        return self::$treeList;
    }

    //left与layout是否权限信息
    public function roleRightInfo()
    {
        $roleId = Session::get('role_id');

        $join = [
            ['right b','a.right_id=b.id'],
        ];

        $leftInfo = Db::table('roleright')
            ->field('b.right_name,b.right_title,b.pid,b.id,b.level,a.module')
            ->alias('a')
            ->join($join)
            ->where('a.role_id',$roleId)
            ->select();

        $data=explode("/",url());
        $str=0;
        foreach ($leftInfo as $val){
            $rel = explode("/",$val['module']);
            if ($rel[0] == $data[2]){
                $str=$str+1;
            }
        }
        $this->view->assign('str',$str);
        $this->view->assign('leftInfo',$leftInfo);
        return $leftInfo;
    }


}