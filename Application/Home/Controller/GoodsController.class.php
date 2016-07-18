<?php
namespace Home\Controller;
use Think\Controller;

//商品控制器
class GoodsController extends Controller {
	//显示商品列表
	public function showlist () {
		$this -> display();
	}
	//商品详情
	public function detail() {
		$this -> display();
	}
}
