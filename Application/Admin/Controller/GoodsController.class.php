<?php
namespace Admin\Controller;
use Think\Controller;

class GoodsController extends AdminController {

	//商品的列表
	public function showlist () {

		//获取商品信息
		//实例化模型对象
		$goodsModel = M('goods');
		$goodsdata = $goodsModel  -> order('id desc') -> select();
		$this -> assign('goodsdata', $goodsdata);
		$this -> display();
	}

	//商品的编辑
	public function edit() {
		//有修改提交的话则处理
		if (IS_POST) {
			$data = I('post.');
			$data['update_time'] = time();
			//logo上传修改
			$this -> upload($data);
			//商品相册多图上传
			$this -> uploadPic($data['id']);

			//修改
			$rst = M('goods') -> save($data);
			if ($rst) {
				$this -> success('修改成功', U('showlist'), 3);
				exit();
			} else {
				$this -> error('修改失败', U('edit', array('id' => $data['id'])), 3);
				exit();
			}

		}

		//回显商品的信息修改表单
		$id = I('get.id');
		//商品数据(根据id)
		$goods = M('goods') -> find($id);
		//商品相册数据(根据goods_id)
		$goods_pics = M('goods_pics') -> where('goods_id=' . $id) -> select();
		$this -> assign('goods', $goods);
		$this -> assign('goods_pics', $goods_pics);
		$this -> display();
	}

	//删除商品相册的ajax请求
	public function delPic() {
		$id = I('post.id');
		//根据id,查出商品相册的一套图片
		$goods_pic = M('goods_pics') -> find($id);
		//删除磁盘上的一套相册
		if ($goods_pic) {
			unlink(ROOT_PATH . $goods_pic['goods_pics_b']);
			unlink(ROOT_PATH . $goods_pic['goods_pics_m']);
			unlink(ROOT_PATH . $goods_pic['goods_pics_s']);
		}
		//删除表中的记录
		$rst = M('goods_pics') -> delete($id);
		if ($rst) {
			$this -> ajaxReturn(array('status' => '1', 'msg' => 'Ok'));
		}
	}

	//商品的添加
	public function tianjia () {

		//判断是否是表单提交
		if (IS_POST) {
			$data = I('post.');
			$goodsModel = M('goods');
			$data['add_time'] = time();
			//logo上传
			$this -> upload($data);
			

			if ($goodsModel -> create()) {
				if ($id = $goodsModel -> add($data)) {
					//相册多图上传(必须要商品基本信息先入库,获取它的id,再操作)
					$this -> uploadPic($id);
					//添加商品属性信息(必须要商品基本信息先入库,获取它的id,再操作)
					$this -> addGoodsAttr($id);
					//添加扩展分类信息(必须要商品基本信息先入库,获取它的id,再操作)
					$this -> addCate($id);
					$this -> success('添加成功', U('showlist'), 1);
					exit();
				} else {
					$this -> error('添加失败', U('tianjia'), 1);
					exit();
				}
			}
		}
		//加载商品分类数据---下拉框
		$cateInfo = M('category') -> where('pid = 0') -> select();
		$this -> assign('cateInfo', $cateInfo);

		//加载商品类型数据  ---下拉框
		$typeInfo = M('type') -> select();
		$this -> assign('typeInfo', $typeInfo);		
		$this -> display();
	}

	//添加扩展分类信息(必须要商品基本信息先入库,获取它的id,再操作)
	public function addCate($goods_id) {
		$cate1 = I('post.cate1');
		$cate2 = I('post.cate2');

		M('goods_cat') -> add(array(
				'goods_id' => $goods_id,
				'cat_id' => $cate1,
			));

		M('goods_cat') -> add(array(
				'goods_id' => $goods_id,
				'cat_id' => $cate2,
			));
	}

	//添加商品属性信息
	public function addGoodsAttr($goods_id) {
		$attrids = I('post.attrids');
		//有商品属性信息的提交
		if (!empty($attrids)) {
			//遍历 判断提交的表单是唯一还是多选
			foreach ($attrids as $k => $v) {
				//数组是多选, 字符串是唯一
				if (is_array($v)) {
					//多选,还要进行循环遍历
					foreach ($v as $kk => $vv) {
						//组装插入数据
						$arr = array(
							'goods_id' => $goods_id,
							'attr_id' => $k,
							'attr_value' => $vv 
						);
						M('goods_attr') -> add($arr);
					}
				} else {
					//唯一
					//组装插入数据
					$arr = array(
						'goods_id' => $goods_id,
						'attr_id' => $k,
						'attr_value' => $v
					);
					M('goods_attr') -> add($arr);
				}
			}
		}
	}


