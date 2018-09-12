<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: huajie <banhuajie@163.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 行为控制器
 * @author huajie <banhuajie@163.com>
 */

class ActionController extends AdminController {

    static protected $allow = array();

    /**
     * 用户管理首页
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index(){
    	$nickname = I('nickname');
    	$map = array('status'=>array('egt',0));
    	if(isset($nickname)){
    		if(intval($nickname) !== 0){
    			$map['uid'] = intval($nickname);
    		}else{
    			$map['nickname']  = array('like', '%'.(string)$nickname.'%');
    		}
    	}
        $list   = $this->lists('Member', $map);
        int_to_string($list);
        $this->assign('_list', $list);
        $this->meta_title = '用户信息';
        $this->display();
    }


    /**
     * 会员登录日志列表
     * @author huajie <banhuajie@163.com>
     */
    public function actionLog(){
        //获取列表数据
		$username = I('username');
    	if(isset($username)){
    		$map['username']  = array('like', '%'.(string)$username.'%');
    	}
		
		$map['uid'] = array('neq',1);//非管理员
        $list   = M('member_session')->where($map)->order('id desc')->select();
		//dump(M('member_session')->getLastSql());
        $this->recordList($list);
        $this->meta_title = '行为日志';
        $this->display();
    }
	
	/**
     * 管理员登录日志列表
     * @author huajie <banhuajie@163.com>
     */
    public function adminLgnLog(){
        //获取列表数据
		$username = I('username');
    	
		
		$Model = new \Think\Model();
		if(isset($username)){
    		$map['username']  = array('like', '%'.(string)$username.'%');
			$list = $Model->table('__MEMBER_SESSION__ s,__MEMBERS__ m')->where('s.uid=m.uid and m.admin=1 and m.username like "%'.$username.'%"')->field('s.*,m.username as username')->order('s.id desc')->select();
    	}
		else{
			$list = $Model->table('__MEMBER_SESSION__ s,__MEMBERS__ m')->where('s.uid=m.uid and m.admin=1 ')->field('s.*,m.username as username')->order('s.id desc')->select();
		}
		
		
		//dump(M('member_session')->getLastSql());
        $this->recordList($list);
        $this->meta_title = '登录日志';
        $this->display();
    }

	/**
     * 管理员操作日志
     * @author huajie <banhuajie@163.com>
     */
    public function adminlog(){
        //获取列表数据
		$username = I('username');
    	if(isset($username)){
    		$map['username']  = array('like', '%'.(string)$username.'%');
    	}

		if(I('type'))
			$map['type']=I('type');
		
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
		
        $list   = M('admin_log')->where($map)->order('id desc')->select();
		//dump(M('member_session')->getLastSql());
        $this->recordList($list);
		
		// foreach($this->adminLogType as $key=>$value)
		// {
			// $data[$key] = $value;
		// }
		//dump($data);
		$this->assign('adminLogType',$this->adminLogType);
        $this->meta_title = '操作日志';
        $this->display();
    }
    /**
     * 查看行为日志
     * @author huajie <banhuajie@163.com>
     */
	public function edit($id = 0){
		empty($id) && $this->error('参数错误！');

        $info = M('ActionLog')->field(true)->find($id);

        $this->assign('info', $info);
        $this->meta_title = '查看行为日志';
        $this->display();
    }

    /**
     * 删除日志
     * @param number $ids
     * @author huajie <banhuajie@163.com>
     */
    public function remove($ids = 0){
    	empty($ids) && $this->error('参数错误！');
    	if(is_array($ids)){
            $map['id'] = array('in', implode(',', $ids));
        }elseif (is_numeric($ids)){
            $map['id'] = $ids;
        }
        $res = M('ActionLog')->where($map)->delete();
        if($res !== false){
        	$this->success('删除成功！');
        }else {
        	$this->error('删除失败！');
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

}
