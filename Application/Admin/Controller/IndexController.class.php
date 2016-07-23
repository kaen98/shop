<?php
namespace Admin\Controller;
use Think\Controller;

//后台index控制器
class IndexController extends AdminController {


	public function index () {
		$this -> display();
	}

	public function top() {
		$this -> display();
	}

	public function center() {
		$this -> display();
	}

	public function down() {
		$this -> display();
	}

	public function left() {
		//判断是否是root用户
		if (session('user_role_id') == 0) {
			#是超级用户
			#显示顶级的auth信息
			$authinfotop = M('auth') -> where('auth_level=0')  -> select();
			#显示子级的auth信息
			$authinfosec = M('auth') -> where('auth_level=1') -> select();

		} else {
			#不是超级用户
			//根据RBAC权限 显示左侧菜单
			#当前登录用户的role_auth_ids
			$user_role_id = session('user_role_id');
			$role = M('role') -> find($user_role_id);
			$role_auth_ids = $role['role_auth_ids'];
			#显示顶级的auth信息
			$authinfotop = M('auth') -> where('auth_level=0 and id in ('. $role_auth_ids .')') -> select();
			#显示子级的auth信息
			$authinfosec = M('auth') -> where('auth_level=1 and id in ('. $role_auth_ids .')') -> select();
		}

		$this -> assign('authinfotop', $authinfotop);
		$this -> assign('authinfosec', $authinfosec);
		$this -> display();

		
	}
}
