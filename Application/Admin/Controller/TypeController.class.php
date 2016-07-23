<?php
namespace Admin\Controller;

class TypeController extends AdminController {

	//商品类型列表
	public function showlist() {
		$typeInfo = M('type') -> select();
		$this -> assign('typeInfo', $typeInfo);
		$this -> display();
	}

	//添加商品类型
	public function tianjia() {
		if (IS_POST) {
			$post = I('post.');
			if (M('type') -> add($post)) {
				$this -> redirect('showlist');
			} else {
				$this -> error('添加失败', U('tianjia'), 3);
			}
		}
		$this -> display();
	}
}
