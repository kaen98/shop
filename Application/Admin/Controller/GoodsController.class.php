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
			$data = I('post.');
			$goodsModel = M('goods');
			$data['add_time'] = time();
			//logo上传
			$this -> upload($data);
			

			if ($goodsModel -> create()) {
				if ($id = $goodsModel -> add($data)) {
					//相册多图上传
					$this -> uploadPic($id);
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


	//图片上传
	public function upload(&$data) {
		//判断图片有无上传(error为0 ,临时上传成功)
		if ($_FILES['goods_logo']['error'] === 0) {
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
		//判断有无临时上传成功(多次中的一次成功就算成功)
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
				
			}
		}
	}

	//测试数据库连接是否成功
	public function test() {
		$m = M('test');
		dump($m);
	}
}
