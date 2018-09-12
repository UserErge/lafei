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
 * 前台首页控制器
 * 主要获取首页聚合数据
 */
class IndexController extends HomeController {

	//系统首页
    public function index(){
    	if(IS_CLI){
            $data = M('Content')->field("id,content")->select();
            foreach ($data as $value) {
                $value['content'] = ubb($value['content']);
                M('Content')->save($value);
            }

        } else {
            // $category = D('Category')->getTree();
            // $lists    = D('Document')->lists(null);

            // $this->assign('category',$category);//栏目
            // $this->assign('lists',$lists);//列表
            // $this->assign('page',D('Document')->page);//分页
			// $this->assign('username',$this->user['username']);//分页
			
            $this->display('Index/frame');
        }
    }
	
	public function main(){
		$where=array();
		$where['uid']=$this->user['uid'];
		$where['isDelete']=0;
		$where['lotteryNo']=array('neq','');
		$where['actionTime']=array('between',array(strtotime('03:00'),strtotime('03:00')+24*60*60));
		$myxf = M('bets')->where($where)->field('sum(mode*beiShu*actionNum) as xiaofei')->find();
		$this->assign('myxf',$myxf);
		
		$notice = M('content')->where(array('enable'=>1))->order('id desc')->select();
		$this->assign('notice',$notice);
		
		$login = M('member_session')->where(array('uid'=>$this->user['uid']))->order('id desc')->limit(5)->select();
		
		import('ORG.Net.IpLocation');
		$ip=new \IpLocation();
		foreach($login as $key=>$val)
		{
			$addr = $ip->getlocation($login['loginIP']);	
			$login[$key]['addr']=$addr['country'];
		}
			
		$this->assign('login',$login);
		$this->display();
	}
	
	public final function getLastKjData(){
		//dump($type);
		$type=I('type','','intval');
		$ykMoney=0;
		$czName='重庆时时彩';
		$this->type=$type;
		if(!$lastNo=$this->getGameLastNo($this->type)) $this->error('查找最后开奖期号出错');
		
		$data = M('data')->where(array('type'=>$this->type, 'number'=>$lastNo['actionNo']))->field('data')->find();
		if(!$lastNo['data']=$data['data'])
			//$this->error('获取数据出错');
			return null;
		
		$thisNo=$this->getGameNo($this->type);
		$lastNo['actionName']=$czName;
		$lastNo['thisNo']=$thisNo['actionNo'];
		$lastNo['diffTime']=strtotime($thisNo['actionTime'])-$this->time;
		$lastNo['kjdTime']=$this->getTypeFtime($type);
		//dump($lastNo);
		$this->ajaxReturn($lastNo,'JSON');
		//return $lastNo;
	}

	public final function getQiHao(){
		//$thisNo=$this->getGameNo($this);
		$type=I('type','','intval');
		$thisNo=$this->getGameNo($type);
		$data = array(
			'lastNo'=>$this->getGameLastNo($type),
			'thisNo'=>$this->getGameNo($type),
			'diffTime'=>strtotime($thisNo['actionTime'])-$this->time,
			'validTime'=>$thisNo['actionTime'],
			'kjdTime'=>$this->getTypeFtime($type)
		);
		//dump($data);
		$this->ajaxReturn($data,'JSON');
		//return $data;
	}
	
	public final function getHistoryData(){
		$this->type=I('type','','intval');
		
		//$sql="select time, number, data from {$this->prename}data where type={$this->type} order by number desc,time desc limit 4";
		$history = M('data')->where(array('type'=>$this->type))->order('number desc,time desc')->limit(4)->field('time,number,data')->select();
		
		$this->assign('type',$this->type);
		$this->assign('history',$history);
		$this->display('Game/inc_game_history');
		
	}
	
	public final function userInfo(){
		$Members = M('Members');
		$map = array();
		$map['uid'] = $this->user['uid'];
		$user = $Members->where($map)->field('username,nickname,enable,coin')->find();
		$this->ajaxReturn($user,'json');
	}
		
	/**
	 * ajax取定单列表
	 */
	public final function getOrdered($type=null){
		if(!$this->type) $this->type=I('type','','intval');
		$this->display('Index/inc_game_order_history');
	}

    public function test(){
		//$data=M()->query("SELECT * FROM `gy_members` WHERE  uid=%s",$_GET['id']);
		//dump(M()->getLastSql());
		//dump($data);
        // $table = new \OT\DataDictionary;
        // echo "<pre>".PHP_EOL;
        // $out = $table->generateAll();
        // echo "</pre>";
        // print_r($out);
    }

}
