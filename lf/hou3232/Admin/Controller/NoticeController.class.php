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
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class NoticeController extends AdminController {

    static protected $allow = array( 'verify');

    /**
     * 后台首页
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index(){
		/* 查询条件初始化 */
    	$map = array();
		
		
		$list = M('content')->where(array())->order('id desc')->select();
		
		
		$this->recordList($list);
        $this->meta_title = '系统公告';
        $this->display();
    }
	
	public function add(){
		if(IS_POST){
			$data['title'] = I('title');
			$data['content'] = I('content');
			$data['addTime'] = time();
			if(M('content')->add($data))
				$this->success('新增成功',U('notice/index'));
			else
				$this->error('新增失败');
		}
		else{
			$this->meta_title = '新增公告';
			$this->display();
		}
	}
	/**
     * 编辑配置
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function update(){
        if(IS_POST){			
			$data['id'] = I('id','','intval');
            $data['title'] = I('title');
			$data['content'] = I('content');
			$data['enable'] = I('enable','1','intval');
			$data['addTime'] = time();
			if(M('content')->save($data))
				$this->success('更新成功',U('notice/index'));
			else
				$this->error('更新失败');
        } else {			
            $content = M('content')->find(I('id','','intval'));            
            $this->assign('content', $content);
            $this->meta_title = '编辑公告';
            $this->display('Notice/add');
        }
    }
	
	public final function delete(){
		if(M('content')->where(array('id'=>I('id','','intval')))->delete())
			$this->success('删除成功',U('notice/index'));
		else
			$this->error('删除失败');
	}

}
