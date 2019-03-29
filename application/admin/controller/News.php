<?php

namespace app\admin\controller;


use app\admin\common\controller\Base;
use app\admin\common\model\News as NewsModel;
use think\facade\Session;
use think\facade\Request;

class News extends Base
{
    //新闻列表界面
    public function newsList()
    {
        $this->isLogin();
        $newsList= NewsModel::order('id desc')->paginate(10);
        $this->view->assign('newsList',$newsList);
        $this->view->assign('title','后台管理-新闻列表');
        return $this->view->fetch('newsList');
    }

    //添加新闻界面
    public function newsAdd()
    {
        $this->isLogin();
        return $this->view->fetch('newsAdd');
    }

    //添加新闻操作
    public function doAdd()
    {
        if (Request::isPost()){
            $data = Request::post();

            $rule = 'app\admin\common\validate\News';//自定义的验证规则
            $res = $this->validate($data, $rule); //开始验证

            if ($res !== true) {
                return ['status' => 0, 'msg' => $res];
            }

            //验证图片信息是否为空
            if (empty($_FILES['title_img']['name'])) {
                return ['status' => 0, 'msg' => '请选择上传图片'];
            }
            //获取图片信息
            $file = Request::file('title_img');

            //文件信息验证,再上传到指定目录
            $img_url = 'uploads/news/';
            $info = $file->validate(['size' => 1000000, 'ext' => 'jpg,jpeg,png,gif'])->move($img_url);
            if ($info) {
                $data['title_img'] = $img_url.str_replace('\\','/',$info->getSaveName());
            } else {
                return ['status' => 0, 'msg' => '上传图片错误'];
            }

            if (NewsModel::create($data)) {
//                $this->redirect('admin/user/login',302);
                return ['status' => 1, 'msg' => '添加新闻成功'];
            }
            return ['status' => -1, 'msg' => '添加新闻失败'];

        } else {
            return ['status' => -1, 'msg' => '请求类型错误'];
        }
    }

    //编辑新闻界面
    public function newsEdit()
    {
        $this->isLogin();
        $id = Request::param('id');

        $newsInfo = NewsModel::where('id',$id)->find();
        $this->view->assign('newsInfo',$newsInfo);

        return $this->view->fetch('newsEdit');
    }

    //编辑新闻操作
    public function doEdit()
    {
        $data = Request::post();
        $rule ='app\admin\common\validate\News';//自定义的验证规则
        $res = $this->validate($data,$rule); //开始验证

        if ($res !== true){
            return ['status' => 0, 'msg' => $res];
        }
        $id = $data['id'];
        unset($data['id']);

        //验证图片信息是否为空
        if (!empty($_FILES['title_img']['name'])) {
            //获取图片信息
            $file = Request::file('title_img');

            //文件信息验证,再上传到指定目录'uploads/news/'
            $img_url = 'uploads/news/';
            $info = $file->validate(['size' => 1000000, 'ext' => 'jpg,jpeg,png,gif'])->move($img_url);
            if ($info) {
                $data['title_img'] = $img_url.str_replace('\\','/',$info->getSaveName());

                //上传图片成功后把旧图删除
                $old_img_url = NewsModel::field('title_img')->where('id',$id)->find();
                if (file_exists($old_img_url['title_img'])){
                    unlink($old_img_url['title_img']);
                }
            } else {
                return ['status' => 0, 'msg' => '上传图片错误'];
            }
        }

        if (NewsModel::where('id',$id)->update($data)){

            return ['status' => 1, 'msg' => '修改新闻成功'];
        }
        return ['status'=> -1,'msg'=>'修改新闻失败'];
    }

    //删除新闻
    public function doDelete()
    {
        $newsId = Request::post('id');

        if (NewsModel::where('id',$newsId)->delete()){
            return ['status'=> 1,'msg'=>'新闻删除成功'];
        }
        return ['status'=> -1,'msg'=>'新闻删除失败'];
    }
}