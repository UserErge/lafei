<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use User\Api\UserApi as UserApi;

/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class BusinessController extends AdminController {

    static protected $allow = array( 'verify');

    /**
     * 后台首页
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index(){

		$this->display();

    }

	//提现记录
	public final function cash(){
		
		$para = I('post.');
		
		if(!$para)
			$para = I('get.');
		
		// 用户限制
		if($para['username']){
			$userWhere=" and m.username like '%{$para['username']}%'";
		}

		// 时间限制
		if($para['fromTime'] && $para['toTime']){
			$fromTime=strtotime($para['fromTime']);
			$toTime=strtotime($para['toTime'])+24*3600;
			$timeWhere=" and c.actionTime between $fromTime and $toTime";
		}elseif($para['fromTime']){
			$fromTime=strtotime($para['fromTime']);
			$timeWhere=" and c.actionTime>=$fromTime";
		}elseif($para['toTime']){
			$toTime=strtotime($para['toTime'])+24*3600;
			$timeWhere=" and c.actionTime<$toTime";
		}else{
			$timeWhere=' and c.actionTime>'.strtotime(' 00:00:00');
		}
		
		$Model = new \Think\Model();
		$list = $Model->table('__MEMBERS__ m,__MEMBER_CASH__ c,__BANK_LIST__ l')->where('m.uid=c.uid and l.id=c.bankId and c.isDelete=0'.$userwhere.$timeWhere)->order('c.id desc')->field('c.*,l.name as name,l.home as home,m.username as uName')->order('c.id desc')->select();
		//dump($Model->getLastSql());
		
		$this->recordList($list);
		//dump($list);
		$this->meta_title = '提现记录';
		$this->display();

    }
	
	//处理提现
	public final function to_cash(){
		
		if(IS_POST){
			
			$MCash = M('member_cash');
            $data = $MCash->create();
			
			if($data){

				
				$cash = $MCash->where(array('id'=>I('id')))->find();
				if($cash['state']!=1)
					$this->error('提现已经被其他管理员处理过');
				
				
				// 开始事物处理
				$Model = new \Think\Model();
				$Model->startTrans();
						
				$log=array(
					'uid'=>$cash['uid'],
					'fcoin'=>-$cash['amount']
				);
				
				if($data['state']==4){
					$log['info']="提现[{$data['id']}]处理失败";
					$log['coin']=$cash['amount'];
					$log['liqType']=8;
					$log['extfield0']=$data['id'];
				}else{
					$log['info']="提现[{$data['id']}]成功扣除冻结金额";
					$log['liqType']=107;
					$log['extfield0']=$data['id'];
				}
				
				if($this->addCoin($log) && $MCash->save($data))
				{
					$Model->commit();//成功则提交
					$this->addLog(1 , $cash['uid'] , $log['info']);
					$this->success('处理提现成功', U('business/cash'));
				}
				else
				{
					$Model->rollback();//不成功，则回滚
					$this->error('处理提现失败');
				}
				
            } else {
                $this->error($Config->getError());
            }
		}	
		else{
			$this->display();
		}
    }
	
	//充值记录
	public final function recharge(){
		
		$para = I('post.');
		
		if(!$para)
			$para = I('get.');
		
		//dump($para);
		// 用户限制
		if($para['username']){
			$userWhere=" and r.username like '%{$para['username']}%'";
		}

		// 充值编号限制
		if($para['rechargeId']){
			$rechargeIdWhere=" and r.rechargeId={$para['rechargeId']}";
		}
		
		//状态类型限制
		if($para['type']!=''){
			$typeWhere=" and r.state={$para['type']}";
		}
		// 时间限制
		if($para['fromTime'] && $para['toTime']){
			$fromTime=strtotime($para['fromTime']);
			$toTime=strtotime($para['toTime'])+24*3600;
			$timeWhere=" and r.actionTime between $fromTime and $toTime";
		}elseif($para['fromTime']){
			$fromTime=strtotime($para['fromTime']);
			$timeWhere=" and r.actionTime>=$fromTime";
		}elseif($para['toTime']){
			$toTime=strtotime($para['toTime'])+24*3600;
			$timeWhere=" and r.actionTime<$toTime";
		}else{
			$timeWhere=' and r.actionTime>'.strtotime('00:00');
		}
		
		$Model = new \Think\Model();
		$list = $Model->table('__MEMBER_RECHARGE__ r,__BANK_LIST__ l, __MEMBERS__ s')->where('l.id=r.mBankId and r.isDelete=0 and s.uid=r.uid '.$userWhere.$rechargeIdWhere.$timeWhere.$typeWhere)->order('r.id desc')->field('r.*,l.name as name,l.home as home, s.parents as parents')->select();
		//dump($Model->getLastSql());
		
		$this->recordList($list);
		
		$members=M('Members')->field('uid, username')->select();
		
		foreach($members as $m){
			$members_list[$m['uid']]=$m['username'];
		}
		$this->assign('members_list', $members_list);
		$this->meta_title = '充值记录';
		$this->display();

    }
	
	//新增充值
	public final function to_recharge(){
		if(IS_POST){
			$this->user = session('user_auth');
			$uid=0;
			if(I('user')==1){
				$uid=intval(I('uid'));
				if($uid<=0) $this->error('用户ID不正确');
			}
			else
				$uid = I('uid');

			$amount=floatval(I('amount'));
			//if($amount<=0) $this->error('充值金额不能为负值');
			
			$data=array(
				'amount'=>$amount,
				'rechargeAmount'=>$amount,
				'actionUid'=>$this->user['uid'],
				'actionIP'=>$this->ip(true),
				'actionTime'=>time(),
				'rechargeTime'=>time()
			);
			
			// 查找用户信息
			if(I('user')==1){
				$user=M('members')->where(array('uid'=>$uid))->find();
			}else{
				$user=M('members')->where(array('username'=>$uid))->find();
			}
			if(!$user) $this->error('用户不存在');
			
			// 开始事物处理
			$Model = new \Think\Model();
			$Model->startTrans();
			
			$data['uid']=$user['uid'];
			$data['coin']=$user['coin'];
			$data['fcoin']=$user['fcoin'];
			$data['username']=$user['username'];
			$data['info']= I('info');
			$data['state']=9;
			$data['mBankId']=1;
			
			do{
				$data['rechargeId']=mt_rand(100000,999999);
			}while($recharge = M('member_recharge')->where(array('rechargeId'=>$data['rechargeId']))->find());
			
			if($dataId = M('member_recharge')->add($data))
			{
				$return = $this->addCoin(array(
					'uid'=>$user['uid'],
					'liqType'=>1,
					'coin'=>$amount,
					'extfield0'=>$dataId,
					'extfield1'=>$data['rechargeId'],
					'info'=>'充值'
				));
				if($return){
					//每天首次充值赠送
					
					$Model->commit();//成功则提交
					$this->addLog(3 , $user['uid'] , $amount);
					$this->success('新增充值成功', U('business/recharge'));
				}
				
			}

			$Model->rollback();//不成功，则回滚
			$this->error('新增充值失败');
		
		}
		else{
			$this->display('recharge_modal');
		}		
	}
	public final function del_recharge(){
		if(M('member_recharge')->where(array('id'=>I('id','','intval')))->save(array('isDelete'=>1)))
			$this->success('删除充值成功', U('business/recharge'));
		else
			$this->error('删除充值失败');
	}
	//到帐处理
	public final function toOn_recharge(){
		if(IS_POST){
			$this->user = session('user_auth');
			
			$data = M('member_recharge')->where(array('id'=>I('id')))->find();
			if(!$data)
				$this->error('此充值id不存在');
			
			if($data['state']) $this->error('充值已经到帐，请不要重复确认');
			if($data['isDelete']) $this->error('充值已经被删除');
			
			$user = M('members')->where(array('uid'=>$data['uid']))->field('coin,fcoin')->find();
			if(!$user)
				$this->error('此充值用户不存在');
			
			// 开始事物处理
			$Model = new \Think\Model();
			$Model->startTrans();
			
			$para = I('post.');
			$MRecharge = M('member_recharge');

			
			$para=array_merge(array('id'=>$para['id'],'rechargeAmount'=>$para['rechargeAmount'],'state'=>1, 'info'=>'手动确认', 'actionUid'=>$this->user['uid'], 
									'actionTime'=>time(),'rechargeTime'=>time(), 'actionIP'=>$this->ip(true), 'coin'=>$user['coin'],'fcoin'=>$user['fcoin']));
			
			if($MRecharge->save($para))
			{
				$return = $this->addCoin(array(
					'uid'=>$data['uid'],
					'coin'=>$para['rechargeAmount'],
					'liqType'=>1,
					'extfield0'=>$data['id'],
					'extfield1'=>$data['rechargeId'],
					'info'=>'充值'
				));
				if($return){
					//每天首次充值赠送

					$Model->commit();//成功则提交
					$this->addLog(2, $data['uid'] , $para['rechargeAmount']);
					$this->success('充值到帐成功', U('business/recharge'));
				}				
			}
			
			//dump($MRecharge->getLastSql());
			$Model->rollback();//不成功，则回滚
			$this->error('充值到帐失败');
			
		}else{
			$this->display('rechargeOn_modal');
		}
	}
	
	//投注记录
	public final function betLog(){
		
		$map=array();
		// 帐号限制
		if(I('username')){
			$map['username']=I('username');
		}
		
		//期号
		if(I('actionNo')){
			$map['actionNo']=I('actionNo');
		}


		// 彩种限制
		if(I('type')){
			$map['type']=I('type');
		}

		// 单号限制
		if(I('wjorderId')){
			$map['wjorderId']=I('wjorderId');
		}

		// 时间限制
		if(I('fromTime') && I('toTime')){
			$fromTime=strtotime(I('fromTime'));
			$toTime=strtotime(I('toTime'))+24*3600;
			$map['actionTime'] = array('between',array($fromTime,$toTime));
		}elseif(I('fromTime')){
			$fromTime=strtotime(I('fromTime'));
			$map['actionTime'] = array('egt',$fromTime);
		}elseif(I('toTime')){
			$toTime=strtotime(I('toTime'))+24*3600;
			$map['actionTime'] = array('elt',$toTime);
		}else{
			$map['actionTime'] = array('gt',strtotime('00:00'));
		}

		
		$list = M('bets')->where($map)->order('id desc')->select();
		$this->recordList($list);
		
		$this->getTypes();
		$this->assign('types',$this->types);
		
		$this->getPlayeds();
		$this->assign('playeds',$this->playeds);
		
		$this->meta_title = '投注记录';
		$this->display();
	}
	
	//投注详单
	public final function betInfo(){
		$this->getTypes();
		$this->getPlayeds();
		$bet=M('bets')->where(array('id'=>I('id')))->find();
		
		//if($bet['uid']!=$this->user['uid']) $this->error('这单子不是您的，您不能查看。');
		
		$this->assign('types',$this->types);
		$this->assign('playeds',$this->playeds);
		$this->assign('bet',$bet);
		$this->assign('user',$this->user);
		$this->display('Business/bet-info');
	}
	
	//充值详单
	public final function rechargeInfo(){
		$rechargeInfo = M('member_recharge')->where(array('id'=>I('id')))->find();		
		$bankInfo = M('member_bank')->where(array('uid'=>$rechargeInfo['uid']))->find();
		$list = M('bank_list')->order('id')->select();
		
		$bankList = array();
		if($list) foreach($list as $var){
			$bankList[$var['id']]=$var;
		}
		
		$this->assign('rechargeInfo',$rechargeInfo);
		$this->assign('bankInfo',$bankInfo);
		$this->assign('bankList',$bankList);
		
		$this->display('recharge-info');
	}
	
	//提现详单
	public final function cashInfo(){
		$cashInfo = M('member_cash')->where(array('id'=>I('id')))->find();		
		$bankInfo = M('member_bank')->where(array('uid'=>$rechargeInfo['uid']))->find();
		$list = M('bank_list')->order('id')->select();
		
		$bankList = array();
		if($list) foreach($list as $var){
			$bankList[$var['id']]=$var;
		}
		
		$this->assign('cashInfo',$cashInfo);
		$this->assign('bankInfo',$bankInfo);
		$this->assign('bankList',$bankList);
		
		$this->display('cash-info');
	}
	
	public final function del_cash(){
		if(M('member_cash')->where(array('id'=>I('id','','intval')))->save(array('isDelete'=>1)))
			$this->success('删除提现成功', U('business/cash'));
		else
			$this->error('删除提现失败');
	}
	
	//改单
	public final function updateBet(){
		if(IS_POST){
			$bet = M('bets')->where(array('id'=>I('id')))->find();
			if(!$bet) $this->error('单号不存在');
			
			$data['id'] = I('id');
			$data['actionData'] = I('actionData');
			
			if(M('bets')->save($data)){
				//将投注记录写入文件
				$fp = fopen(__ROOT__."Record/record.txt", "a+");
				$tz_content=$bet['wjorderId']." 会员：".$bet['username']." 投注内容：".$bet['actionData']." 玩法：".$bet['playedId']." 元角分：".$bet['mode']." 倍数：".$bet['beiShu']." 注数：".$bet['actionNum']." 时间：".date('m-d H:i:s',time())." ".$_SERVER['REMOTE_ADDR']."\r\n\r\n";
				$flag=fwrite($fp,$tz_content);
				if(!$flag)
				{
					throw new Exception('创建投注记录文件失败');
				} 
				fclose($fp); 
				
				$this->addLog(18,$data['id'],$data['actionData']);
				$this->success('修改投注成功',U('business/betlog'));
			}
			else{
				$this->error('修改投注记录失败或未曾改动');
			}
		}else{
			$this->getTypes();
			$this->getPlayeds();
			$this->assign('types',$this->types);
			$this->assign('playeds',$this->playeds);
			
			$bet = M('bets')->where(array('id'=>I('id')))->find();
			if(!$bet) $this->error('单号不存在');
			
			$this->assign('bet',$bet);
			$this->display('update-bet-info');
		}
	}
	
	//撤单
	public final function deleteBet(){


		if(!$data=M('bets')->where(array('id'=>I('id')))->find()) $this->error('找不到定单。');
		if($data['isDelete']) $this->error('这单子已经撤单过了。');
		
		if($data['qz_uid']) $this->error('单子已经被人抢庄，不能撤单');
		
		// 开始事物处理
		$Model = new \Think\Model();
		$Model->startTrans();
		
		$amount=$data['beiShu'] * $data['mode'] * $data['actionNum'] * (intval($data['fpEnable']?'2':'1'));
		$amount=abs($amount);
		// 添加用户资金变更日志
		$return1 = $this->addCoin(array(
			'uid'=>$data['uid'],
			'type'=>$data['type'],
			'playedId'=>$data['playedId'],
			'liqType'=>7,
			'info'=>"撤单",
			'extfield0'=>I('id'),
			'coin'=>$amount,
		));			
		
		// 更改定单为已经删除状态
		$return2 = M('bets')->where(array('id'=>I('id')))->save(array('isDelete'=>1));
		if($return1 && $return2)
		{		
			//将投注记录写入文件
			$fp = fopen(__ROOT__."Record/record.txt", "a+");
			$tz_content=$data['wjorderId']." 撤单 ".date('m-d H:i:s',time()).$_SERVER['REMOTE_ADDR']."\r\n\r\n";
			$flag=fwrite($fp,$tz_content);
			if(!$flag)
			{
				$Model->rollback();//不成功，则回滚
				$this->error('创建投注记录文件失败');
			} 
			fclose($fp); 
			
			$Model->commit();//成功则提交
			$this->addLog(181, $data['id'] , '');
			$this->success('撤单成功', U('business/betlog'));
		}
		else
		{
			$Model->rollback();//不成功，则回滚
			$this->error('撤单失败');
		}
			
	}
	
	//账变记录
	public final function coinLog(){
		
		$para=I('post.');
		
		if(!$para)
			$para = I('get.');
		// 用户限制
		if($para['username']){
			$userWhere=" and u.username like '%{$para['username']}%'";
		}

		// 帐变类型限制
		if($para['liqType']){
			$liqTypeWhere=" and l.liqType={$para['liqType']}";
			if($para['liqType']==2) $liqTypeWhere=' and liqType=2 or liqType=3';
		}

		// 彩种限制
		if($para['type']){
			$typeWhere=" and b.type={$para['type']}";
		}

		// 时间限制
		if($para['fromTime'] && $para['toTime']){
			$fromTime=strtotime($para['fromTime']);
			$toTime=strtotime($para['toTime'])+24*3600;
			$timeWhere=" and l.actionTime between $fromTime and $toTime";
		}elseif($para['fromTime']){
			$fromTime=strtotime($para['fromTime']);
			$timeWhere=" and l.actionTime>=$fromTime";
		}elseif($para['toTime']){
			$toTime=strtotime($para['toTime'])+24*3600;
			$timeWhere=" and l.actionTime<$toTime";
		}else{
			$timeWhere=' and l.actionTime>'.strtotime('00:00');
		}
		
		$Model = new \Think\Model();
		$list = $Model->table('__COIN_LOG__ l,__MEMBERS__ u')->where('l.uid=u.uid '. $timeWhere. $liqTypeWhere. $typeWhere. $userWhere)->order('l.id desc')->field('l.*,u.username')->select();
		//dump($Model->getLastSql());
		
		$this->recordList($list);
		
		//dump($list);
		
		$this->getTypes();
		$this->assign('types',$this->types);
		
		$this->getPlayeds();
		$this->assign('playeds',$this->playeds);
		
		$this->meta_title = '账变记录';
		$this->display();
	}
	
	public final function getTip_cash(){
		if($data=M('member_cash')->where(array('state'=>1, 'isDelete'=>0, 'actionTime'=>array('gt',strtotime(' 00:00:00'))))->field('id,flag')->select()){

			$isDialog = false;
			foreach($data as $d){
				if($d['flag']==0)
					$isDialog=true;
			}
			
			M('member_cash')->where(array('flag'=>0))->save(array('flag'=>1));
			
			$return = array(
				'flag'=>true,
				'isDialog'=>$isDialog,
				'message'=>'有新的提现请求需要处理',
				'buttons'=>'前往处理:goToDealWithCash|忽略:defaultCloseModal'
			);
			
			$this->ajaxReturn($return,'JSON');
		}
		
	}
	
	public final function getTip_recharge(){
		
		if($data=M('member_recharge')->where(array('state'=>0,'isDelete'=>0, 'actionTime'=>array('gt',strtotime(' 00:00:00'))))->field('id,flag')->select()){
			
			// if($cookie=$_COOKIE['recharge-tip']){
				// $cookie=explode(',',$cookie);
				// if(!array_diff($data, $cookie)) {
					// $return = array(
						// 'flag'=>false,					
					// );
					// $this->ajaxReturn($return,'JSON');
				// }
			// }
			
			// $data=implode(',', $data);
			// if($data) setcookie('recharge-tip', $data);
			
			$isDialog = false;
			foreach($data as $d){
				if($d['flag']==0)
					$isDialog=true;
			}
			
			M('member_recharge')->where(array('flag'=>0))->save(array('flag'=>1));
			
			$return = array(
				'flag'=>true,
				'isDialog'=>$isDialog,
				'message'=>'有新的充值请求需要处理',
				'buttons'=>'前往处理:goToDealWithRec|忽略:defaultCloseModal'
			);
			
			
			$this->ajaxReturn($return,'JSON');
		}
		
	}

}
