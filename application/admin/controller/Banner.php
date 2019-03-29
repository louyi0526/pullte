<?php

namespace app\admin\controller;

use app\admin\common\controller\Base;
use app\admin\common\model\Asset as BannerModel;
class Banner extends Base
{
    public function bannerList()
    {
        $rel = BannerModel::where('key','home_img')->find();
        $info = explode(",",$rel['value']);
        $this->view->assign('info',$info);
        return $this->view->fetch('bannerList');
    }
}