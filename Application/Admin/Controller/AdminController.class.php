<?php
namespace Admin\Controller;
use Think\Controller;

//中间控制器 RBAC权限控制
class AdminController extends Controller {

	//构造方法
	public function __construct() {
		parent::__construct();
		
		//获得ac  控制器-方法
		$now_ac = CONTROLLER_NAME . '-' . ACTION_NAME;
		$now_ac = strtolower($now_ac);	
		//判断当前ac 是否在 允许的ac列表内
		
		//有无登录
		if (!session('?user_id')) {
			//未登录
			$allow_ac = 'Manager-getCode, Manager-login';
			$allow_ac = strtolower($allow_ac);
			if (strpos($allow_ac, $now_ac) === false) {
				$this -> error('请登录', U('Manager/login'), 2);
			}
		} else {
			//已登录
			
			//如果不是超级管理员, RBAC权限判断
			if (session('user_name') !== 'admin') {
				//获取当前登录用户的ac权限字符串
				$roleInfo = M('role') -> find(session('user_role_id'));
				$role_ac = $roleInfo['role_auth_ac'];
				

				//默认可以访问的控制器-方法
				$allow_ac = 'Index-index, Index-top, Index-left, Index-center, Index-down, Manager-login, Manager-logout, Manager-getCode';
				$role_ac = $role_ac . ', ' . $allow_ac;
				$role_ac = strtolower($role_ac);

				if (strpos($role_ac, $now_ac)===false) {
					exit(非法访问);
				} 
			}


		}
		


	}
}
