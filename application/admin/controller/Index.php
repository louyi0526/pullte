<?php
/**
 * 后台入口文件
 */

namespace app\admin\controller;


use app\admin\common\controller\Base;

class Index extends Base
{

    public function index()
    {
        //用户是否登录
        $this->isLogin();
//        return $this->view->fetch();
        return $this->redirect('user/userList');
    }
}