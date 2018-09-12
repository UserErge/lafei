<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use User\Api\UserApi;

/**
 * 后台用户控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */

class UserController extends AdminController {

    static protected $allow = array( 'updatePassword','updateNickname','submitPassword','submitNickname');

    /**
     * 用户管理首页
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index(){
    	$username = I('username');
    	if(isset($username)){
    		$map['username']  = array('like', '%'.(string)$username.'%');
    	}
		
		if(I('parentId'))
			$map['parentId'] = I('parentId');
		
        //$list   = $this->lists('Members', $map , 'uid asc');
		$map['admin'] = 0;
		$map['isDelete'] = 0;
		$data = M('Members')->where($map)->order('uid')->select();
		$list = array();
		$i=0;
		foreach($data as $d)
		{
			$list[$i]=$d;
			$map=array();
			$map['uid'] = $d['uid'];
			$logip = M('member_session')->where($map)->order('id desc')->limit(1)->select();
			
			if($logip)
			{
				$list[$i]['loginIP'] = $logip[0]['loginIP'];
				$list[$i]['accessTime'] = $logip[0]['accessTime'];
				//$list[$i]['isOnLine'] = $logip[0]['isOnLine'];
			}
			
			$i++;
		}
		//dump(M('Members')->getLastSql());
		//dump($list);
        $this->recordList($list);
        $this->meta_title = '用户信息';
        $this->display();
    }

	
    /**
     * 修改昵称初始化
     * @author huajie <banhuajie@163.com>
     */
    public function updateNickname(){
        $nickname = M('Member')->getFieldByUid(UID, 'nickname');
        $this->assign('nickname', $nickname);
        $this->meta_title = '修改昵称';
        $this->display();
    }

    /**
     * 修改昵称提交
     * @author huajie <banhuajie@163.com>
     */
    public function submitNickname(){
        //获取参数
        $uid = UID;
        $nickname = I('post.nickname');
        $password = I('post.password');
        empty($nickname) && $this->error('请输入昵称');
        empty($password) && $this->error('请输入密码');

		$user = session('user_auth');
		$user = D('members')->where(array('uid'=>$user['uid']))->find();
		if($user['password']!=think_ucenter_md5($password, UC_AUTH_KEY))
			$this->error('输入的密码错误');
		
		$res = D('members')->where(array('uid'=>$uid))->save(array('username'=>$nickname));

        if($res){
        	$user['username'] = $data['nickname'];
        	session('user_auth', $user);
        	session('user_auth_sign', data_auth_sign($user));
            $this->success('修改昵称成功！');
        }else{
            $this->error('修改昵称失败！');
        }
		
        //密码验证
        // $User = new UserApi();
        // $uid = $User->login($uid, $password, 4);
        // ($uid == -2) && $this->error('密码不正确');

        // $Member = D('Member');
        // $data = $Member->create(array('nickname'=>$nickname));
        // if(!$data){
            // $this->error($Member->getError());
        // }

        // $res = $Member->where(array('uid'=>$uid))->save($data);

        // if($res){
        	// $user = session('user_auth');
        	// $user['username'] = $data['nickname'];
        	// session('user_auth', $user);
        	// session('user_auth_sign', data_auth_sign($user));
            // $this->success('修改昵称成功！');
        // }else{
            // $this->error('修改昵称失败！');
        // }
    }

    /**
     * 修改密码初始化
     * @author huajie <banhuajie@163.com>
     */
    public function updatePassword(){
    	$this->meta_title = '修改密码';
        $this->display();
    }

    /**
     * 修改密码提交
     * @author huajie <banhuajie@163.com>
     */
    public function submitPassword(){
        //获取参数
        $uid        =   UID;
        $password   =   I('post.old');
        empty($password) && $this->error('请输入原密码');
        $data['password'] = I('post.password');
        empty($data['password']) && $this->error('请输入新密码');
        $repassword = I('post.repassword');
        empty($repassword) && $this->error('请输入确认密码');
		
        if($data['password'] !== $repassword){
            $this->error('您输入的新密码与确认密码不一致');
        }
		
		if($data['password'] == $password){
            $this->error('输入的新密码与旧密码一致');
        }
		
		$user = session('user_auth');
		$user = D('members')->where(array('uid'=>$user['uid']))->find();
		if($user['password']!=think_ucenter_md5($password, UC_AUTH_KEY))
			$this->error('输入的原密码错误');
		
		$return = D('members')->where(array('uid'=>$user['uid']))->save(array('password'=>think_ucenter_md5($repassword, UC_AUTH_KEY)));
		if($return)
			$this->success('修改密码成功！');
		else
			$this->error('修改密码失败');

        // $Api = new UserApi();
        // $res = $Api->updateInfo($uid, $password, $data);
        // if($res['status']){
            // $this->success('修改密码成功！');
        // }else{
            // $this->error($res['info']);
        // }
    }
	
	
    /**
     * 用户行为列表
     * @author huajie <banhuajie@163.com>
     */
    public function action(){
        //获取列表数据
        $Action = M('Action')->where(array('status'=>array('gt',-1)));
        $list   = $this->lists($Action);
        int_to_string($list);
        $this->assign('_list', $list);
        $this->meta_title = '用户行为';
        $this->display();
    }

