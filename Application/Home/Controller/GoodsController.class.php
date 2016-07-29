<?php
namespace Home\Controller;
use Think\Controller;

//商品控制器
class GoodsController extends HomeController {
	//显示商品列表
	public function showlist () {
		//获取cat_id
		$cat_id = I('get.cat_id');
		//因数据表结构的原因，按分类来查询数据有2种情况：a.顶级分类回显  b.次级分类回显（一级 二级）
		//首先，要判断分类id 是否顶级分类
		$categoryInfo = M('category')->find($cat_id);

		if ($categoryInfo['cate_level'] == 0) {
			//顶级分类(goods商品表中存着顶级分类cat_id, 可以对应上具体的商品id)
			//获得商品的id集合
			$goodsInfo = M('goods')->field('group_concat(id) goods_ids')->where('cat_id=' . $cat_id)->find(); 
			$goods_ids = $goodsInfo['goods_ids'];
			
		} else {
			//次级分类(而次级分类cat_id存储在goods_cat表中，可以对应上具体的商品id)
			$goodsInfo = M('goods_cat')->field('group_concat(goods_id) goods_ids')->where('cat_id=' . $cat_id)->find();
			$goods_ids = $goodsInfo['goods_ids'];
		}

		if (!$goods_ids) {
			$this -> error('该类别无商品', U('index/index'), 3);exit;
		}
		$goodsInfo = M('goods')->where('id in ('.$goods_ids.')')->select();
		$this->assign('goodsInfo', $goodsInfo);

		$this -> display();
	}
	//商品详情
	public function detail() {
		
		$gid = I('get.gid');
		//查询出基本商品信息
		$goodsInfo = M('goods') -> find($gid);
		$this->assign('goodsInfo', $goodsInfo);

		//查询出商品的唯一属性
		$sql = "SELECT
				g.*, a.attr_name,
				a.attr_sel
			FROM
				goods_attr AS g
			LEFT JOIN attr AS a ON g.attr_id = a.id
			WHERE
				a.attr_sel = '0'
			AND g.goods_id = {$gid}";
		$goods_attr_one = M()->query($sql);
		$this->assign('goods_attr_one', $goods_attr_one);		

		//查询出商品的多选属性
		$sql = "SELECT
				g.*, a.attr_name,
				a.attr_sel,GROUP_CONCAT(g.attr_value) AS attr_values
			FROM
				goods_attr AS g
			LEFT JOIN attr AS a ON g.attr_id = a.id
			WHERE
				a.attr_sel = '1'
			AND g.goods_id = {$gid} GROUP BY a.id"; 

		$goods_attr_more =M()->query($sql);

		foreach ($goods_attr_more as $k => $v) {
			$goods_attr_more[$k]['attr_value'] = explode(',', $v['attr_values']);
		}
		//echo '<hr>';
		//dump($goods_attr_more);

		$this->assign('goods_attr_more', $goods_attr_more);

		//查询商品相册
		$goods_pics = M('goods_pics')->where('goods_id=' . $gid)->select();
		$this->assign('goods_pics', $goods_pics);
		$this -> display();
	}
}
