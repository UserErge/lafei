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
class TipController extends HomeController{
	//没有任何方法，直接执行HomeController的_empty方法
	
	
	/**
	 *  盈亏提示
	 */
	public final function getYKTip($type, $actionNo){
		
		if($type && $actionNo){
			$this->type=$type;
			$ykMoney=0;
			//获取彩种
			$this->types = $this->getTypes();
			$czName=$this->types[$this->type]['title'];
			
			$where = array('type'=>$this->type, 
							'uid'=>$this->user['uid'], 
							'actionNo'=>$actionNo, 
							'isDelete'=>0, 
							'flag'=>0, 
							'lotteryNo'=>array('neq','')
							);
			if(M('bets')->where($where)->select()){
				
				$Model = new \Think\Model();
				$return = $Model->query("select IFNULL(sum(bonus-(mode*beiShu*actionNum*(fpEnable+1)*(1-fanDian/100))),'0') tMoney from {$this->prename}bets ".$whereStr);
				
				$return = M('bets')->where($where)->field("IFNULL(sum(bonus-(mode*beiShu*actionNum*(fpEnable+1)*(1-fanDian/100))),'0') tMoney")->find();
				if($return)
					$ykMoney = $return['tMoney'];

				if($ykMoney>0){
					$messager=$czName." 第".$actionNo."期：<br />盈亏 <font style='color:#F00;font-weight:bold;font-size:14px;'>".round($ykMoney,2)."</font> 元";
				
				}else{
					$messager=$czName." 第".$actionNo."期：<br />盈亏 <font style='color:#060;font-weight:bold;font-size:14px;'>".round($ykMoney,2)."</font> 元";
				
				}
				
				M('bets')->where($where)->save(array('flag'=>1));
				
				$data['flag'] = true;
				$data['message'] = $messager;
				$this->ajaxReturn($data,'JSON');			
			}
		}
	}
	
	/**
	 *  提款成功提示
	 */
	public final function getTKTip(){
		
				
		//$sql="select id from {$this->prename}member_cash where (state=0 or state=4) and isDelete=0 and flag=0 and uid=".$this->user['uid']." order by id desc";
		if($gdata=M('member_cash')->where(array('state'=>array('in',array(0,4)), 'isDelete'=>0, 'flag'=>0, 'uid'=>$this->user['uid']))->field('id,amount,state,info')->order('id desc')->find()){
			//dump(M('member_cash')->getLastSql());						
			$amount=$gdata['amount'];
			$state=$gdata['state'];
			$info=$gdata['info'];			
			
			M('member_cash')->save(array('flag'=>1, 'id'=>$gdata['id']));
			
			if(intval($state)==4){
				$data['flag'] = true;
				$data['message'] = '提款失败！<br/>原因：'.$info;
				$this->ajaxReturn($data,'JSON');	

			}else{
				$data['flag'] = true;
				$data['message'] = '提款成功！<br/>金额：'.$amount.'元';
				$this->ajaxReturn($data,'JSON');	
			}
		}
		
	}
	
	
	/**
	 *  充值成功提示  //state=1 前台充值  state=9 后台充值
	 */
	public final function getCZTip(){
		//$sql="select id from {$this->prename}member_recharge where (state=1 or state=9) and isDelete=0 and flag=0 and uid=".$this->user['uid']." order by id desc";
		if($gdata=M('member_recharge')->where(array('state'=>array('in',array(1,9)), 'isDelete'=>0, 'flag'=>0, 'uid'=>$this->user['uid']))->field('id,rechargeAmount')->order('id desc')->find()){
			//dump(M('member_recharge')->getLastSql());
			$CZAmount=$gdata['rechargeAmount'];
			M('member_recharge')->where(array('id'=>$gdata['id']))->save(array('flag'=>1));

			if($CZAmount>0){
				$data['flag'] = true;
				$data['message'] = '充值成功！<br>系统充值：'.$CZAmount.'元';
				$this->ajaxReturn($data,'JSON');	
				
			}else{
				$data['flag'] = true;
				$data['message'] = '扣款成功！<br>系统扣款：'.abs($CZAmount).'元';
				$this->ajaxReturn($data,'JSON');	
		   }/////
		}
		
	}
}
