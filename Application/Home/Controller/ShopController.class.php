<?php
namespace Home\Controller;

use Tools\Cart;
//购物车和订单处理控制器
class ShopController extends HomeController {

	//商品添加到购物车
	public function addCart() {
		//接受ajax请求 传递的goods_id
		$goods_id = I('post.goods_id');
		//获取商品信息
		$goodsData = M('goods')->find($goods_id);
		//组装要传递的数据
		$goods_info['goods_id'] = $goodsData['id'];
		$goods_info['goods_name'] = $goodsData['goods_name'];
		$goods_info['goods_price'] = $goodsData['goods_price'];
		$goods_info['goods_buy_number'] = 1;
		$goods_info['goods_total_price'] = $goodsData['goods_price'];
		//实例化cart对象
		$cart = new Cart();
		//添加到购物车
		$cart->add($goods_info);
		//获取购物车商品数量和总价格, 并返回
		$this->ajaxReturn($cart->getNumberPrice());
	}

	//购物车展示
	public function flow1() {
		//实例化cart对象
		$cart = new Cart();
		//获取购物车信息
		$info = $cart->getCartInfo();
		$this->assign('cartInfo', $info);

		//获取购物车总金额
		$np = $cart->getNumberPrice();
		$this->assign('numberPrice', $np);

		//info数组的key键里存着商品id  goods_id,遍历出来,保存为数组
		foreach ($info as $key => $v) {
			$tmp[] = $key;
		}
		//商品id 由数组转为字符串
		$goods_ids = implode(',', $tmp);
		//查询出商品信息,包含了商品的log图片地址
		$goods_logos = D('goods')->select($goods_ids); 
		//只需要商品id和商品 logo(small)
		//$logos 就是我们要的logo图片信息 array('goods_id'=>logo地址, 'goods_id'=>logo地址)
		foreach($goods_logos as $v) {
			$logos[$v['id']] = $v['goods_small_logo'];
		}
		$this->assign('logos', $logos);
		$this->display();
	}

	//ajax  修改商品数量(减少 添加)
	public function changeNumber() {
		//接受goods_id
		$goods_id = I('post.goods_id');
		$goods_num = I('post.goods_num');
		//实例化购物车类
		$cart = new Cart();
		$goods_total_price = $cart->changeNumber($goods_num, $goods_id);
		//返回小计价格
		$this->ajaxReturn(array($goods_total_price));
	}

	//ajax 删除购物车商品
	public function delGoods() {
		$goods_id = I('post.goods_id');
		$cart = new Cart();
		$cart->del($goods_id);

		//查询现在的总价
		
		$this->ajaxReturn($cart->getNumberPrice());
	}

	public function flow2() {
		//判断有无登录
		if (session('?user_id')) {
			//有无提交
			if (IS_POST) {
				$post = I('post.');
				//拼凑数据 post中字段数据
				$post['order_number'] = 'php47' . date('YmdHis', time()) . mt_rand(1000, 9999);
				//订单总价格,从购物车中查询
				$cart = new Cart();
				$np = $cart->getNumberPrice();
				$post['order_price'] = $np['price'];
				//时间
				$post['add_time'] = $post['upd_time'] = time();
				//用TP自带的_after_insert在add后自动插入
				if (D('order')->add($post)) {
					//在成功生成订单后,录入商品订单关系数据(因为要使用order_id, 必须先产生订单id, 在把 order_id插入到order_goods表中)
					die;
				} else {
					$this->error('订单生成失败', U('flow1'), 1);
				}

			}
		} else {	
			//保存跳转登录前的地址
			session('histroy_url', 'shop/flow2');
			//未登录,跳转登录
			$this->redirect('User/login');
			exit();
		}

		//商品清单展示
		$cart = new Cart();
		$goodsInfo = $cart->getCartInfo();
		$this->assign('goodsInfo', $goodsInfo);
		foreach ($goodsInfo as $k => $v) {
			$tmp[] = $k; 
		}
		$goodIds = implode(',', $tmp);
		//查询出商品信息(包含小log图的地址)
		$goods = M('goods')->select($goodIds);
		foreach ($goods as $v) {
			$s_log[$v['id']] = $v['goods_small_logo'];
		}
		$this->assign('s_log', $s_log);

		//查询购物车商品总价
		$numberPrice = $cart->getNumberPrice();
		$this->assign('numberPrice', $numberPrice);
		
		$this->display();
	}
}
