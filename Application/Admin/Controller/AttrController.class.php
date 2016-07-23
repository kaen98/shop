<?php
namespace Admin\Controller;

class AttrController extends AdminController {

	//属性列表
	public function showlist() {

		//商品类型数据 --下拉框
		$typeInfo = M('type') -> select();
		$this -> assign('typeInfo', $typeInfo);

		//attr 左连接 type
		$sql = 'SELECT attr.*, type.type_name FROM attr LEFT JOIN type ON attr.type_id = type.id';
		$attrInfo = M('attr') -> query($sql);
		$this -> assign('attrInfo', $attrInfo);
		$this -> display();
	}

	//添加属性
	public function tianjia() {
		if (IS_POST) {
			$post = I('post.');
			if (M('attr') -> add($post)) {
				$this -> redirect('showlist');
				exit;
			} else {
				$this -> error('添加失败', U('tianjia'), 3);
				exit;
			}
		}

		//商品类型数据  --下拉框
		$typeInfo = M('type') -> select();
		$this -> assign('typeInfo', $typeInfo);
		$this -> display();
	}

	//AJAX    :   根据type_id 查询 属性列表
	public function getAttrListByTypeId() {
		$type_id = I('post.type_id');
		$sql = 'SELECT attr.*, type.type_name FROM attr LEFT JOIN type ON attr.type_id = type.id where type_id = ' . $type_id;
		$attrList = M('attr') -> query($sql);
		$this -> ajaxReturn($attrList);
	}

}
