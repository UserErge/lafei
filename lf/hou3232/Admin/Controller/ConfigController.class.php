<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 后台配置控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */

class ConfigController extends AdminController {

    /**
     * 配置管理
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index(){
    	/* 查询条件初始化 */
    	$map = array();
        if(isset($_GET['name'])){
        	$map['name']  = array('like', '%'.(string)I('name').'%');
        }

		$list = $this->lists('params', $map,'id asc');
		//dump($list);
        $this->assign('list', $list);
        $this->meta_title = '配置管理';
        $this->display();
    }

    /**
     * 新增配置
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function add(){
        if(IS_POST){
            $Config = D('params');
            $data = $Config->create();
            if($data){
                if($Config->add()){
					S('DB_CONFIG_DATA',null);
                    $this->success('新增成功', U('index'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
            $this->meta_title = '新增配置';
            $this->display('edit');
        }
    }

    /**
     * 编辑配置
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function edit($id = 0){
        if(IS_POST){
            $Config = D('params');
            $data = $Config->create();
            if($data){
                if($Config->save()){
					//记录行为
					//action_log('update_config','params',$data['id'],UID);
                    $this->success('更新成功', U('index'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('params')->field(true)->find($id);

            if(false === $info){
                $this->error('获取配置信息错误');
            }
            $this->assign('info', $info);
            $this->meta_title = '编辑配置';
            $this->display();
        }
    }

    /**
     * 批量保存配置
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function save($config){
        if($config && is_array($config)){
            $Config = M('params');
            foreach ($config as $name => $value) {
                $map = array('name' => $name);
                $Config->where($map)->setField('value', $value);
            }
        }
		S('DB_CONFIG_DATA',null);
        $this->success('保存成功！');
    }

    /**
     * 删除配置
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function del(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('params')->where($map)->delete()){
			//记录行为
			action_log('update_config','params',$id,UID);
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    // 获取某个标签的配置参数
    public function group() {
		
		$this->getSystemSettings();
		
		if(IS_POST)
		{
			$params = D('params');
            $post = I('post.');
			$i=0;
			foreach($post as $key=>$value)
			{
				if($value==$this->settings[$key]) continue;
				$i++;
				$map['name'] = $key;
				$d['value'] = $value;
				$str2 = $key.':'.$value.',';
				if(!$return = $params->where($map)->save($d))
					$this->error('网站设置失败');
			}
			if($i==0) $this->error('数据未改动');
			
			$this->addLog(10 , 0, $str2);
			
			$this->deldir();//清除所有缓存
			$this->success('网站设置成功');
		}
		else
		{
			$this->assign('settings',$this->settings);
			$this->meta_title = '网站设置';
			$this->display();
		}       
    }
	private function deldir() {		
		header("Content-type: text/html; charset=utf-8");
		//清文件缓存
		$dirs = array('../html/Runtime/');
		@mkdir('../html/Runtime',0777,true);
		//清理缓存
		foreach($dirs as $value) {
			$this->rmdirr($value);
		}
	}
	
	private function rmdirr($dirname) {
		if (!file_exists($dirname)) {
			return false;
		}
		if (is_file($dirname) || is_link($dirname)) {
			return unlink($dirname);
		}
		$dir = dir($dirname);
		if($dir){
			while (false !== $entry = $dir->read()) {
				if ($entry == '.' || $entry == '..') {
					continue;
				}
				//递归
				$this->rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
			}
		}
		$dir->close();
		return rmdir($dirname);
	}
	
	/*清除数据库*/
	public final function clearData(){
		if(!I('date')) $this->error('日期未填');
		$date=strtotime(I('date')." 00:00:00")+24*3600;
		$sql=" call clearData('$date')";
		//throw new Exception(date("Y-m-d H:i:s",$date));
		$Model = new \Think\Model();
		$return = $Model->query($sql); 
		
		$str2 = '日期：'.I('date');
		$this->addLog(20 , 0, $str2);
		
		$this->success('清除数据库成功');
	}

	/*清除空闲用户*/
	public final function clearUser(){
		if( !is_numeric(I('clearMemberCoin'))) $this->error('金额未填');
		if( !is_numeric(I('clearMemberDate'))) $this->error('天数未填');
		
		$clearMemberCoin=intval(I('clearMemberCoin'));
		$clearMemberDate=strtotime(date('y-m-d 00:00:00',time()))-intval(I('clearMemberDate'))*24*3600;
		
		$sql=" call delUsers($clearMemberCoin,$clearMemberDate)";
		//throw new Exception($sql);
		$Model = new \Think\Model();
		$return = $Model->query($sql);

		$str2 = '分数：'.$clearMemberCoin.'天数：'.$clearMemberDate;
		$this->addLog(20 , 0, $str2);
		
		$this->success('清理用户成功');
	}
	
	public final function bank(){
		$Model = new \Think\Model();
		
    	$username = I('username');

		$list = $Model->table('__MEMBER_BANK__ b,__BANK_LIST__ l')->where('l.id=b.bankId and l.isDelete=0 and b.admin=1')->field('b.*,l.name as bankName')->select();
	
		//dump($Model->getLastSql());
		//dump($list);
        $this->recordList($list);
        $this->meta_title = '用户信息';
        $this->display();
	}
	public final function addBank(){
        if(IS_POST){

            $Config = D('member_bank');
            $data = $Config->create();
            if($data){
				$data['admin'] = 1;
				$data['uid'] = 1;
				
                if($lastid=$Config->add($data)){
					
					$str2 = implode(',',$data);
					$this->addLog(11 , $data['id'], $str2);
                    $this->success('新增充值银行成功', U('config/bank'));
                } else {
                    $this->error('新增充值银行失败');
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
			$map['isDelete'] = 0;
			$list = M('bank_list')->where($map)->select();
			$this->assign('bankList',$list);
            $this->display();
        }
	}
	
	public final function delBank(){
		if(M('member_bank')->where(array('id'=>I('id')))->delete())
		{
			$str2 = implode(',',$data);
			$this->addLog(111 , $data['id'], $str2);
			$this->success('删除成功');
		}
		else
			$this->error('删除失败');
	}
	
	//彩种设置
	public final function lotteryList(){
        if(IS_POST){

            $Config = D('type');
            $data = $Config->create();
            if($data){
				
				if($data['enable']=='on') 
					$data['enable']=1;
				else
					$data['enable']=0;
				
                if($Config->save($data)){
					//记录行为
					$str2 = implode(',',$data);
					$this->addLog(12 , $data['id'], $str2);
                    $this->success('更新成功');
                } else {
                    $this->error('更新失败或数据未改动');
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
			
			$map['isDelete'] = 0;
			$list = M('type')->where($map)->select();
			$this->assign('list',$list);
            $this->display();
        }
	}
	
	//玩法设置
	public final function playList(){
		if(IS_POST)
		{
			
		}
		else
		{
			$chiTypes=array(
				1=>'时时彩/五分彩/2分彩/韩国1.5分彩',
				2=>'11选5',
				3=>'3D/P3/时时乐',
				6=>'PK10',
				8=>'快8',
				9=>'快三',
			);
			
			if(!I('type'))
				$type = 1;
			else
				$type=I('type');
			
			$groups = M('played_group')->where(array('type'=>$type))->order('sort')->select();
			$playeds = M('played')->where()->order('sort')->select();
			
			$this->assign('chiTypes',$chiTypes);
			$this->assign('type',$type);
			$this->assign('groups',$groups);
			$this->assign('playeds',$playeds);
			
			$this->display();
		}
		
	}
	
	//更改group状态
	public final function switchPlayedGroupStatus(){
		if(IS_POST)
		{
			$data['id'] = I('id');
			if(I('enable'))
				$data['enable'] =0;
			else
				$data['enable'] =1;
			
			if(M('played_group')->save($data))
			{
				$str2 = implode(',',$data);
				$this->addLog(131 , $data['id'], $str2);
				$this->success('更新成功',U('config/playList?type='.I('type')));
			}
			else
				$this->error('更新失败');
		}
	}
	
	//更改played状态
	public final function switchPlayedStatus(){
		if(IS_POST)
		{			
			$Config = D('played');
            $data = $Config->create();
            if($data){
				
				if($data['enable']=='on') 
					$data['enable']=1;
				else
					$data['enable']=0;
				
                if($Config->save($data)){
					//记录行为
					//action_log('update_data_time','data_time',$data['id'],UID);
					
					$str2 = implode(',',$data);
					$this->addLog(13 , $data['id'], $str2);
                    $this->success('更新成功',U('config/playList?type='.I('type')));
                } else {
                    $this->error('更新失败或数据未改动');
                }
            } else {
                $this->error($Config->getError());
            }
		}
	}
}
