<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

//过滤文章
function getNewsContent($content)
{
    return mb_substr(strip_tags($content),0,50).'>>>';
}

//获取用户角色名称
//function getRoleName($user_id)
//{
//    $role_id = Db::table('userrole')->where('user_id', $user_id)->value('role_id');
//
//    return Db::table('role')->where('id', $role_id)->value('role_name');
//
//}