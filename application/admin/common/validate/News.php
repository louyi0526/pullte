<?php
namespace app\admin\common\Validate;

use think\Validate;
class News extends Validate
{
    //添加用户验证规则
    protected $rule=[
//        'title_img|标题图片'=>'require',
        'title|新闻标题'=>'require|length:4,20|chsAlphaNum',
        'subhead|内容简介'=>'require|length:4,50|chsAlphaNum',
        'class|新闻分类'=>'require|integer',
        'content|新闻内容'=>'require',
    ];

}