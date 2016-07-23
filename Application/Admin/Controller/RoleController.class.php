<?php
namespace Admin\Controller;
use Think\Controller;

//角色控制
class RoleController extends AdminController {

	//角色列表
	public function showlist () {
		$roles = M('role') -> select();
		$this -> assign('roles', $roles);
		$this -> display();
	}

	//角色修改
	public function edit() {
		
		//提交判断
		if (IS_POST) {
			//接受post数据
			$post = I('post.');
			//整理数据
			#拼接role_auth_ids字段数据
			$role_auth_ids = implode(',', $post['authids']);
			#拼接role_auth_ac字段数据(有规律:auth_level都是1)
			$authes = M('auth') -> where('auth_level=1 and id in ('. $role_auth_ids .')') -> select();
			$str = '';
			foreach ($authes as $auth) {
				$str .= $auth['auth_c'] . '-' . $auth['auth_a'] . ',';
			}
			$role_auth_ac = rtrim($str, ',');
			//更新
			$data = array(
				'id' => $post['role_id'],
				'role_auth_ids' => $role_auth_ids,
				'role_auth_ac' => $role_auth_ac,
			);
			$rst = M('role') -> save($data);
			if ($rst !== false) {
				$this -> success('修改成功', U('showlist'), 2);exit;
			} else {
				$this -> error('修改失败', U('edit', array('role_id' => $role_id)), 2);exit;
			}
		}

		//接受role_id
		$role_id = I('get.role_id');
		//当前角色的信息
		$role = M('role') -> find($role_id);
		//当前角色的role_auth_ids
		$role_auth_ids = $role['role_auth_ids'];

		//全部的顶级权限
		$authRoleTop = M('auth') -> where('auth_level=0') -> select();
		//全部的子级权限
		$authRoleSec = M('auth') -> where('auth_level=1') -> select();

		$this -> assign('role_id', $role_id);
		$this -> assign('role_auth_ids', $role_auth_ids);
		$this -> assign('authRoleTop', $authRoleTop);
		$this -> assign('authRoleSec', $authRoleSec);
		$this -> display();
	}
}