    /**
     * 新增行为
     * @author huajie <banhuajie@163.com>
     */
    public function addAction(){
        $this->meta_title = '新增行为';
        $this->display('editaction');
    }

    /**
     * 编辑行为
     * @author huajie <banhuajie@163.com>
     */
    public function editAction(){
        $id = I('get.id');
        empty($id) && $this->error('参数不能为空！');
        $data = M('Action')->field(true)->find($id);

        $this->assign($data);
        $this->meta_title = '编辑行为';
        $this->display();
    }

    /**
     * 更新行为
     * @author huajie <banhuajie@163.com>
     */
    public function saveAction(){
        $res = D('Action')->update();
        if(!$res){
            $this->error(D('Action')->getError());
        }else{
            if($res['id']){
                $this->success('更新行为成功！', U('action'));
            }else{
                $this->success('新增行为成功！', U('action'));
            }
        }
    }

    /**
     * 设置一条或者多条数据的状态
     * @author huajie <banhuajie@163.com>
     */
    public function setStatus(){
        /*参数过滤*/
        $ids = I('request.ids');
        $status = I('request.status');
        if(empty($ids) || !isset($status)){
            $this->error('请选择要操作的数据');
        }
        //删除缓存
        S('action_list', null);

        /*拼接参数并修改状态*/
        $Model = 'Action';
        $map = array();
        if(is_array($ids)){
            $map['id'] = array('in', implode(',', $ids));
        }elseif (is_numeric($ids)){
            $map['id'] = $ids;
        }
        switch ($status){
            case -1 : $this->delete($Model, $map, array('success'=>'删除成功','error'=>'删除失败'));break;
            case 0 : $this->forbid($Model, $map, array('success'=>'禁用成功','error'=>'禁用失败'));break;
            case 1 : $this->resume($Model, $map, array('success'=>'启用成功','error'=>'启用失败'));break;
            default : $this->error('参数错误');break;
        }
    }

    /**
     * 会员状态修改
     * @author 朱亚杰 <zhuyajie@topthink.net>
     */
    public function changeStatus($method=null){
        $id = array_unique((array)I('id',0));
        if( in_array(C('USER_ADMINISTRATOR'), $id)){
            $this->error("不允许对超级管理员执行该操作!");
        }
        $id = is_array($id) ? implode(',',$id) : $id;
        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
		
		$str2 = $method.':'.$id;
		$this->addLog(55 , 0 , $str2);
		
        switch ( strtolower($method) ){
            case 'forbiduser':
                $this->forbid('Members', array('uid'=>array('in',$id)) );
                break;
            case 'resumeuser':
                $this->resume('Members', array('uid'=>array('in',$id)) );
                break;
            case 'deleteuser':
                $this->delete('Members', array('uid'=>array('in',$id)) );
                break;
			case 'undeleteuser':
                $this->undelete('Members', array('uid'=>array('in',$id)) );
                break;
            default:
                $this->error('参数非法');
        }
    }

