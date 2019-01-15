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

function randString($num, $type = 'str')
{
    if($type == 'str'){
        $base = 'qwertyuipasdfghjkxcvbnm3456789QWERTYUOPASDFGHJKLXCVBNM';
        $b = 53;
    }else{
        $base = '0987654321';
        $b = 9;
    }
    $str = '';
    for ($i = 0; $i < $num; $i++){
        $str .= $base{rand(0,$b)};
    }
    return $str;
}