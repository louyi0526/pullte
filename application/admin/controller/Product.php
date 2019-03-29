<?php

namespace app\admin\controller;


use app\admin\common\controller\Base;
use app\admin\common\model\Product as ProductModel;
use think\facade\Session;
use think\facade\Request;

class Product extends Base
{
    //产品列表界面
    public function productList()
    {
        $this->isLogin();
        $productList= ProductModel::order('id desc')->paginate(10);
        $this->view->assign('productList',$productList);
        $this->view->assign('title','后台管理-产品列表');
        return $this->view->fetch('productList');
    }

    //添加产品界面
    public function productAdd()
    {
        $this->isLogin();
        return $this->view->fetch('productAdd');
    }

    //添加产品操作
    public function doAdd()
    {
        if (Request::isPost()){
            $data = Request::post();

            $rule = 'app\admin\common\validate\Product';//自定义的验证规则
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
            $img_url = 'uploads/product/';
            $info = $file->validate(['size' => 1000000, 'ext' => 'jpg,jpeg,png,gif'])->move($img_url);
            if ($info) {
                $data['title_img'] = $img_url.str_replace('\\','/',$info->getSaveName());
            } else {
                return ['status' => 0, 'msg' => '上传图片错误'];
            }

            if (ProductModel::create($data)) {
//                $this->redirect('admin/user/login',302);
                return ['status' => 1, 'msg' => '添加产品成功'];
            }
            return ['status' => -1, 'msg' => '添加产品失败'];

        } else {
            return ['status' => -1, 'msg' => '请求类型错误'];
        }
    }

    //编辑产品界面
    public function productEdit()
    {
        $this->isLogin();
        $id = Request::param('id');

        $productInfo = ProductModel::where('id',$id)->find();
        $this->view->assign('productInfo',$productInfo);

        return $this->view->fetch('productEdit');
    }

    //编辑产品操作
    public function doEdit()
    {
        $data = Request::post();
        $rule ='app\admin\common\validate\Product';//自定义的验证规则
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

            //文件信息验证,再上传到指定目录'uploads/product/'
            $img_url = 'uploads/product/';
            $info = $file->validate(['size' => 1000000, 'ext' => 'jpg,jpeg,png,gif'])->move($img_url);
            if ($info) {
                $data['title_img'] = $img_url.str_replace('\\','/',$info->getSaveName());

                //上传图片成功后把旧图删除
                $old_img_url = ProductModel::field('title_img')->where('id',$id)->find();
                if (file_exists($old_img_url['title_img'])){
                    unlink($old_img_url['title_img']);
                }
            } else {
                return ['status' => 0, 'msg' => '上传图片错误'];
            }
        }

        if (ProductModel::where('id',$id)->update($data)){

            return ['status' => 1, 'msg' => '修改产品成功'];
        }
        return ['status'=> -1,'msg'=>'修改产品失败'];
    }

    //删除产品
    public function doDelete()
    {
        $productId = Request::post('id');

        if (ProductModel::where('id',$productId)->delete()){
            return ['status'=> 1,'msg'=>'产品删除成功'];
        }
        return ['status'=> -1,'msg'=>'产品删除失败'];
    }
}