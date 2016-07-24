<?php
namespace Admin\Controller;

class CategoryController extends AdminController {

	//分类列表
	public function showlist() {
		//查询出category表所有数据, 无限极分类排序
		$cateInfo = M('category') -> order('cate_path') ->  select();
		$this -> assign('cateInfo', $cateInfo);
		$this -> display();
	}

	
	

	//分类添加
	public function tianjia() {
		//判断有无提交
		if (IS_POST) {//有提交
			//接受post数据
			$post = I('post.');
			//cate_name  pid 字段数据入库, 
			//并利用TP的模型扩展_after_insert( )回调方法,来更新cate_path, cate_level
			if (D('category') -> add($post)) {
				$this -> success('添加分类成功', U('showlist'), 1);
			} else {
				$this -> error('添加分类失败', U('tianjia'), 1);
			}

		} else {//无提交加载模板
			//无限极分类数据
			$cateInfo = M('category') -> order('cate_path') -> select();
			$this -> assign('cateInfo', $cateInfo);
			$this -> display();
		}
		
	}

	//分类编辑
	public function edit() {
		if (IS_POST) {
			//接受post来的数据
			$post = I('post.');
			//判断修改的分类, 其下是否还有子类
			$cateSon = M('category') -> where('pid = '. $post['id']) -> select();
			if (!$cateSon) {//其下无子类,则可以修改
				//用D方法, 利用TP中模型扩展 _after_update() 回调, 来自动更新 cate_path, cate_level字段数据
				if (D('category') -> save($post) !== false) {
					$this -> success('修改成功', U('showlist'), 1);
				} else {
					$this -> error('修改失败', U('edit', array('cateid' => $post['id'])), 2);
				}


			} else {
				$this -> error('修改的分类,其下有子类, 不可修改', U('showlist'), 2);
			}

		} else {
			//接受get传递的cate_id
			$cateid = I('get.cateid');
			//回显数据, 并加载模板
			$cateInfoNow = M('category') -> find($cateid);
			//为下拉框准备数据
			$cateInfo = M('category') -> order('cate_path') -> select();

			$this -> assign('cateInfoNow', $cateInfoNow);
			$this -> assign('cateInfo', $cateInfo);

			$this -> display();
		}
	}

	//ajax接口   返回扩展分类数据
	public function getCateById() {
		$id = I('post.id');
		$cateData = M('category') -> where('pid = ' . $id) -> select();
		$this -> ajaxReturn($cateData);
	}
}
