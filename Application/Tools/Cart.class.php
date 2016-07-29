<?php
namespace Tools;
/**
  * 购物车类
  * 实现对购物车里边商品的添加、删除操作
  */
class Cart{
    //购物车的一个属性，用于存放商品信息的
    var $cartInfo = array();

    function __construct(){
        session_start();
        $this -> loadData();
    }

    /**
     * 取得购物车里边已经存放的商品信息
     * 该方法是该类里边第一个被执行的方法
     * 在类的构造函数里边调用
     */
    function loadData(){
        if(isset($_SESSION['cart'])){
            //取得购物车里边已经存放的商品信息(并反序列化)
            $this -> cartInfo = $_SESSION['cart'];
        }
    }

    /**
     * 将商品添加到购物车里边
     * @param $goods  array('goods_id'=>'10','goods_name'=>'诺基亚','goods_price'=>'1750','goods_buy_number'=>'1','goods_total_price'=>1750);
    */
    function add($goods){
        $pd_id = $goods['goods_id'];
        //对重复购买的商品要判断(还要判断当前的购物车是否为空，即是否是第一次添加商品)
        if(!empty($this->cartInfo) && array_key_exists($pd_id, $this->cartInfo)){
            //1数量增加
            $this->cartInfo[$pd_id]['goods_buy_number'] +=1;
            //2单间商品的总价增加 单间商品总价=单价*数量
            $this->cartInfo[$pd_id]['goods_total_price'] = $this->cartInfo[$pd_id]['goods_price']*$this->cartInfo[$pd_id]['goods_buy_number'];
        } else {
            $this -> cartInfo[$pd_id] = $goods;
        }

        $this -> saveData();//将刷新的数据重新存入cookie 
    }

    /**
     *删除购物车里边指定的商品
     *@param $goods_id 被删除商品的id信息
     */
    function del($goods_id){
        if(array_key_exists($goods_id, $this -> cartInfo)){
            unset($this -> cartInfo[$goods_id]);
        }
        $this -> saveData();//将刷新的数据重新存入cookie        
    }

    /**
     * 清空购物车
     */
    function delall(){
        unset($this->cartInfo);
        $this -> saveData();//将刷新的数据重新存入cookie
    }

    /**
     * 商品数量发生变化要执行的步骤
     * @param $pd_number 商品修改后的数量
     * @param $pd_id     被修改的商品的id
     */
    function changeNumber($pd_number,$pd_id){
        
        //1修改商品的数量
        $this->cartInfo[$pd_id]['goods_buy_number'] = $pd_number;
        //2修改单件商品的小计价格
        $this->cartInfo[$pd_id]['goods_total_price'] = $pd_number*$this->cartInfo[$pd_id]['goods_price'];

        $this -> saveData();//将刷新的数据重新存入cookie
        
        return $this->cartInfo[$pd_id]['goods_total_price'];
    }

    /**
     * 获得购物车的商品数量和总价格
     */
    function getNumberPrice(){
        $number = 0;//商品数量
        $price = 0;//商品总价钱
        foreach($this->cartInfo as $_k => $_v){
            $number   += $_v['goods_buy_number'];
            $price += $_v['goods_total_price'];
        }
        $arr['number'] = $number;
        $arr['price'] = $price;
        
        return $arr;
    }
    
    function getCartInfo(){
        return $this -> cartInfo;
    }
    
    /**
     * 将购物车的商品信息存入购物车
     */
    function saveData(){
        $data = $this -> cartInfo;
        $_SESSION['cart'] = $data;
        //setcookie('cart',$data,time()+3600,'/');
    }
}
