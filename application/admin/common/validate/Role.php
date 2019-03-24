<?php
namespace app\admin\common\Validate;

use think\Validate;
class Role extends Validate
{
    //添加用户验证规则
    protected $rule=[
        'role_name|角色名称'=>'require|length:4,20|chs|unique:role',
    ];

}