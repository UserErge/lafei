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
class ScoreController extends AdminController {

    static protected $allow = array( 'verify');

    /**
     * 后台首页
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public final function index(){
        $para = I('post.');
		
		if(!$para)
			$para = I('get.');
		
		//dump($para);
		// 用户限制
		if($para['username']){
			$userWhere=" and m.username like '%{$para['username']}%'";
		}
		
		// 时间限制
		if($para['fromTime'] && $para['toTime']){
			$fromTime=strtotime($para['fromTime']);
			$toTime=strtotime($para['toTime'])+24*3600;
			$timeWhere=" and s.swapTime between $fromTime and $toTime";
		}elseif($para['fromTime']){
			$fromTime=strtotime($para['fromTime']);
			$timeWhere=" and s.swapTime>=$fromTime";
		}elseif($para['toTime']){
			$toTime=strtotime($para['toTime'])+24*3600;
			$timeWhere=" and s.swapTime<$toTime";
		}else{
			$timeWhere=' and s.swapTime>'.strtotime('00:00');
		}
		
		$Model = new \Think\Model();
		$list = $Model->table('__SCORE_SWAP__ s,__MEMBERS__ m,__SCORE_GOODS__ g')->where('s.uid=m.uid and s.goodId=g.id '.$userWhere.$timeWhere)->order('s.id desc')->field('s.*, g.title goodsTitle, g.price goodsPrice, m.username userName')->select();
		//dump($Model->getLastSql());
		
		$this->recordList($list);
		
		$this->meta_title = '兑换记录';
		$this->display();
    }
	
	public final function goodslist(){
        $list = M('score_goods')->where(array())->order('id desc')->select();
		//dump($Model->getLastSql());
		
		$this->recordList($list);
		
		$this->meta_title = '兑换管理';
		$this->display();

    }
	
	public final function onScore(){
		if(I('type','','intval')==1)
			$enable=0;
		else
			$enable=1;
		if(M('score_goods')->where(array('id'=>I('id','','intval')))->save(array('enable'=>$enable)))
			$this->success('修改成功',U('score/goodslist'));
		else
			$this->error('修改失败');
	}
	

	public final function del(){
		
		if(M('score_goods')->where(array('id'=>I('id','','intval')))->delete())
			$this->success('删除成功',U('score/goodslist'));
		else
			$this->error('删除失败');
	}
	
	public final function modal(){
		$this->display('Score/goods-modal');
	}
	
	public final function updateGoods(){
		if(I('id'))
		{
			$Config = D('score_goods');
            $data = $Config->create();
            if($data){
				
                if($Config->save($data)){
                    $this->success('更新商品成功', U('score/goodslist'));
                } else {
                    $this->error('更新商品失败');
                }
            } else {
                $this->error($Config->getError());
            }
		}		
		else
		{
			$Config = D('score_goods');
            $data = $Config->create();
            if($data){
				
                if($Config->save($data)){
                    $this->success('新增商品成功', U('score/goodslist'));
                } else {
                    $this->error('新增商品失败');
                }
            } else {
                $this->error($Config->getError());
            }
		}
	}
	
	public final function activity(){
        $list = M('activity')->where(array())->order('id')->select();
		//dump($Model->getLastSql());
		
		$this->recordList($list);
		
		$this->meta_title = '消费活动';
		$this->display();

    }
	public final function activitymodal(){
		$this->display('Score/addactivity');
	}
	public final function addactivity(){
		if(IS_POST){
			$data['all']=I('all');
			$data['amount']=I('amount');
				
			if(M('activity')->add($data)){
				$this->success('新增成功', U('score/activity'));
			} else {
				$this->error('新增失败');
			}

		}
		else{
			$this->meta_title = '新增消费活动';
			$this->display();
		}

    }
	public final function editactivity(){
		if(IS_POST){
			//$data['id']=I('id');
			$data['all']=I('all');
			$data['amount']=I('amount');
				
			if(M('activity')->where(array('id'=>I('id','','intval')))->save($data)){
				$this->success('修改成功');
			} else {
				$this->error('修改失败或没有改动');
			}

		}
    }
	public final function delactivity(){
		
		if(M('activity')->where(array('id'=>I('id','','intval')))->delete())
			$this->success('删除成功',U('score/activity'));
		else
			$this->error('删除失败');
	}
}
