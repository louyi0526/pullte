<?php
namespace app\admin\common\Validate;

use think\Validate;
class Product extends Validate
{
    //添加用户验证规则
    protected $rule=[
//        'title_img|标题图片'=>'require',
        'title|产品标题'=>'require|length:4,20|chsAlphaNum',
        'content|产品内容'=>'require',
    ];

}