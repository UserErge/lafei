<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;

/**
 * 空模块，主要用于显示404页面，请不要删除
 */
class WufencaiController extends HomeController{
	//没有任何方法，直接执行HomeController的_empty方法
	//请不要删除该控制器
	
	
	/**
	 * 获取信息页面
	 */
	public function info5(){
		$lastNo=$this->getGameLastNo(14);
		$flag=1;      //开奖按钮
		$kjdata='';  //开奖号码
		$kjtime=date('Y-m-d H:m:s');
		
		$data = M('params')->where(array('name'=>'wufencai'))->find();
		$wufencai=$data['value'];

		$this->assign('lastNo',$lastNo);
		$this->assign('wufencai',$wufencai);
		$this->assign('flag',$flag);

		$data = array();
		$data['actionNo']=$lastNo['actionNo'];
		$data['wufencai']=$wufencai;
		$data['actionTime']=$lastNo['actionTime'];
		$data['flag']=$flag;
		$this->ajaxReturn($data,'JSON');
		//$this->display();
	}

	public function info2(){
		$lastNo=$this->getGameLastNo(34);
		$flag=1;      //开奖按钮
		$kjdata='';  //开奖号码
		$kjtime=date('Y-m-d H:m:s');
		
		$data = M('params')->where(array('name'=>'wufencai'))->find();
		$wufencai=$data['value'];

		$this->assign('lastNo',$lastNo);
		$this->assign('wufencai',$wufencai);
		$this->assign('flag',$flag);

		$data = array();
		$data['actionNo']=$lastNo['actionNo'];
		$data['wufencai']=$wufencai;
		$data['actionTime']=$lastNo['actionTime'];
		$data['flag']=$flag;
		$this->ajaxReturn($data,'JSON');
		//$this->display();
	}
		public function info1(){
		$lastNo=$this->getGameLastNo(5);
		$flag=1;      //开奖按钮
		$kjdata='';  //开奖号码
		$kjtime=date('Y-m-d H:m:s');
		
		$data = M('params')->where(array('name'=>'wufencai'))->find();
		$wufencai=$data['value'];

		$this->assign('lastNo',$lastNo);
		$this->assign('wufencai',$wufencai);
		$this->assign('flag',$flag);

		$data = array();
		$data['actionNo']=$lastNo['actionNo'];
		$data['wufencai']=$wufencai;
		$data['actionTime']=$lastNo['actionTime'];
		$data['flag']=$flag;
		$this->ajaxReturn($data,'JSON');
		//$this->display();
	}
}
