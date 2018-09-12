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
 * 空模块，主要用于显示404页面，请不要删除
 */
class NoticeController extends HomeController{
	//没有任何方法，直接执行HomeController的_empty方法
	//请不要删除该控制器
	/**
	 * 列表页
	 */
	public final function notice(){
		
		$list = M('content')->where(array('enable'=>1))->order('id desc')->select();
		foreach($list as $l)
			$list2[$l['id']]=$l;
		if(I('id'))
			$info=$list2[I('id')];
		else
			$info=$list[0];
		$this->assign('info',$info);
		$this->assign('data',$list);
		$this->display('User/notice');		
	}
	
	/**
	 * 详情页
	 */
	public final function info(){
		$content = M('content')->where(array('enable'=>1, 'id'=>I('id')))->find();
		$this->assign('info',$content);
		$this->display('Notice/info');
	}
}
