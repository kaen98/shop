<?php 
namespace Home\Controller;
use Think\Controller;

class UserController extends Controller {

	//用户登录
	public function login () {
		//如果是表单提交，获取数据，校验，持久化用户数据
		if (IS_POST) {
			$userInfo = I('post.');
			//验证码判断
			if ($this->checkCode($userInfo['checkcode'])) {
				//验证用户密码
				$info = D('user')->where("name='{$userInfo['name']}' AND pwd='{$userInfo['pwd']}'")->find();
				if ($info) {
					//信息持久化
					session('user_name', $info['name']);
					session('user_id', $info['id']);
					//判断跳转登录,上一次的网址,存不存在
					if (session('?histroy_url')) {
						//销毁登录,禁止重复跳转
						session('history_url', null);
						$this->redirect('Shop/flow2');
					}
					$this->redirect('index/index');
					//要实现跳回上一页，记录来源页（存在session当中）
					
					
				} else {
					$this->error('用户名或密码错误', U('login'), 3);
				}
			} else {
				$this->error('验证码错误', U('login'), 3);
			}
		}
		$this -> display();	
	} 

	//用户注册
	public function register () {
		//有无表单输入
		if (IS_POST) {
			//接受post数据
			$post = I('post.');
			//验证验证码
			if($this->checkCode(strtolower($post['checkcode']))) {
				//2次密码是否一致
				if ($post['pwd'] === $post['password']) {
					//注册信息 入库
					$post['add_time'] = time();
					if (M('User')->add($post)) {
						$this->success('注册成功', U('User/login'), 3);
					} else {
						$this->error('注册失败，请重新注册', U('register'), 3);
					}
				} else {
					$this->error('2次输入密码不一致', U('register'), 3);
				}
			} else {
				$this->error('验证码错误', U('register'), 3);
			}
		}
		
		$this -> display();
	}


	//生成验证码
	public function getCode() {
		$config = array(
			'useImgBg'  =>  false,           // 使用背景图片 
			'fontSize'  =>  25,              // 验证码字体大小(px)
			'useCurve'  =>  false,            // 是否画混淆曲线
			'useNoise'  =>  false,            // 是否添加杂点	
			'imageH'    =>  0,               // 验证码图片高度
			'imageW'    =>  0,               // 验证码图片宽度
			'length'    =>  4,               // 验证码位数
			'fontttf'   =>  '5.ttf',              // 验证码字体，不设置随机获取
			'bg'        =>  array(243, 251, 254),  // 背景颜色
			'reset'     =>  true,           // 验证成功后是否重置
		);
		$verify = new \Think\Verify($config);
		$verify->entry();
	}

	//验证码校验
	public function checkCode($code) {
		$verify = new \Think\Verify();
		return $verify->check($code);
	}
}


