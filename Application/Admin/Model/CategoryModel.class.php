<?php
namespace Admin\Model;

use Think\Model;

class CategoryModel extends Model {

	//category 插入之后的回调方法(自动调用)   ---组装cate_path, cate_level字段数据
	public function _after_insert($data, $options) {
		//是否顶级分类
		if ($data['pid'] == 0) {
			//是顶级分类
			$data = array(
				'id' => $data['id'],
				'cate_path' => $data['id'],
				'cate_level' => 0,
			);
		} else {
			//是子分类
			//获取当前分类的父分类的全路径, 拼装出当前分类的全路径
			$cateInfo = M('category') -> find($data['pid']);
			$cate_path = $cateInfo['cate_path'] . '-' . $data['id'];
			//当前分类的level等级
			$cate_level = $cateInfo['cate_level'] + 1;
			//最终拼装
			$data = array(
				'id' => $data['id'],
				'cate_path' => $cate_path,
				'cate_level' => $cate_level,
			);
		}

		//更新 (注意这里只能用M方法, 不然后面的'更新回调方法'会BUG)
		M('category') -> save($data);
	}

	//category 更新之后的回调方法(自动调用)   ---组装cate_path, cate_level字段数据
	public function _after_update($data, $options) {
		//是否顶级分类
		if ($data['pid'] == 0) {
			//是顶级分类
			$data = array(
				'id' => $data['id'],
				'cate_path' => $data['id'],
				'cate_level' => 0,
			);
		} else {
			//是子分类
			//获取当前分类的父分类的全路径, 拼装出当前分类的全路径
			$cateInfo = M('category') -> find($data['pid']);
			$cate_path = $cateInfo['cate_path'] . '-' . $data['id'];
			//当前分类的level等级
			$cate_level = $cateInfo['cate_level'] + 1;
			//最终拼装
			$data = array(
				'id' => $data['id'],
				'cate_path' => $cate_path,
				'cate_level' => $cate_level,
			);
		}

		//更新 (注意这里只能用M方法, 不然后面的'更新回调方法'会BUG)
		M('category') -> save($data);
	}

}
