<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Home\Controller;
use User\Api\UserApi as UserApi;

/**
 * 用户控制器
 * 包括用户中心，用户登录及注册
 */
class UserController extends HomeController {

	/* 用户中心首页 */
	public function index(){
		//$login = A('User/User', 'Api')->login('麦当苗儿ss', 'aoiujz');
		//$login = A('User/User', 'Api')->register('麦当苗儿ss', 'aoiujz', 'xiaoxiaoxiao@qq.com');
		//$login = A('User/User', 'Api')->checkEmail('zuojiazi@vip.qq.com');


		// dump($login);
		
	}
	
	public function test(){
		// $Model = new \Think\Model();
		// $sql = 'select * from ssc_members where uid=%d';
		// dump($sql);
		// //$return=$Model->query($sql,$_GET['id']);
		// dump($Model->getLastSql());
		// dump($return);exit;
	}
	
	/**
	 * 推广注册
	 */
	public final function register(){
				
		if(IS_POST)
		{
			if(!preg_match("/^[0-9a-zA-Z]{4,30}$/",I('username'))){
				$this->error('用户名只能由英文和数字组成，长度4-30个字符');
			}
			/* 检测验证码 */
			if(!check_verify(I('verify'))){
				$this->error('验证码输入错误！');
			}
			
			$pwd = I('password');
			if(strlen($pwd)<6) $this->error('密码至少6位');
			$linkData = M('links')->where(array('lid'=>I('lid'), 'uid'=>I('uid')))->find();
			
			if(!$linkData) $this->error('此链接不存在');
			if(!$user2 = M('members')->find($linkData['uid'])) $this->error('上级不存在');
			if(M('members')->where(array('username'=>I('username')))->find()) $this->error('用户名'.I('username').'已存在');
			$para=array(
				'username'=>I('username'),
				'type'=>$linkData['type'],
				'password'=>think_ucenter_md5(I('password'), UC_AUTH_KEY),
				'parentId'=>$linkData['uid'],
				'parents'=>$user2['parents'],
				'fanDian'=>$linkData['fanDian'],
				'fanDianBdw'=>$linkData['fanDianBdw'],
				'qq'=>I('qq'),
				'regIP'=>$this->ip(true),
				'regTime'=>$this->time,
				'regPath'=> I('regPath')
			);
			
			M()->startTrans();
			if($lastid = M('members')->add($para))
			{
				if(M('members')->save(array('uid'=>$lastid, 'parents'=>$user2['parents'].','.$lastid)))
				{
					M()->commit();//成功则提交
					
					$user = M('members')->find($lastid);
					$ip = $this->ip(true);
					$session=array(
						'uid'=>$user['uid'],
						'username'=>$user['username'],
						'session_key'=>0,//session_id(),
						'loginTime'=>$this->time,
						'accessTime'=>$this->time,
						'loginIP'=>$ip,						
					);
					
					if(!($lastid=M('member_session')->add($session)))
						$this->error('插入登陆记录表失败，登陆失败');;
					$user['sessionId']=$lastid;
					session('user',$user);		
					session('user_auth_sign2', data_auth_sign($_SERVER['HTTP_USER_AGENT']));
					$this->success('注册成功',U('index/index'));
				}
			}
			
			M()->rollback();//不成功，则回滚
			$this->error('注册失败');
		}
		else
		{
			//如果手机打开，跳到手机站
			$agent = $_SERVER['HTTP_USER_AGENT'];
			if(strpos($agent,"comFront") || strpos($agent,"iPhone") || strpos($agent,"MIDP-2.0") || strpos($agent,"Opera Mini") || strpos($agent,"UCWEB") || strpos($agent,"Android") || strpos($agent,"Windows CE") || strpos($agent,"SymbianOS"))
			{
				header('location: '.U('Mobile/User/register'));
				return;
			}
			$this->display('User/register');
		}
	}
	
