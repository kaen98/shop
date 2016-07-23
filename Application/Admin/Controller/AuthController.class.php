<?php
namespace Admin\Controller;
use Think\Controller;


class AuthController extends AdminController {

	public function showlist() {
		$authes = M('auth') -> order('auth_path') -> select();
		$this -> assign('authes', $authes);
		$this -> display();
	}


	public function tianjia() {
		//判断有无提交
		if (IS_POST) {
			//接受post数据
			$post = I('post.');
			// dump($post);die;
			//先把auth_name,auth_pid,auth_c,auth_a插入表中,获得插入id, 再更新auth_path, auth_level字段
			if (!$id = M('auth') -> add($post)) {
				#添加失败, 跳到添加页面
				$this -> error('添加失败', U('tianjia'), 2);exit;
			}
			//组装auth_path字段  组装auth_level字段
			if ($post['auth_pid'] == 0) {
				#父权限 为顶级权限的话, auth_path字段就为自身的id
				$data['auth_path'] = $id;
				$data['auth_level'] = 0;
			} else {
				#父权限不为顶级权限, 有规律
				#根据auth_pid,查出父权限的auth_path
				$auth_parent = M('auth') -> find($post['auth_pid']);
				#开始拼接
				$data['auth_path'] = $auth_parent['auth_path'] .'-'. $id;
				$data['auth_level'] = $auth_parent['auth_level'] + 1;
			}

			//更新auth_path, auth_level字段
			$data['id'] = $id;
			if (M('auth') -> save($data)) {
				#更新成功
				$this -> success('添加成功', U('showlist'), 2);exit();
			} else {
				#更新失败
				$this -> error('添加失败', U('tianjia'), 2);exit();
			}

		}

		//父权限下拉框数据
		$authes = M('auth') -> order('auth_path') -> select();
		$this -> assign('authes', $authes);
		//加载'添加'模板
		$this -> display();
	}
}
