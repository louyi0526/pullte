<?php
namespace app\admin\common\Validate;

use think\Validate;
class User extends Validate
{
    //添加用户验证规则
    protected $rule=[
        'login_name|用户名称'=>'require|length:5,20|chsAlphaNum|unique:user',
        'password|密码'=>'require|length:6,20|alphaNum|confirm',
        'role_id|用户组'=>'require|integer',
    ];

}