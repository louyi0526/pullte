<?php
namespace app\admin\common\Validate;

use think\Validate;
class Right extends Validate
{
    //添加用户验证规则
    protected $rule=[
        'right_name|权限英文名称'=>'require|length:4,20|alpha|unique:right',
        'right_title|权限中文名称'=>'require|length:4,20|chs|unique:right',

    ];

}