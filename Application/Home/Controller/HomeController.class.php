<?php
namespace Home\Controller;

use Think\Controller;

class HomeController extends Controller {


	public function __construct() {
		parent::__construct();
	
	 	$cateInfoA = D('category')->where('cate_level=0')->select();
	 	$cateInfoB = D('category')->where('cate_level=1')->select();
	 	$cateInfoC = D('category')->where('cate_level=2')->select();

	 	$this->assign('cateInfoA', $cateInfoA);
	 	$this->assign('cateInfoB', $cateInfoB);
	 	$this->assign('cateInfoC', $cateInfoC);

	 	
	}
}