	//logo图片上传
	public function upload(&$data) {
		//判断是否有文件上传
		if ($_FILES['goods_logo']['error'] === 0) {
			//根据$data里有无id 来判断是logo图片是添加还是修改
			if (isset($data['id'])) {//修改的话,则删除原logo图
				$goods = M('goods') -> find($data['id']);
				unlink(ROOT_PATH . $goods['goods_big_logo']);
				unlink(ROOT_PATH . $goods['goods_small_logo']);
			}
			//上传类配置
			$config = array(
			    'maxSize'       =>  5242880, //5M限制            上传的文件大小限制 (0-不做限制) 字节单位   
			    'exts'          =>  array('jpg', 'png', 'gif', 'jpeg'), //允许上传的文件后缀
			    'rootPath'      =>  ROOT_PATH . UPLOAD_PATH, //保存根路径
			    'savePath'      =>  'logo/', //保存路径
			);
			//实例化上传类
			$upload = new \Think\Upload($config);
			//上传
			$info = $upload -> uploadOne($_FILES['goods_logo']);
			//echo $upload -> getError();die;
			//判断上传与否
			if ($info) {#上传成功
				//记录原图的   相对路径
				$goods_big_logo = UPLOAD_PATH . $info['savepath'] . $info['savename'];
				$goods_small_logo = UPLOAD_PATH . $info['savepath'] . 's_' . $info['savename'];
				//对原图进行缩略
				$image = new \Think\Image();
				$image -> open(ROOT_PATH . $goods_big_logo);
				$image -> thumb('160', '160') -> save(ROOT_PATH . $goods_small_logo);

				//利用引用传值,添加data中的字段
				$data['goods_big_logo'] = $goods_big_logo; 
				$data['goods_small_logo'] = $goods_small_logo;
			}

			
			
		}
	}

	//多图上传
	public function uploadPic($goods_id) {
		//dump($_FILES['goods_pic']);die;
		//判断有无图片上传
		#上传成功标志
		$flag = false;
		foreach($_FILES['goods_pic']['error'] as $v) {
			#只要有一次成功,就可以正式上传
			if ($v === 0) {
				$flag = true;
				break;
			}
		}
		//判断上传标志
		if ($flag === true) {
			//上传类配置
			$config = array(
			    'maxSize'       =>  5242880, //5M限制            上传的文件大小限制 (0-不做限制) 字节单位   
			    'exts'          =>  array('jpg', 'png', 'gif', 'jpeg'), //允许上传的文件后缀
			    'rootPath'      =>  ROOT_PATH . UPLOAD_PATH, //保存根路径(绝对路径)
			    'savePath'      =>  'pic/', //保存路径(相对)
			);
			//实例化上传类
			$upload = new \Think\Upload($config);
			//多文件上传
			$info = $upload -> upload(array($_FILES['goods_pic']));
			//对多图片进行缩略, 并插入到goods_pic表
			#实例化goods_pic模型
			$model = M('goods_pics');
			#实例化image类
			$image = new \Think\Image();
			foreach($info as $v) {
				#原图绝对路径
				$goods_pics_path = $config['rootPath'] . $v['savepath'] . $v['savename'];
				#打开
				$image -> open($goods_pics_path); 
				#生成800*800 缩略图
				$goods_pics_b_path = UPLOAD_PATH . $v['savepath'] . 'b_' . $v['savename'];
				$image -> thumb(800, 800) -> save(ROOT_PATH . $goods_pics_b_path);
				#生成350*350 缩略图
				$goods_pics_m_path = UPLOAD_PATH . $v['savepath'] . 'm_' . $v['savename'];
				$image -> thumb(350, 350) -> save(ROOT_PATH . $goods_pics_m_path);
				#生成54*54 缩略图
				$goods_pics_s_path = UPLOAD_PATH . $v['savepath'] . 's_' . $v['savename'];
				$image -> thumb(54, 54) -> save(ROOT_PATH . $goods_pics_s_path);
				#插入到goods_pic表
				$model -> add(array(
						'goods_id' => $goods_id,
						'goods_pics_b' => $goods_pics_b_path,
						'goods_pics_m' => $goods_pics_m_path,
						'goods_pics_s' => $goods_pics_s_path,
					));
				#删除掉原图
				unlink($goods_pics_path);
				
			}
		}
	}

	//测试数据库连接是否成功
	public function test() {
		$m = M('test');
		dump($m);
	}
}
