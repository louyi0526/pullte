<?php

namespace app\index\controller;


use app\common\controller\Base;
use app\common\model\Product as ProModel;
use think\facade\Request;

class Product extends Base
{
    public function oneInfo()
    {
        $id = Request::param('id');
        $product = ProModel::where('id',$id)->find();
        $this->view->assign('product',$product);
        return $this->view->fetch('oneInfo');
    }
}