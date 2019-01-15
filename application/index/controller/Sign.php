<?php
/**
 * Created by PhpStorm.
 * User: qingyun
 * Date: 2018/12/26
 * Time: 下午7:48
 */

namespace app\index\controller;

use app\admin\model\Admin;
use think\Controller;
//use think\Error;
use think\facade\Request;
use think\captcha\Captcha;


class Sign extends Controller
{
    public function in()
    {
        return view();

    }

    public function up()
    {
//        return view();
        return $this->fetch();
    }
















}
