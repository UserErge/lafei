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
 * 游戏记录模块
 */
class RecordController extends HomeController{
	
	public function search(){

		$this->getTypes();
		$this->getPlayeds();
		
		$this->assign('types',$this->types);
		$this->searchFun();
		$this->display();
		
	}
	public final function searchFun(){
		$para=I('get.');
	
		$this->getTypes();
		$this->getPlayeds();
		$this->assign('types',$this->types);
		$this->assign('playeds',$this->playeds);
		
		
		$where = array();
		// 用户名限制
		$where['uid'] = $this->user['uid'];
		
		$userList = M('members')->where($where)->select();

		$where = array();
		// 彩种限制
		if($para['type']){
			$where['type'] = $para['type'];
		}
				
		// 时间限制
		if($para['fromTime'] && $para['toTime']){
			$map['actionTime'] = array('between',array(strtotime($para['fromTime']),strtotime($para['toTime'])));
		}elseif($para['fromTime']){
			$where['actionTime'] = array('egt',strtotime($para['fromTime']));
		}elseif($para['toTime']){
			$where['actionTime'] = array('elt',strtotime($para['toTime']));
		}else{
			if($GLOBALS['fromTime'] && $GLOBALS['toTime']){
				$map['actionTime'] = array('between',array($GLOBALS['fromTime'],$GLOBALS['toTime']));
			}
		}
		
		// 投注状态限制
		if($para['state']){
		switch($para['state']){
			case 1:
				// 已派奖
				$where['zjCount'] = array('gt',0);
			break;
			case 2:
				// 未中奖
				$where['zjCount']=0;
				$where['lotteryNo']=array('neq','');
				$where['isDelete']=0;
				
			break;
			case 3:
				// 未开奖
				$where['lotteryNo']=array('eq','');
			break;
			case 4:
				// 追号
				$where['zhuiHao']=1;
			break;
			case 5:
				// 撤单
				$where['isDelete']=1;
			break;
			}
		}

		
		 //单号
	   if($para['betId'] && $para['betId']!='输入单号') $where['wjorderId']=$para['betId'];

		$betList = M('bets')->where($where)->order('id desc,actionTime desc')->select();
		
		$i=0;
		foreach($betList as $bet)
		{
			$isIn = false;
			foreach($userList as $user)
			{
				if($bet['uid']==$user['uid'])
				{
					$isIn=true;
					break;
				}
			}
			if($isIn)
			{
				$data[$i] = $bet;
				$i++;
			}
		}

		//dump($data);
		$this->recordList($data);		
	}
	
	public final function searchGameRecord(){
		if(IS_GET)
		{
			$this->searchFun();
			
			$modeName=array('2.00'=>'元', '0.20'=>'角', '0.02'=>'分');
			$this->assign('modeName',$modeName);
			
			$this->display('Record/search-list');
		}
	}
	
	public final function betInfo(){
		$this->getTypes();
		$this->getPlayeds();
		$bet=M('bets')->where(array('id'=>I('id')))->find();
		$user=M('members')->where(array('uid'=>$bet['uid']))->field('parents')->find();
		
		if($bet['uid']!=$this->user['uid'] && strstr($user['parents'],','.$this->user['uid'].',')===false) $this->error('这单子不是您或您的下级的，您不能查看。');
		
		$this->assign('types',$this->types);
		$this->assign('playeds',$this->playeds);
		$this->assign('bet',$bet);
		$this->assign('user',$this->user);
		$this->display('Record/bet-info');
	}
	public final function betInfo2(){
		$this->getTypes();
		$this->getPlayeds();
		$bet=M('bets')->where(array('id'=>I('id')))->find();
		$user=M('members')->where(array('uid'=>$bet['uid']))->field('parents')->find();
		
		if($bet['uid']!=$this->user['uid'] && strstr($user['parents'],','.$this->user['uid'].',')===false) $this->error('这单子不是您或您的下级的，您不能查看。');
		
		$this->assign('types',$this->types);
		$this->assign('playeds',$this->playeds);
		$this->assign('bet',$bet);
		$this->assign('user',$this->user);
		$this->display('Record/bet-info2');
	}
}
