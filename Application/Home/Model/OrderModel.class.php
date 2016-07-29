<?php
namespace Home\Model;
use Think\Model;
use Tools\Cart;

class OrderModel extends Model {

	public function _after_insert($data, $options) {
		
		//从购物车获取商品信息
		$cart = new Cart();
		$cartInfo = $cart->getCartInfo();
		//拼接数据
		foreach ($cartInfo as $k => $v) {
			$post['order_id'] = $data['id'];
			$post['goods_id'] = $k;
			$post['goods_price'] = $v['goods_price'];
			$post['goods_number'] = $v['goods_buy_number'];

			M('order_goods')->add($post);
		}
		//order_goods表插入成功, 清除购物车数据
		$cart->delall();
	}
}
