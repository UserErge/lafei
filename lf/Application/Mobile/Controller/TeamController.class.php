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
class TeamController extends HomeController{
	
	public final function index() {
		$user = M('members')->find($this->user['uid']);
		$this->assign('user',$user);
		$this->display();
	}
	/*游戏记录*/
	public final function record(){
		$this->getTypes();
		$this->getPlayeds();
		$this->assign('types',$this->types);
		$this->assign('playeds',$this->playeds);

		$this->search();
		
		if(!I('get.'))
			$this->display('Team/record');
		else
			$this->display('Team/record-list');		
	}
	
	public final function search(){
		$para=I('get.');
	
		$this->getTypes();
		$this->getPlayeds();
		$this->assign('types',$this->types);
		$this->assign('playeds',$this->playeds);
		
		
		$where = array();
		// 用户名限制
		if($para['username'] && $para['username']!='用户名'){
			// 按用户名查找时
			// 只要符合用户名且是自己所有下级的都可查询
			// 用户名用模糊方式查询
			$where['username'] = array('like',"%".I('username')."%");
			$where['parents'] = array('like',"%,".$this->user['uid'].",%");
		}
		//用户类型限制
		switch($para['utype']){
			case 1:
				//我自己
				$map['uid'] = $this->user['uid'];
				break;
			case 2:
				//直属下线
				$map['parentId'] = $this->user['uid'];
				break;
			case 3:
				// 所有下级
				$map['parents'] = array('like','%,'.$this->user['uid'].',%');
				break;
			default:
				//所有人
				$map['parents'] = array('like',"%,".$this->user['uid'].",%");
				$map['uid'] = $this->user['uid'];
				$map['_logic'] = 'or';
				break;
		}
		
		$where['_complex'] = $map;
		$userList = M('members')->field('uid,username')->where($where)->select();
		//dump(M('members')->getLastSql());
		$userData=array();
		foreach($userList as $user)
		{
			$userStr = $userStr.$user['uid'].',';
			$userData[$user['uid']] = $user;
		}

		$where = array();
		// 彩种限制
		if($para['type']){
			$where['type'] = $para['type'];
		}
				
		// 时间限制
		if($para['fromTime'] && $para['toTime']){
			$where['actionTime'] = array('between',array(strtotime($para['fromTime']),strtotime($para['toTime'])));
		}elseif($para['fromTime']){
			$where['actionTime'] = array('egt',strtotime($para['fromTime']));
		}elseif($para['toTime']){
			$where['actionTime'] = array('elt',strtotime($para['toTime']));
		}else{
			if($GLOBALS['fromTime'] && $GLOBALS['toTime']){
				$where['actionTime'] = array('between',array($GLOBALS['fromTime'],$GLOBALS['toTime']));
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
		
		$where['uid'] = array('in',$userStr);
		$betList = M('bets')->field('id,wjorderId,uid,username,type,playedId,actionNo,beiShu,mode,lotteryNo,isDelete,zjCount,bonus,actionNum,fpEnable,actionTime')->where($where)->order('id desc,actionTime desc')->select();
		$this->recordList($betList);		
	}
	
	public final function searchRecord(){
		$this->search();
		$this->display('Team/record-list');
	}
	
	
	/*盈亏报表*/
	public final function report(){

		$this->reportSearch();
		if(!I('get.'))
			$this->display('Team/report');
		else
			$this->display('Team/report-list');
	}
	public final function searchReport(){
		$this->reportSearch();
		$this->display('Team/report-list');
	}
	public final function reportSearch(){		
		$para=I('get.');
		
		$where = array();
		$parentWhere = array();
		// 用户限制
		$uid=$this->user['uid'];
		if($para['parentId']=intval($para['parentId'])){
			// 直属下级
			$where['parentId'] = $para['parentId'];
			
			$parentWhere['parents'] = array('like',"%,".$para['parentId'].",%");
			$parentWhere['uid'] = $para['parentId'];
			$parentWhere['_logic'] = 'or';
		}elseif($para['uid']){
			// 上级
			$user = M('members')->where(array('uid'=>$para['uid']))->find();
			$where['uid'] = $user['parentId'];
			
			$parentWhere['parents'] = array('like',"%,".$user['parentId'].",%");
			$parentWhere['uid'] = $user['parentId'];
			$parentWhere['_logic'] = 'or';
		}elseif($para['username'] && $para['username']!='用户名'){
			// 用户名限制
			
			// 按用户名查找时
			// 只要符合用户名且是自己所有下级的都可查询
			// 用户名用模糊方式查询
			$where['username'] = $para['username'];
			$where['parents'] = array('like',"%,".$this->user['uid'].",%");
			
			$user = M('members')->where(array('username'=>$para['username']))->find();
			$parentWhere['parents'] = array('like',"%,".$user['uid'].",%");
			$parentWhere['uid'] = $user['uid'];
			$parentWhere['_logic'] = 'or';
		}
		else{
			$where['parents'] = array('like',"%,".$this->user['uid'].",%");
			$where['uid'] = $this->user['uid'];
			$where['_logic'] = 'or';
			
			$parentWhere['parents'] = array('like',"%,".$this->user['uid'].",%");
			$parentWhere['uid'] = $this->user['uid'];
			$parentWhere['_logic'] = 'or';
		}
			
		$userList = M('members')->field('uid,username,parentId,coin')->where($where)->order('uid')->select();
		
		$userData=array();
		foreach($userList as $user)
		{
			//$userStr = $userStr.$user['uid'].',';
			$userData[$user['uid']] = $user;
		}

		$map=array();
		// 时间限制
		if($para['fromTime'] && $para['toTime']){
			$map['actionTime'] = array('between',array(strtotime($para['fromTime']),strtotime($para['toTime'])));
		}elseif($para['fromTime']){
			$map['actionTime'] = array('egt',strtotime($para['fromTime']));
		}elseif($para['toTime']){
			$map['actionTime'] = array('elt',strtotime($para['toTime']));
		}else{
			if($GLOBALS['fromTime'] && $GLOBALS['toTime']){
				$map['actionTime'] = array('between',array($GLOBALS['fromTime'],$GLOBALS['toTime']));
			}
		}
		//$map['uid'] = array('in',$userStr);
		
		$coinList = M('coin_log')->where($map)->field("actionTime,uid,sum(case when liqType in ('2','3') then coin else 0 end) as fanDianAmount, 
		0-sum(case when liqType in ('101','102','7') then coin else 0 end) as betAmount, 
		sum(case when liqType=6 then coin else 0 end) as zjAmount, 
		0-sum(case when liqType=107 then fcoin else 0 end) as cashAmount, 
		sum(case when liqType=1 then coin else 0 end) as rechargeAmount, 
		sum(case when liqType in ('50','51','52','53') then coin else 0 end) as brokerageAmount")->group('uid')->select();
		
		$allList = M('members')->where($parentWhere)->field('uid,coin')->order('uid')->select();
		
		foreach($coinList as $coin)
		{
			$user2 = $userData[$coin['uid']];
			if($user2)
			{
				$data[$coin['uid']] = array_merge($coin,$user2);
			}
			
			foreach($allList as $user)
			{
				if($coin['uid'] == $user['uid'])
				{
					$all['betAmount']+=$coin['betAmount'];
					$all['zjAmount']+=$coin['zjAmount'];
					$all['fanDianAmount']+=$coin['fanDianAmount'];
					$all['brokerageAmount']+=$coin['brokerageAmount'];
					$all['cashAmount']+=$coin['cashAmount'];
					$all['rechargeAmount']+=$coin['rechargeAmount'];
				}
				$all['coin']+=$user['coin'];
			}
		}
		//将没有消费的用户补上为0，显示出来，提高用户体验
		foreach($userData as $u)
		{
			if(!$data[$u['uid']])
			{
				$data[$u['uid']]=array(	'uid'=>$u['uid'],
										'parentId'=>$u['parentId'],
										'username'=>$u['username'],
										'betAmount'=>0.0000,
										'zjAmount'=>0.0000,
										'fanDianAmount'=>0.0000,
										'brokerageAmount'=>0.0000,
										'cashAmount'=>0.0000,
										'coin'=>$u['coin'],
										'rechargeAmount'=>0.0000,
				);				
			}
		}
		
		$this->recordList($data);

		//团队
		$this->assign('all',$all);
		
		$this->assign('para',$para);
		$this->assign('user',$this->user);
	}
	
	//会员管理
	public final function member(){
		//dump(I('get.'));
		$this->memberSearch();
		if(!I('get.'))
			$this->display('Team/member');
		else
			$this->display('Team/member-list');
	}
	public final function searchMember(){
		$this->memberSearch();
		$this->display('Team/member-list');
	}
	public final function memberSearch(){
		if(I('username') && I('username')!='用户名'){
			// 按用户名查找时
			// 只要符合用户名且是自己所有下级的都可查询
			// 用户名用模糊方式查询
			$where['username'] = array('like',"%".I('username')."%");
			$where['parents'] = array('like',"%,".$this->user['uid'].",%");
			
		}else{
			switch(I('utype')){
				case 1:
					// 我自己
					$where['uid'] = $this->user['uid'];
					break;
				case 2:
					// 直属下级
					$uid = $this->user['uid'];
					if(I('uid')) {
						$uid = I('uid');
					}
					$where['parentId'] = $uid;
					break;
				case 3:
					// 所有下级
					$where['parents'] = array('like',"%".$this->user['uid'].",%");
					break;
				default:
					//所有人
					$where['parents'] = array('like',"%,".$this->user['uid'].",%");
					$where['uid'] = $this->user['uid'];
					$where['_logic'] = 'or';
					break;			
			}
		}
		$userList = M('members')->where($where)->order('uid')->select();
		//dump($where);
		$this->recordList($userList,10);	
		$this->assign('user',$this->user);
	}
	
	public final function userUpdate(){
		
		$user = M('members')->find(I('id'));
		$this->assign('userData',$user);
		
		$parentData = M('members')->find($user['parentId']);
		
		if($userData['parentId']){
			$parentData=$parentData;
		}else{
			$this->getSystemSettings();
			$parentData['fanDian']=$this->settings['fanDianMax'];
			$parentData['fanDianBdw']=$this->settings['fanDianBdwMax'];
		}
		$sonFanDianMax= M('members')->where(array('isDelete'=>0, 'parentId'=>I('uid')))->field('max(fanDian) sonFanDian, max(fanDianBdw) sonFanDianBdw')->find();
		
		$this->assign('parentData',$parentData);
		$this->assign('sonFanDianMax',$sonFanDianMax);
		$this->display('Team/update-menber');
	}
	
	public final function userUpdateed(){
		if(I('fanDian')<0)
			$this->error('返点不能小于0');
		$user = M('members')->where(array('username'=>I('username')))->find();
		if($this->user['uid']!=$user['parentId'])
			$this->error('不是你的直属下级，不可以修改');
		
		if($this->user['fanDian']<=I('fanDian'))
			$this->error('返点不可以大于上级');
		
		$sonFanDianMax= M('members')->where(array('isDelete'=>0, 'parentId'=>$user['uid']))->field('max(fanDian) sonFanDian, max(fanDianBdw) sonFanDianBdw')->find();
		
		if($sonFanDianMax['sonFanDian']){
			if($sonFanDianMax['sonFanDian']>=I('fanDian'))
				$this->error('返点不可以小于直属下级'.$sonFanDianMax['sonFanDian']);
		}
		
		$data['uid'] = $user['uid'];
		$data['fanDian'] = I('fanDian');
		$data['type'] = I('type');
		
		if(M('members')->save($data)){
			$this->success('修改成功',U('Team/member'));
		}else{
			$this->error('修改失败');
		}		
	}
	
	public final function addMember(){
		//print_r($this->getMyUserCount());
		$this->display('Team/add-member');
	}
	
	public final function insertMember(){
		$username=I('username');
		$password=I('password');
		if(!$username.trim() || !$password.trim())
			$this->error('用户名或密码不能为空');
		
		if(!preg_match("/^[0-9a-zA-Z]{4,30}$/",I('username'))){
			$this->error('用户名只能由英文和数字组成，长度4-30个字符');
		}
		
		if(M('members')->where(array('username'=>I('username')))->find())
			$this->error('用户'.I('username').'已经存在');
		
			
		if(I('fanDian')<0)
			$this->error('返点不能小于0');
		
		if($this->user['fanDian']<=I('fanDian'))
			$this->error('返点不可以大于上级');

		$para['parentId']=$this->user['uid'];
		$para['parents']=$this->user['parents'];
		$para['password']=think_ucenter_md5(I('password'), UC_AUTH_KEY);
		$para['username'] = I('username');
		$para['qq'] = I('qq');
		$para['type']=I('type');
		$para['regIP']=$this->ip(true);
		$para['regTime']=$this->time;
		
		if(!$para['nickname']) $para['nickname']=$para['username'];
		if(!$para['name']) $para['name']=$para['username'];
		
		
		// 查检返点设置
		if($para['fanDian']=floatval(I('fanDian'))){
			$this->getSystemSettings();
			if($para['fanDian'] % $this->settings['fanDianDiff']) $this->error(sprintf('返点只能是%.1f%的倍数', $this->settings['fanDianDiff']));
		}else{
			$para['fanDian']=0;
		}		
		
		M()->startTrans();
		if($lastid = M('members')->add($para))
		{
			if(M('members')->save(array('uid'=>$lastid, 'parents'=>$this->user['parents'].','.$lastid)))
			{
				M()->commit();//成功则提交
				$this->success('添加会员成功',U('Team/member'));
			}
		}
		
		M()->rollback();//不成功，则回滚
		$this->error('添加会员失败');
		
	}
	
	/*帐变列表*/
	public final function coin(){
		$this->coinSearch();
		if(!I('get.'))
			$this->display('Team/coin');
		else
			$this->display('Team/coin-list');
	}
	public final function searchCoin(){
		$this->coinSearch();		
		$this->display('Team/coin-list');
	}
	public final function coinSearch(){
		$this->getTypes();
		$this->getPlayeds();
		$this->assign('types',$this->types);
		$this->assign('playeds',$this->playeds);
		
		$para=I('get.');
		$where = array();
		
		// 用户名限制
		if($para['username'] && $para['username']!='用户名'){
			// 按用户名查找时
			// 只要符合用户名且是自己所有下级的都可查询
			// 用户名用模糊方式查询
			$where['username'] = array('like',"%".$para['username']."%");
			$where['parents'] = array('like',"%,".$this->user['uid'].",%");
		}
		//用户类型限制
		switch($para['utype']){
			case 1:
				//我自己
				$map['uid'] = $this->user['uid'];
				break;
			case 2:
				//直属下线
				$map['parentId'] = $this->user['uid'];
				break;
			case 3:
				// 所有下级
				$map['parents'] = array('like','%,'.$this->user['uid'].',%');
				break;
			default:
				//所有人
				$map['parents'] = array('like',"%,".$this->user['uid'].",%");
				$map['uid'] = $this->user['uid'];
				$map['_logic'] = 'or';
				break;
		}
		
		$where['_complex'] = $map;
		$userList = M('members')->field('uid,username')->where($where)->select();
		//dump($userList);
		$userData=array();
		foreach($userList as $user)
		{
			$userStr = $userStr.$user['uid'].',';			
			$userData[$user['uid']] = $user;
		}

		$where = array();
		// 账变类型限制
		if($para['liqType']){
			$where['liqType'] = $para['liqType'];
			if($para['liqType']==2) $where['liqType'] = array('between','2,3');
		}
				
		// 时间限制
		if($para['fromTime'] && $para['toTime']){
			$where['actionTime'] = array('between',array(strtotime($para['fromTime']),strtotime($para['toTime'])));
		}elseif($para['fromTime']){
			$where['actionTime'] = array('egt',strtotime($para['fromTime']));
		}elseif($para['toTime']){
			$where['actionTime'] = array('elt',strtotime($para['toTime']));
		}else{
			if($GLOBALS['fromTime'] && $GLOBALS['toTime']){
				$where['actionTime'] = array('between',array($GLOBALS['fromTime'],$GLOBALS['toTime']));
			}
		}
		
		$userStr = substr($userStr,0,-1);
		$where['uid'] = array('in',$userStr);
		//dump($where);
		$coinList = M('coin_log')->field('uid,actionTime,liqType,extfield0,extfield1,coin,userCoin')->where($where)->order('id desc')->select();
		//dump($coinList);
		unset($where['liqType']);
		$betList = M('bets')->field('id,actionNo,mode,type,playedId,wjorderId')->where($where)->order('id desc')->select();		
		$betData=array();
		foreach($betList as $bet)
		{
			$betData[$bet['id']] = $bet;
		}

		$data = array();
		$i=0;
		foreach($coinList as $coin)
		{
			$b = $betData[$coin['extfield0']];
			$b = $b?$b:array();
			$data[$i] = array_merge($coin,$userData[$coin['uid']],$b);
			$i++;
		}
		//dump($data);
		
		$this->recordList($data);		
	}
	
	//团队统计
	public final function team(){
				
		$teamAll = M('members')->where(array('isDelete'=>0, 'parents'=>array('like','%,'.$this->user['uid'].',%')))->field('sum(coin) coin, count(uid) count')->find();
		$teamAll2 = M('members')->where(array('isDelete'=>0, 'parentId'=>$this->user['uid']))->field('count(uid) count')->find();
		
		$this->assign('teamAll',$teamAll);
		$this->assign('teamAll2',$teamAll2);
		$this->assign('user',$this->user);
		$this->display('Team/team');
	}
	
	//提现记录
	public final function cashRecord(){
		$this->cashSearch();
		if(!I('get.'))
			$this->display('Team/cashRecord');
		else
			$this->display('Team/cash-list');
	}
	public final function searchCashRecord(){
		$this->cashSearch();
		$this->display('Team/cash-list');
	}
	public final function cashSearch(){
		
		$para = I('get.');
		
		// 用户名限制
		if($para['username'] && $para['username']!='用户名'){
			// 按用户名查找时
			// 只要符合用户名且是自己所有下级的都可查询
			// 用户名用模糊方式查询
			$where['username'] = array('like',"%".I('username')."%");
			$where['parents'] = array('like',"%,".$this->user['uid'].",%");
		}
		else{
			//用户类型限制
			switch($para['utype']){
				case 1:
					//我自己
					$map['uid'] = $this->user['uid'];
					break;
				case 2:
					//直属下线
					$map['parentId'] = $this->user['uid'];
					break;
				case 3:
					// 所有下级
					$map['parents'] = array('like','%,'.$this->user['uid'].',%');
					break;
				default:
					//所有人
					$map['parents'] = array('like',"%,".$this->user['uid'].",%");
					$map['uid'] = $this->user['uid'];
					$map['_logic'] = 'or';
					break;
			}
		}
		
		$where['_complex']=$map;
		$userList = M('members')->field('uid,username')->where($where)->select();
		
		$userData=array();
		foreach($userList as $user)
		{
			$userStr = $userStr.$user['uid'].',';
			$userData[$user['uid']] = $user;
		}

		$where = array();
	
		// 时间限制
		if($para['fromTime'] && $para['toTime']){
			$where['actionTime'] = array('between',array(strtotime($para['fromTime']),strtotime($para['toTime'])));
		}elseif($para['fromTime']){
			$where['actionTime'] = array('egt',strtotime($para['fromTime']));
		}elseif($para['toTime']){
			$where['actionTime'] = array('elt',strtotime($para['toTime']));
		}else{
			if($GLOBALS['fromTime'] && $GLOBALS['toTime']){
				$where['actionTime'] = array('between',array($GLOBALS['fromTime'],$GLOBALS['toTime']));
			}
		}
		
		$where['uid'] = array('in',$userStr);
		$cashList = M('member_cash')->field('id,uid,actionTime,amount,account,username,state,bankId')->where($where)->order('id desc')->select();
		
		$i=0;
		foreach($cashList as $cash)
		{
			$data[$i] = array_merge($cash,$userData[$cash['uid']]);
			$i++;
		}
		
		$bankList = M('bank_list')->field('id,name')->where(array('isDelete'=>0))->order('id')->select();
		$bankData=array();
		foreach($bankList as $bank)
		{
			$bankData[$bank['id']] = $bank;
		}
		$this->assign('bankData',$bankData);

		$this->recordList($data);		
	}
	
	
	//充值记录
	public final function rechargeRecord(){
		$this->rechargeSearch();
		if(!I('get.'))
			$this->display('Team/rechargeRecord');
		else
			$this->display('Team/recharge-list');
	}
	public final function searchrechargeRecord(){
		$this->rechargeSearch();
		$this->display('Team/recharge-list');
	}
	public final function rechargeSearch(){
		
		$para = I('get.');
		
		// 用户名限制
		if($para['username'] && $para['username']!='用户名'){
			// 按用户名查找时
			// 只要符合用户名且是自己所有下级的都可查询
			// 用户名用模糊方式查询
			$where['username'] = array('like',"%".I('username')."%");
			$where['parents'] = array('like',"%,".$this->user['uid'].",%");
		}
		else{
			//用户类型限制
			switch($para['utype']){
				case 1:
					//我自己
					$map['uid'] = $this->user['uid'];
					break;
				case 2:
					//直属下线
					$map['parentId'] = $this->user['uid'];
					break;
				case 3:
					// 所有下级
					$map['parents'] = array('like','%,'.$this->user['uid'].',%');
					break;
				default:
					//所有人
					$map['parents'] = array('like',"%,".$this->user['uid'].",%");
					$map['uid'] = $this->user['uid'];
					$map['_logic'] = 'or';
					break;
			}
		}
		
		$where['_complex']=$map;
		$userList = M('members')->field('uid,username')->where($where)->select();
		
		$userData=array();
		foreach($userList as $user)
		{
			$userStr = $userStr.$user['uid'].',';
			$userData[$user['uid']] = $user;
		}

		$where = array();
	
		// 时间限制
		if($para['fromTime'] && $para['toTime']){
			$where['actionTime'] = array('between',array(strtotime($para['fromTime']),strtotime($para['toTime'])));
		}elseif($para['fromTime']){
			$where['actionTime'] = array('egt',strtotime($para['fromTime']));
		}elseif($para['toTime']){
			$where['actionTime'] = array('elt',strtotime($para['toTime']));
		}else{
			if($GLOBALS['fromTime'] && $GLOBALS['toTime']){
				$where['actionTime'] = array('between',array($GLOBALS['fromTime'],$GLOBALS['toTime']));
			}
		}
		
		$where['uid'] = array('in',$userStr);
		$cashList = M('member_recharge')->field('id,uid,username,rechargeId,amount,rechargeAmount,mBankId,state,info')->where($where)->order('id desc')->select();
		
		$i=0;
		foreach($cashList as $cash)
		{
			$data[$i] = array_merge($cash,$userData[$cash['uid']]);
			$i++;
		}
		
		$bankList = M('bank_list')->field('id,name')->where(array('isDelete'=>0))->order('id')->select();
		$bankData=array();
		foreach($bankList as $bank)
		{
			$bankData[$bank['id']] = $bank;
		}
		$this->assign('bankData',$bankData);

		$this->recordList($data);		
	}
	
	//推广链接
	public final function linkList(){
		
		$list = M('links')->where(array('uid'=>$this->user['uid']))->order('fanDian desc')->select();
		$this->assign('data',$list);		
		$this->display('Team/link-list');
	}

	
	public final function addLink(){
		if(IS_POST)
		{
			//$para=$_POST;
			$para['regIP']=$this->ip(true);
			$para['regTime']=$this->time;
			$para['uid']=$this->user['uid'];
			$para['type'] = I('type','','intval');
			// 查检返点设置
			if($para['fanDian']=floatval(I('fanDian'))){
				if($para['fanDian'] % $this->settings['fanDianDiff']) $this->error(sprintf('返点只能是%.1f%的倍数', $this->settings['fanDianDiff']));
				
			}else{
				$para['fanDian']=0;
			}
			
			if(I('fanDian')>=$this->user['fanDian'])
				$this->error('下级返点不能大于自己的返点');
			
			$para['fanDianBdw']=floatval(I('fanDianBdw'));
			
			if(M('links')->where(array('uid'=>$this->user['uid'], 'fanDian'=>$para['fanDian']))->find())
				$this->error('此链接已经存在');
			
			if(M('links')->add($para))
				$this->success('添加链接成功',U('Team/linklist'));
			else
				$this->error('添加链接失败');
			
		}
		else
		{
			$this->display('Team/add-link');
		}
	}
	
	/*编辑注册链接*/
	public final function linkUpdate(){
		if(IS_POST)
		{

			// 查检返点设置
			if($para['fanDian']=floatval(I('fanDian'))){
				if($para['fanDian'] % $this->settings['fanDianDiff']) $this->error(sprintf('返点只能是%.1f%的倍数', $this->settings['fanDianDiff']));
				
			}else{
				$para['fanDian']=0;
			}
			
			if(I('fanDian')>=$this->user['fanDian'] || I('fanDianBdw')>=$this->user['fanDianBdw'])
				$this->error('下级返点不能大于自己的返点');
			
			$para['fanDianBdw']=floatval(I('fanDianBdw'));
			$para['lid']=intval(I('lid'));
			
			if(!M('links')->where(array('uid'=>$this->user['uid'], 'lid'=>I('lid')))->find())
				$this->error('此链接不存在');
			
			if(M('links')->save($para))
				$this->success('修改链接成功');
			else
				$this->error('修改链接失败');
		}
		else
		{
			$linkData = M('links')->where(array('lid'=>I('lid'), 'uid'=>$this->user['uid']))->find();
		
			if($linkData['uid']){
				$parentData=M('members')->field('fanDian, fanDianBdw')->find($this->user['uid']);
			}else{
				$parentData['fanDian']=$this->settings['fanDianMax'];
				$parentData['fanDianBdw']=$this->settings['fanDianBdwMax'];
			}
			
			$this->assign('linkData',$linkData);
			$this->assign('parentData',$parentData);
			
			$this->display('Team/update-link');
		}
		
	}
	
	public final  function deletelink(){
		if(IS_POST)
		{
			if(M('links')->where(array('lid'=>I('lid'), 'uid'=>$this->user['uid']))->delete())
				$this->success('删除成功', U('Team/linklist'));
			else
				$this->error('删除失败');
		}
	}
	public final  function getlink(){
		$linkData = M('links')->where(array('lid'=>I('lid'), 'uid'=>$this->user['uid']))->find();
		
		if($linkData['uid']){
			$parentData=M('members')->field('fanDian, fanDianBdw')->find($this->user['uid']);
		}else{
			$parentData['fanDian']=$this->settings['fanDianMax'];
			$parentData['fanDianBdw']=$this->settings['fanDianBdwMax'];
		}
				
		$this->assign('linkData',$linkData);
		$this->assign('parentData',$parentData);
		$this->display('get-link');
	}
	
	public final function turnMoney()
	{
		$this->display('Team/turn-money');
	}
	public final function turnRecharge(){
		$me = M('members')->find($this->user['uid']);
		//dump($me);
		//dump('--'.think_ucenter_md5(I('coinpwd'), UC_AUTH_KEY));
		if($me['coinPassword']!=think_ucenter_md5(I('coinpwd'), UC_AUTH_KEY))
			$this->error('资金密码不正确');
		
		if(intval(I('amount'))<=0)
			$this->error('转账金额必须大于0');
		if($me['coin']-I('amount')<0)
			$this->error('您的余额不足');
		
		$where['username'] = I('username');
		$child=M('members')->where($where)->find();
		if(!$child)
			$this->error('此用户不存在');
		if(strpos($child['parents'],','.$me['uid'].',')===false)
			$this->error('此用户不是你的下级');
		
		// 添加本人资金流动日志
		$this->addCoin(array(
			'uid'=>$me['uid'],
			'type'=>0,
			'liqType'=>12,
			'info'=>'用户['.$me['username'].']转账给其下级['.I('username').']'.I('amount').'元',
			'extfield0'=>I('amount'),				
			'coin'=>-I('amount'),
			'fcoin'=>0
		));	

		// 添加下级资金流动日志
		$this->addCoin(array(
			'uid'=>$child['uid'],
			'type'=>0,
			'liqType'=>12,
			'info'=>'用户['.$me['username'].']转账给其下级['.I('username').']'.I('amount').'元',
			'extfield0'=>I('amount'),				
			'coin'=>I('amount'),
			'fcoin'=>0
		));
		$this->success('给下级转账成功');
	}
}