	public $test;
	public function login (){
		if(IS_POST){
			/* 检测验证码 */
			if(!check_verify(I('verify'))){
				$this->error('验证码输入错误！');
			}
			
			$username = I('username');
				
			if($username=='')
			{
				$error = '用户名不能为空';
				$this->error($error);
			}
			$Members = M('Members');
			$map = array();
			$map['username'] = $username;
			$user = $Members->where($map)->find();
			
			if($user){
				
				if($user['isDelete']==1)
				{
					$error = '用户已被删除';
					$this->error($error);
				}
				else if($user['enable']==0)
				{
					$error = '用户已被冻结';
					$this->error($error);
				}
				else{
					session('username',null);
					session('username',$username);
					session('safepwd',null);
					session('safepwd',$user['safepwd']);
					$this->success('用户名正确！',U('user/logined'));
				}
				
			}else{
				$this->error('用户不存在');
			}
		}
		else {
			//如果手机打开，跳到手机站
			$agent = $_SERVER['HTTP_USER_AGENT'];
			if(strpos($agent,"comFront") || strpos($agent,"iPhone") || strpos($agent,"MIDP-2.0") || strpos($agent,"Opera Mini") || strpos($agent,"UCWEB") || strpos($agent,"Android") || strpos($agent,"Windows CE") || strpos($agent,"SymbianOS"))
			{
				header('location: '.U('Mobile/index/index'));
				return;
			}
		
			$this->display();
		}
	}
	/* 登录页面 */
	public function logined(){
		if(IS_POST){ //登录验证
			
			$username = session('username');
			$password = I('password');
			
			if($username=='' || $password == '')
			{
				$error = '用户名或密码不能为空';
				$this->error($error);
			}
			
			$Members = M('Members');
			$map = array();
			$map['username'] = $username;
			$map['password'] = think_ucenter_md5($password, UC_AUTH_KEY);
			$user = $Members->where($map)->find();
			
			if($user)
			{
				if($user['isDelete']==1)
				{
					$error = '用户已被删除';
					$this->error($error);
				}
				else if($user['enable']==0)
				{
					$error = '用户已被冻结';
					$this->error($error);
				}
				else
				{
					$ip = $this->ip(true);
					$session=array(
						'uid'=>$user['uid'],
						'username'=>$user['username'],
						'session_key'=>0,//session_id(),
						'loginTime'=>$this->time,
						'accessTime'=>$this->time,
						'loginIP'=>$ip,						
					);
					
					//$session=array_merge($session, $this->getBrowser());
					
					if(!($lastid=M('member_session')->add($session)))
						$this->error('插入登陆记录表失败，登陆失败');;
					$user['sessionId']=$lastid;
					
					$data['isOnLine'] = '0';
					M('member_session')->where('uid='.$user['uid'].' and id<'.$user['sessionId'])->save($data);							
					
					session('username',null);
					session('safepwd',null);
					session('user',null);
					session('user',$user);		
					session('user_auth_sign2', data_auth_sign($_SERVER['HTTP_USER_AGENT']));//session实现ip认证，防止session被盗取时别人可以登录。在adminControll中验证ip是否一致
														
					$this->success('登录成功！',U('Index/index'));				
				}				
			}
			else
			{
				$error = '密码错误';
				$this->error($error);
			}
			
		} else { //显示登录表单
			
			$this->display();
		}
	}
	
	/* 验证码，用于登录和注册 */
	public function verify(){
		$verify = new \COM\Verify();
		$verify->imageL = 90;
		$verify->imageH = 43;
		$verify->fontSize=18;
		$verify->fontttf='4.ttf';
		$verify->entry(1);
	}

	/* 退出登录 */
	public function logout(){		
		if(session('user')){			
			M('member_session')->where(array('uid'=>$_SESSION['user']['uid']))->save(array('isOnLine'=>0));	
		}
		session('user',null);
		session('user_auth_sign2',null);
		$this->success('退出成功！', U('User/login'));
	}

	
	/* 个人信息 */
	public function info(){
		if(IS_POST){
			$user = M('members')->find($this->user['uid']);
			$this->assign('user',$user);
			if(think_ucenter_md5(I('password'),UC_AUTH_KEY)!=$user['password'])
				$this->error('登录密码错误');
			else
			{
				if(M('members')->where('uid='.$this->user['uid'])->save(array('nickname'=>I('nickname'),'safepwd'=>I('safepwd'))))
					$this->success('修改成功');
				else
					$this->error('没有改动');
			}
		}
		else{
			$user = M('members')->find($this->user['uid']);
			$this->assign('user',$user);
			
			$map = array();
			$map['uid'] = $this->user['uid'];
			$mybank = M('member_bank')->where($map)->find();
			$this->assign('mybank',$mybank);
			
			$map=array();
			$map['uid'] = $this->user['uid'];
			$map['isOnLine'] = 1;
			$login = M('member_session')->where($map)->order('id desc')->find();
			import('ORG.Net.IpLocation');
			$ip=new \IpLocation();
			$addr = $ip->getlocation($login['loginIP']);	
			$login['addr']=$addr['country'];
			$this->assign('login',$login);
			
			$map = array();
			$map['parents'] = array('like','%,'.$this->user['uid'].',%');
			$childs = M('members')->where($map)->select();
			$regcount=0;
			$time = strtotime('00:00:00');
			$logins = M('member_session')->where(array('accessTime'=>array('gt',time()-15*60),'isOnLine'=>1))->order('id')->select();
			foreach($logins as $l){
				$logins2[$l['uid']]=$l;
			}
			
			foreach($childs as $child)
			{
				$coins +=$child['coin'];
				if($child['regTime']>$time)
					$regcount++;
				if($logins2[$child['uid']])
					$linecount++;			
			}
			$childinfo['coins'] = $coins+$user['coin'];
			$childinfo['count'] = count($childs)+1;
			$childinfo['linecount'] = $linecount+1;
			$childinfo['regcount'] = $regcount;
			$this->assign('childinfo',$childinfo);
			
			$this->display();
		}
		
	}
	
