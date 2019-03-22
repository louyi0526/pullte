<?php
namespace app\index\controller;

use app\common\controller\Base;
use app\common\model\News;
use app\common\model\Product;
use think\facade\Request;
class Index extends Base
{
    public function index()
    {
        //产品案例
        $productInfo=Product::field('id,title,title_img,create_time')->order('id desc')->select();
        $this->view->assign('productInfo',$productInfo);

        //获取新闻信息
        $newsInfo = News::field('id,title,create_time')->order('id desc')->limit(6)->select();
        $this->view->assign('newsInfo',$newsInfo);
        return $this->view->fetch();
    }


}
