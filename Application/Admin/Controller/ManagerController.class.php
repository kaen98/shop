<?php
namespace Admin\Controller;
use Think\Controller;

//后台index控制器
class ManagerController extends AdminController {

	public function login() {
		//判断有无提交
		if (IS_POST) {
			//接受提交数据
			$post = I('post.');
			//核实验证码
			$verify = new \Think\Verify();
			if (!$verify -> check($post['code'])) {
				#验证码不正确则跳转登录页面
				$this -> error('验证码不正确', U('login'), 2);
				exit();
			}
			//验证登录
			#实例化
			$managerModel = M('Manager');
			#根据用户输入的用户,查询出用户信息
			$user = $managerModel -> where("name = '{$post['name']}'") -> find();
			#验证用户输入的密码和数据库中的密码
			if ($user['pwd'] !== $post['pwd']) {
				#登录失败
				$this -> error('用户名或者密码错误', U('login'), 2);
				exit;
			} else {
				#登录成功
				#登录状态持久化
				session('user_id', $user['id']);
				session('user_name', $user['name']);
				session('user_role_id', $user['role_id']);
				#重定向到后台首页
				$this -> redirect('Index/index');
			}

		}
		$this -> display();
	}

	//验证码
	public function getCode() {
		//验证码类配置
		$config = array(
			'useImgBg'  =>  false,           // 使用背景图片 
			'fontSize'  =>  8,              // 验证码字体大小(px)
			'useCurve'  =>  false,            // 是否画混淆曲线
			'useNoise'  =>  false,            // 是否添加杂点	
			'imageH'    =>  25,               // 验证码图片高度
			'imageW'    =>  50,               // 验证码图片宽度
			'length'    =>  3,               // 验证码位数
			'fontttf'   =>  '5.ttf',              // 验证码字体，不设置随机获取
			
		);
		//实例化验证码类
		$verify = new \Think\Verify($config);
		//获取验证码
		$verify -> entry();
	}

	//退出系统
	public function logout() {
		#清除session
		session(null);
		#跳转回登录页
		$this -> redirect('login');
	}
}