	/* 密码 */
	public function password(){
		
		$this->display();
	}
	
	/* 设置密码 */
	public function setPasswd(){		
		if(IS_POST){
			
			$opwd=I('oldpassword');
			if(!$opwd) $this->error('原密码不能为空');
			if(strlen($opwd)<6) $this->error('原密码至少6位');
			if(!$npwd=I('newpassword')) $this->error('密码不能为空');
			if(strlen($npwd)<6) $this->error('密码至少6位');
			
			$user = M('members')->where('uid='.$this->user['uid'])->find();
			$pwd = $user['password'];
			
			$opwd=think_ucenter_md5($opwd,UC_AUTH_KEY);
			if($opwd!=$pwd) $this->error('原密码不正确');
			
			if(M('members')->where('uid='.$this->user['uid'])->save(array('password'=>think_ucenter_md5($npwd,UC_AUTH_KEY))))
				$this->success('修改密码成功');
			
			$this->error('修改密码失败或新密码与旧密码一致');
		}
	}
	
	/* 设置资金密码 */
	public function setCoinPwd(){		
		if(IS_POST){
			
			$opwd=I('oldpassword');
			if(!$npwd=I('newpassword')) $this->error('资金密码不能为空');
			if(strlen($npwd)<6) $this->error('资金密码至少6位');
			
			$pwd = M('members')->where('uid='.$this->user['uid'])->find();
			
			if(!$pwd['coinPassword']){
				$npwd=think_ucenter_md5($npwd,UC_AUTH_KEY);
				if($npwd==$pwd['password']) $this->error('资金密码与登录密码不能一样');
			}else{
				if(think_ucenter_md5($opwd,UC_AUTH_KEY)!=$pwd['coinPassword']) $this->error('原资金密码不正确');
				$npwd=think_ucenter_md5($npwd,UC_AUTH_KEY);
				if($npwd==$pwd['password']) $this->error('资金密码与登录密码不能一样');
			}
			if(M('members')->where('uid='.$this->user['uid'])->save(array('coinPassword'=>$npwd)))
			{
				$_SESSION['user']['coinPassword'] = think_ucenter_md5($npwd,UC_AUTH_KEY);
				$this->success('资金密码设置成功');
			}
			
			$this->error('修改资金密码失败');
		}
	}
	
	/* 银行信息 */
	public function bank(){
		$user=M('members')->find($this->user['uid']);
		if(!$user['coinPassword'])
			$this->error('请先设置资金密码',U('user/password'));
		$map = array();
		$map['uid'] = $this->user['uid'];
		$mybank = M('member_bank')->where($map)->select();
		$this->assign('mybank',$mybank);
		
		$banks = M('bank_list')->where('isDelete=0')->order('sort')->select();
		foreach($banks as $var){
			$banks2[$var['id']]=$var;
		}
		$this->assign('banks',$banks2);
		
		$this->display();
	}
	/**
	 * 设置银行帐户
	 */
	public function setCBAccount(){
		if(IS_POST){
			$user = M('members')->where('uid='.$this->user['uid'])->find();

			if(think_ucenter_md5(I('coinPassword'), UC_AUTH_KEY)!=$user['coinPassword']) $this->error('资金密码不正确');
			
			
			//检查银行账号唯一
			$map = array();
			$map['account'] = I('account');
			$bank = M('member_bank')->where($map)->find();
			if($bank)
				$this->error('该'.I('account').'银行账号已经使用');
			
			$map = array();
			$map['uid'] = $this->user['uid'];
			$bank = M('member_bank')->where($map)->select();	
			if(count($bank)>2){
				$this->error('最多只能绑定三张银行卡');				
			}else{
				
				if(count($bank)>0 && I('username')!=$bank[0]['username'])
					$this->error('绑定的新银行持卡人必须跟之前绑定的一致');
				$b['uid'] = $this->user['uid'];
				$b['editEnable'] = 0;
				$b['bankId'] = I('bankId');
				$b['account'] = I('account');
				$b['username'] = I('username');
				$b['actionTime']=time();
				
				if(M('member_bank')->add($b)){
					// 如果是工行，参与工行卡首次绑定活动
					if(I('bankId')){
						//读取系统配置
						$this->getSystemSettings();
						if($coin=floatval($this->settings['huoDongRegister'])){
							$liqType=51;
							$info='首次绑定银行卡赠送';
							$ip=$this->ip(true);
							$bankAccount=I('account');
							
							if(!$ip)
								$ip=0;
							
							// 查找是否已经赠送过
							//$sql="select id from {$this->prename}coin_log where liqType=$liqType and (`uid`={$this->user['uid']} or extfield0=$ip or extfield1=$bankAccount) limit 1";
														
							$where['uid']  = $this->user['uid'];
							$where['extfield0']  = $ip;
							$where['extfield1'] = $bankAccount;
							$where['_logic'] = 'or';
							$map['_complex'] = $where;
							$map['liqType']  = $liqType;

							if(!M('coin_log')->where($map)->find()){
								$this->addCoin(array(
									'coin'=>$coin,
									'liqType'=>$liqType,
									'info'=>$info,
									'extfield0'=>$ip,
									'extfield1'=>$bankAccount
								));
								$this->success(sprintf('更改银行信息成功，由于你第一次绑定银行卡，系统赠送%.2f元', $coin));
							}
						}
					}
					$this->success('更改银行信息成功');
				}else{
					$this->error('更改银行信息出错');
				}
			}
		}
	}
	
	

