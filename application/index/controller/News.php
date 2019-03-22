<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/22 0022
 * Time: 15:11
 */

namespace app\index\controller;

use app\common\controller\Base;
use app\common\model\News as NewsModel;
use think\facade\Request;
class News extends Base
{
    public function detail()
    {
        $id = Request::param('id');
        $news = NewsModel::where('id',$id)->find();
        $this->view->assign('news',$news);
        return $this->view->fetch('detail');
    }

    public function NewsList()
    {
        $news = NewsModel::paginate(5);
        $this->view->assign('news',$news);
        return $this->view->fetch('newsList');
    }
}