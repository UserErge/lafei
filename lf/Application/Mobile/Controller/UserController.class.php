<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Mobile\Controller;
use User\Api\UserApi as UserApi;

/**
 * 用户控制器
 * 包括用户中心，用户登录及注册
 */
class UserController extends HomeController {
	
	
	protected function _initialize(){
		
		parent::_initialize();
	}
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
				'regTime'=>$this->time
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
			$this->display('User/register');
		}
	}
	
	public $test;
	/* 登录页面 */
	public function login(){
		if(IS_POST){ //登录验证
			
			$username = I('username');
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
					
					$session=array_merge($session, $this->getBrowser());
					
					if(!($lastid=M('member_session')->add($session))){
						\Think\Log::write('xief '.M('member_session')->getLastSql());
						$this->error('插入登陆记录表失败，登陆失败');;
					}
					$user['sessionId']=$lastid;
					
					$data['isOnLine'] = '0';
					M('member_session')->where('uid='.$user['uid'].' and id<'.$user['sessionId'])->save($data);							
					
					session('user',$user);		
					session('user_auth_sign2', data_auth_sign($_SERVER['HTTP_USER_AGENT']));//session实现ip认证，防止session被盗取时别人可以登录。在adminControll中验证ip是否一致								
					if(I('id'))
						$url = U('game/game?id='.I('id'));
					else 
						$url = U('index/index');
					$this->success('登录成功！',$url);					
				}
			}
			else
			{				
				$error = '用户名或密码错误';
				$this->error($error);
			}
			
		} else { //显示登录表单

			$this->display();
		}
	}

	/* 退出登录 */
	public function logout(){		
		if(session('user')){			
			M('member_session')->where(array('uid'=>session('user.uid')))->save(array('isOnLine'=>0));	
		}
		session('user',null);
		session('user_auth_sign',null);
		session('cart',null);
		$this->success('退出成功！', U('user/login'));
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
	
	/* 个人信息 */
	public function info(){
		if(IS_POST){
			$data['address'] = I('address');
			$data['nickname'] = I('nickname');
			$data['idCard'] = I('idCard');
			$data['mobile'] = I('mobile');
			
			if(M('members')->where(array('id'=>$this->user['id']))->save($data))
			{
				$this->success('保存信息成功');
			}
			else
			{
				$this->error('信息未修改');
			}
		}
		else{
			$user = M('members')->find($this->user['uid']);
			$this->assign('user',$user);
			
			$this->meta_title="个人中心";
			$this->display();
		}
	}
	
	public function infodetail(){
		$user = M('members')->find($this->user['uid']);
		$this->assign('user',$user);
		
		$this->meta_title="个人中心";
		$this->display();
	}
	
	public function address(){
		$user = M('members')->find($this->user['uid']);
		$this->assign('user',$user);
		
		$this->meta_title="个人中心";
		$this->display();
	}
	
	/* 密码 */
	public function password(){
		
		if(IS_POST){			
			$opwd=I('oldpassword');
			if(!$opwd) $this->error('原密码不能为空');
			if(strlen($opwd)<6) $this->error('原密码至少6位');
			if(!$npwd=I('newpassword')) $this->error('密码不能为空');
			if(strlen($npwd)<6) $this->error('密码至少6位');
			if(I('newpassword')!=I('repassword')) $this->error('输入的新密码和确认密码不一致');
			
			$user = M('members')->where('uid='.$this->user['uid'])->find();
			$pwd = $user['password'];
			
			$opwd=think_ucenter_md5($opwd,UC_AUTH_KEY);
			if($opwd!=$pwd) $this->error('原密码不正确');
			
			if(M('members')->where('uid='.$this->user['uid'])->save(array('password'=>think_ucenter_md5($npwd,UC_AUTH_KEY))))
				$this->success('修改密码成功',U('user/info'));
			
			$this->error('修改密码失败或新密码与旧密码一致');
		}
		else{
			
			$this->display();
		}
	}
	
	/* 密码 */
	public function coinpassword(){
		
		if(IS_POST){			
			$opwd=I('oldpassword');
			if(!$opwd) $this->error('原密码不能为空');
			if(strlen($opwd)<6) $this->error('原密码至少6位');
			if(!$npwd=I('newpassword')) $this->error('密码不能为空');
			if(strlen($npwd)<6) $this->error('密码至少6位');
			if(I('newpassword')!=I('repassword')) $this->error('输入的新密码和确认密码不一致');
			
			$user = M('members')->where('uid='.$this->user['uid'])->find();
			$pwd = $user['coinPassword'];
			
			$opwd=think_ucenter_md5($opwd,UC_AUTH_KEY);
			if($opwd!=$pwd) $this->error('原密码不正确');
			
			if(M('members')->where('uid='.$this->user['uid'])->save(array('coinPassword'=>think_ucenter_md5($npwd,UC_AUTH_KEY))))
				$this->success('修改密码成功',U('user/info'));
			
			$this->error('修改密码失败或新密码与旧密码一致');
		}
		else{			
			$this->display();
		}
	}
	
	/* 银行信息 */
	public function bank(){
		$user=M('members')->find($this->user['uid']);
		if(!$user['coinPassword'])
			$this->error('请先设置资金密码',U('user/coinpassword'));
		$map = array();
		$map['uid'] = $this->user['uid'];
		$mybanks = M('member_bank')->where($map)->select();
		$this->assign('mybanks',$mybanks);
		
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
					if(I('bankId')==1){
						//读取系统配置
						$this->getSystemSettings();
						if($coin=floatval($this->settings['huoDongRegister'])){
							$liqType=51;
							$info='首次绑定工行卡赠送';
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
								$this->success(sprintf('更改银行信息成功，由于你第一次绑定工行卡，系统赠送%.2f元', $coin));
							}
						}
					}
					$this->success('更改银行信息成功', U('bank'));
				}else{
					$this->error('更改银行信息出错');
				}
			}
		}
	}
	
	public final function cash(){
		
		$bank = M('member_bank')->where(array('enable'=>1,'uid'=>$this->user['uid']))->select();
		$bankList = M('bank_list')->where(array('isDelete'=>0))->field('id as lid,name')->select();
		$bankList2=array();
		foreach($bankList as $b)
		{
			$bankList2[$b['lid']]=$b;
		}
		foreach($bank as $key=>$b){
			if($bbb=$bankList2[$b['bankId']])
			{
				$bank[$key] = array_merge($b,$bbb);
			}
		}
	
		$this->assign('bank',$bank);
		
		$grade = M('member_level')->where(array('level'=>$this->user['grade']))->field('maxToCashCount')->find();
		$this->assign('maxToCashCount',$grade['maxToCashCount']);
		
		$this->display();
	}
	
	/* 进入充值，生产充值订单 */
	public final function recharge(){
		if(IS_POST){
			if(I('amount')<=0)
				$this->error('充值金额必须大于0');
		
		
			// 插入提现请求表
			unset($para['coinpwd']);
			$para['rechargeId']=$this->getRechId();
			$para['actionTime']=$this->time;
			$para['uid']=$this->user['uid'];
			$para['username']=$this->user['username'];
			$para['actionIP']=$this->ip(true);
			$para['mBankId']=13;
			$para['info']='在线支付';
			$para['amount']=intval(I('amount'));
			
			if(M('member_recharge')->add($para)){
				
				
			}else{
				$this->error('充值订单生产请求出错');
			}
		
			$data['rechargeId'] = $para['rechargeId'];
			$this->ajaxReturn($data,'json');
			
		}else{
			$this->display();
		}
		
	}
	
	private final function getRechId(){
		$rechargeId=mt_rand(100000,999999);
		if(M('member_recharge')->where(array('rechargeId'=>$rechargeId))->find()){
			getRechId();
		}else{
			return $rechargeId;
		}
	}
}
