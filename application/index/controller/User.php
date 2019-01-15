<?php
/**
 * Created by PhpStorm.
 * User: qingyun
 * Date: 19/1/12
 * Time: 19:29
 */

namespace app\index\controller;

use Qcloud\Sms\SmsSingleSender;
use think\Controller;
use think\captcha\Captcha;
use think\Db;



class User extends Controller{

    public function captcha()
    {
        $config = [
            'imageH' => 55,
            'imageW' => 170,
            'length' => 4,
            'useCurve' => false,
            'useNoise' => false,
            'fontsize' => 20,
//            'fontttf' => '6.ttf'
        ];
        $captcha = new Captcha($config);
        return $captcha->entry();
    }

    public function add(){
        if ($this->request->isPost()){
            //接受前端发来的数据
            $data = $this->request->param();
//            return $data;
            //todo 验证前端发来的数据
            $yzm = $data['yzm'];
            $code = $data['code'];
            $mobile = $data['mobile'];
            $password = $data['password'];
            $c = new Captcha();
            if (!$c->check($yzm)){
                return ['code'=>0,'info'=>'请输入正确的验证码'];

            }
            $regMobile = session('regMobile');
//
            preg_match('/^1[356789]\d{9}$/',$data['mobile'],$match);
            if (!$match){
                return ['code'=>0,'info'=>'请输入有效的手机号'];
            }
            //定义验证规则
            $rules = [
                'password' => 'require|length:6,16',
                'repassword' => 'require|confirm:password'
            ];
            $msg = [
                'password.require' => '密码为必填项',
                'password.length' => '密码长度应在6-16位之间',
                'repassword.require' => '重复密码为必填项',
                'repassword.confirm' => '两次密码输入不一致'
            ];
            //执行验证
            $res = $this->validate($data,$rules,$msg);
            //验证失败后返回数据
            if ($res !== true){
                return ['code'=> 0, 'info'=> $res];
            }

            //检验一下该邮箱是否已经被注册过
            $m = Db::table('user')->get(['mobile'=>$mobile]);

            if ($m){
                return ['code'=>0,'info'=>'该邮箱已经被使用'];
            }
            //对密码进行加密处理
            $password = password_hash($password,PASSWORD_DEFAULT);
            //将用户发来的数据进行入库处理
//            return $password;
//            $save = Db::table('user')->insert($data);
//             Db::execute("insert into user (mobile,password) values ($mobile,$password)");
            $save = Db::table('user')->insert(['mobile'=>$mobile,'password'=>$password]);
            if ($save){
                return ['code'=>1,'info'=>'注册成功'];
            }else{
                return ['code'=>0,'info'=>'注册失败'];
            }


        }
    }

    #发送短信验证码
    public function sendMobile(){

        //生成一个4个数字的验证码
        $mobileCode = randString(4,'num');
        $mobile = $this->request->post('mobile');
//        return 123;
        //todo 再次验证前端发来的手机号
        preg_match(config('myself.mobile_preg'),$mobile,$match);
//        return $match;
        if ($match){
            //调用短信接口,发送短信
//            return $mobileCode;
            $sms = $this->sendSms($mobile,$mobileCode);
//            return $sms['result'];
            if ($sms['result'] == 0){
                //把验证码放入session中,并记录失效时间

                session('regMobile',['code'=>$mobileCode,'mobile'=>$mobile,'time'=>time() + 300]);
                return ['code'=>1,'info'=>'验证码发送成功,5分钟内有效'];
            }

        }else{
            return ['code'=>0,'info'=>'手机号有误'];
        }
    }

    #短信接口
    public function sendSms($phone,$code)
    {

        // 短信应用SDK AppID
        $appid =1400170241; // 1400开头
        //// 短信应用SDK AppKey
        $appkey = "9ecace73ca78cfbcf1178e135c368a55";
        // 短信模板ID，需要在短信应用中申请
        $templateId = 245450;  // NOTE: 这里的模板ID`7839`只是一个示例，真实的模板ID需要在短信控制台中申请
        // 签名
        try{
            $ssender = new SmsSingleSender($appid,$appkey);
            $result = $ssender->send(0, "86", $phone,
                "【书生主页】您的验证码为".$code."，5分钟内有效。", "", "");
//            $rsp = json_decode($result);
            //强制转换成数组
//            return $result;
            $res = (array)$result;

            //json_decode解码json字符串
           return json_decode($res[0], true);
        }catch(\Exception $e) {
            echo var_dump($e);
        }

    }



}




