	/**
	 * 获取用户注册错误信息
	 * @param  integer $code 错误编码
	 * @return string        错误信息
	 */
	private function showRegError($code = 0){
		switch ($code) {
			case -1:  $error = '用户名长度必须在16个字符以内！'; break;
			case -2:  $error = '用户名被禁止注册！'; break;
			case -3:  $error = '用户名被占用！'; break;
			case -4:  $error = '密码长度必须在6-30个字符之间！'; break;
			case -5:  $error = '邮箱格式不正确！'; break;
			case -6:  $error = '邮箱长度必须在1-32个字符之间！'; break;
			case -7:  $error = '邮箱被禁止注册！'; break;
			case -8:  $error = '邮箱被占用！'; break;
			case -9:  $error = '手机格式不正确！'; break;
			case -10: $error = '手机被禁止注册！'; break;
			case -11: $error = '手机号被占用！'; break;
			default:  $error = '未知错误';
		}
		return $error;
	}
	
	
	private function getBrowser(){
		$flag=$_SERVER['HTTP_USER_AGENT'];
		$para=array();
		
		// 检查操作系统
		if(preg_match('/Windows[\d\. \w]*/',$flag, $match)) $para['os']=$match[0];
		
		if(preg_match('/Chrome\/[\d\.\w]*/',$flag, $match)){
			// 检查Chrome
			$para['browser']=$match[0];
		}elseif(preg_match('/Safari\/[\d\.\w]*/',$flag, $match)){
			// 检查Safari
			$para['browser']=$match[0];
		}elseif(preg_match('/MSIE [\d\.\w]*/',$flag, $match)){
			// IE
			$para['browser']=$match[0];
		}elseif(preg_match('/Opera\/[\d\.\w]*/',$flag, $match)){
			// opera
			$para['browser']=$match[0];
		}elseif(preg_match('/Firefox\/[\d\.\w]*/',$flag, $match)){
			// Firefox
			$para['browser']=$match[0];
		}else{
			$para['browser']='unkown';
		}
		//print_r($para);exit;
		$para=array();
		return $para;
	}
	
	/*盈亏报表*/
	public final function report(){

		$this->reportSearch();		
		$this->display('User/report');		
	}
	public final function searchReport(){
		$this->reportSearch();
		$this->display('User/report-list');
	}
	
	public final function reportSearch(){
		
		$para=I('get.');
		
		$where = array();
		// 用户限制
		$uid=$this->user['uid'];
		
		$where['uid'] = $uid;
		
		$userList = M('members')->where($where)->order('uid')->select();
		
		foreach($userList as $user)
		{
			$userStr = $userStr.$user['uid'].',';
		}
		
		$map=array();
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
		$map['uid'] = array('in',$userStr);
		
		$coinList = M('coin_log')->where($map)->field("uid,sum(case when liqType in ('2','3') then coin else 0 end) as fanDianAmount, 
		0-sum(case when liqType in ('101','102','7') then coin else 0 end) as betAmount, 
		sum(case when liqType=6 then coin else 0 end) as zjAmount, 
		0-sum(case when liqType=107 then fcoin else 0 end) as cashAmount, 
		sum(case when liqType=1 then coin else 0 end) as rechargeAmount, 
		sum(case when liqType in ('50','51','52','53') then coin else 0 end) as brokerageAmount")->group('uid')->select();
		
		$i = 0;
		foreach($userList as $user)
		{
			foreach($coinList as $coin)
			{
				if($coin['uid'] == $user['uid'])
				{
					$data[$i] = array_merge($user,$coin);
					$i++;
				}
			}
		}
		
		$this->recordList($data);	
	}
}
