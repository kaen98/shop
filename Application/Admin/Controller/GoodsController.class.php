<?php
namespace Admin\Controller;
use Think\Controller;

class GoodsController extends Controller {

	//商品的列表
	public function showlist () {

		//获取商品信息
		//实例化模型对象
		$goodsModel = M('goods');
		$goodsdata = $goodsModel  -> order('id desc') -> select();
		$this -> assign('goodsdata', $goodsdata);
		$this -> display();
	}

	//商品的添加
	public function tianjia () {
		//判断是否是表单提交
		if (IS_POST) {
			$goodsModel = M('goods');
			if ($goodsModel -> create()) {
				if ($goodsModel -> add($post)) {
					$this -> success('添加成功', U('showlist'), 1);
					exit();
				} else {
					$this -> error('添加失败', U('tianjia'), 1);
					exit();
				}
			}
		}		
		$this -> display();
	}

	//测试数据库连接是否成功
	public function test() {
		$m = M('test');
		dump($m);
	}
}