    public function add_2($username = '', $password = '', $repassword = '', $email = ''){
        if(IS_POST){
            /* 检测密码 */

            $Config = D('Members');
            $data = $Config->create();
            if($data){
				$data['password'] = think_ucenter_md5($data['password'], UC_AUTH_KEY);
				$data['regTime'] = time();
                if($lastid=$Config->add($data)){
					$data['uid']= $lastid;
					$data['parentId']= 1;
					$data['parents']='1,'.$lastid;
					$Config->save($data);
					
					$this->addLog(4 , $data['uid'], $data['username']);
                    $this->success('新增用户成功', U('index'));
                } else {
                    $this->error('新增用户失败');
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
			$this->meta_title="新增用户";
            $this->display();
        }
    }
	
	
	public function edit(){
        if(IS_POST){
			
            $Config = D('Members');
            $data = $Config->create();
			
			$isBank=false;
			//重置银行
			if(I('resetBank')=='1')
			{
				$isBank = M('member_bank')->where(array('uid'=>$data['uid']))->delete();
				$this->addLog(23 , $data['uid'] , '');
			}
					
            if($data){
				if($data['password']!=I('oldpassword'))
					$data['password'] = think_ucenter_md5($data['password'], UC_AUTH_KEY);
				
				if($data['coinPassword']!=I('oldcoinPassword'))
					$data['coinPassword'] = think_ucenter_md5($data['coinPassword'], UC_AUTH_KEY);
				
				if($data['parentPath']!=I('oldparentPath')){
					if(M('members')->where(array('parentId'=>I('uid')))->find())
						$this->error('此人是存在下级的代理，不允许更改上级，否则平台数据容易错乱');
					$parent_array = explode('>', I('parentPath'));
					foreach($parent_array as $par){
						$mem = M('members')->where(array('username'=>$par))->field('uid')->find();
						if(!$mem)
							$this->error('上级关系中有用户不存在或格式未按要求填写');
						$parent_path = $parent_path.','.$mem['uid'];
					}
					$parent_path = substr($parent_path,1);
					$data['parents'] = $parent_path;
				}
                if($Config->save($data)){
					
					$str2 = implode(',',$data);
					$this->addLog(5 , $data['uid'] , $str2);
					
                    $this->success('编辑用户成功', U('index'));
                } else {
					if(!$isBank)
						$this->error('编辑用户失败或数据未更改');
					else
						$this->success('编辑用户成功', U('index'));
                    
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
			$Mem = D('Members');
			$map['uid'] = I('id');
			$user = $Mem->where($map)->find();
			$this->assign('user',$user);
			
			$parents = $Mem->where(array('uid'=>array('in',$user['parents'])))->select();
			$parentPath='';
			foreach($parents as $parent)
			{
				$parentPath=$parentPath.'>'.$parent['username'];
			}
			$this->assign('parentPath',substr($parentPath,1));
			
			$this->meta_title = '编辑用户';
            $this->display();
        }
    }
	
	public function bank(){
		$Model = new \Think\Model();
		
    	$username = I('username');
    	if(isset($username)){
			//$map['username']  = array('like', '%'.(string)$username.'%');
			$list = $Model->table('__MEMBER_BANK__ b,__BANK_LIST__ l,__MEMBERS__ m')->where("b.uid=m.uid and l.id=b.bankId and l.isDelete=0 and b.enable=1 and b.admin=0 and m.username like '%".(string)$username."%'")->field('b.*,m.username as mUsername,l.name as bankName')->select();
    	}
		else
		{
			$list = $Model->table('__MEMBER_BANK__ b,__BANK_LIST__ l,__MEMBERS__ m')->where('b.uid=m.uid and l.id=b.bankId and l.isDelete=0 and b.enable=1 and b.admin=0')->field('b.*,m.username as mUsername,l.name as bankName')->select();
		}
	
		//dump($Model->getLastSql());
		//dump($list);
        $this->recordList($list);
        $this->meta_title = '用户信息';
        $this->display();
    }
	
	 /**
     * 用户管理首页
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function admin_list(){
		$this->user = session('user_auth');
		if($this->user['username']!='admin')
			$this->error('只有admin管理员才能进行此操作');
        //$list   = $this->lists('Members', $map , 'uid asc');
		$map['admin'] = 1;
		//$map['isDelete'] = 0;
		$data = M('Members')->where($map)->order('uid')->select();
		$list = array();
		$i=0;
		foreach($data as $d)
		{
			$list[$i]=$d;
			$map=array();
			$map['uid'] = $d['uid'];
			$logip = M('member_session')->where($map)->order('id desc')->limit(1)->select();
			
			if($logip)
			{
				$list[$i]['loginIP'] = $logip[0]['loginIP'];
				$list[$i]['accessTime'] = $logip[0]['accessTime'];
				$list[$i]['loginTime'] = $logip[0]['loginTime'];
				//$list[$i]['isOnLine'] = $logip[0]['isOnLine'];
			}
			
			$i++;
		}
		//dump(M('Members')->getLastSql());
		//dump($list);
        $this->recordList($list);
        $this->meta_title = '用户信息';
        $this->display();
    }
	
	public function add_admin(){
		$this->user = session('user_auth');
		if($this->user['username']!='admin')
			$this->error('只有admin管理员才能进行此操作');
		
		if(IS_POST){
			$map['username']= I('username');
			$map['password']= think_ucenter_md5(I('password'), UC_AUTH_KEY);
			
			$Mem = D('Members');
			
			if(I('password') !=I('repassword'))
				$this->error('输入的两次密码不一致');
			if(I('id')){
				$map['uid']=I('id');
				if($Mem->save($map))
					$this->success('修改管理员密码成功', U('User/admin_list'));
				else
					$this->error('修改管理员密码失败');
			}else {
				$map['admin']=1;
				$map['sb']=9;
				if($Mem->add($map))
					$this->success('新增管理员成功', U('User/admin_list'));
				else
					$this->error('新增管理员失败');
			}
			
		}else {
			$Mem = D('Members');
			$map['uid'] = I('id');
			$map['admin'] = 1;
			$user = $Mem->where($map)->find();
			$this->assign('user',$user);
			
			$this->meta_title = '编辑管理员';
            $this->display();
		}
	}
	
	public function del_admin(){
		$this->user = session('user_auth');
		if($this->user['username']!='admin')
			$this->error('只有admin管理员才能进行此操作');
	
		if(M('Members')->save(array('uid'=>I('id'), 'isDelete'=>I('isDelete'))))
			$this->success('操作成功', U('User/admin_list'));
		else
			$this->error('操作失败');
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

}
