<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Mobile\Controller;

/**
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class IndexController extends HomeController {

	//系统首页
    public function index(){		
		$lists = M('goods')->where(array('isDelete'=>0, 'enable'=>1))->order('sort,id')->select();
		// if(!$lists)
			// $this->error('商品不存在');
		$this->assign('lists',$lists);//列表
		$this->display();       
    }
	public function toSearch(){
		$this->display();
	}
	public function search(){

		$content = I('content');

		$lists = M('goods')->where(array('title'=>array('like','%'.$content.'%')))->order('sort,id')->select();
		
		//dump($lists);
		$this->assign('lists',$lists);//列表
		$this->display('Index/search');
        
    }

    public function test(){
		//$data=M()->query("SELECT * FROM `gy_members` WHERE  uid=%s",$_GET['id']);
		//dump(M()->getLastSql());
		//dump($data);
        // $table = new \OT\DataDictionary;
        // echo "<pre>".PHP_EOL;
        // $out = $table->generateAll();
        // echo "</pre>";
        // print_r($out);
    }

}
