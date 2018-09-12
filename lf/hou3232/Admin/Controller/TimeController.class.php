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
class TimeController extends AdminController {

    static protected $allow = array( 'verify');

    /**
     * 后台首页
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index(){
		/* 查询条件初始化 */
    	$map = array();
		
		if(!I('type'))
			$type = 1;
		else
			$type=I('type');
		$this->assign('type',$type);
		
		$types = M('type')->where(array('enable'=>1))->select();
		$this->assign('types',$types);
		
		$Model = M('data_time');
		$list = $Model->alias('a')->field('a.*,t.title')->join('__TYPE__ t ON a.type = t.id and t.id='.intval($type))->order('actionNo')->select();
		//dump($Model->getLastSql());
		
		$this->recordList($list);
        $this->meta_title = '时间管理';
        $this->display();
    }
	/**
     * 编辑配置
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function update(){
        if(IS_POST){
			
            $Config = D('data_time');
            $data = $Config->create();
            if($data){
                if($Config->save()){
					//记录行为
					$this->addLog(22 , $data['id'], $data['actionTime']);
                    $this->success('更新成功');
                } else {
                    $this->error('更新失败或数据未改动');
                }
            } else {
                $this->error($Config->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('data_time')->field(true)->find($id);

            if(false === $info){
                $this->error('获取配置信息错误');
            }
            $this->assign('info', $info);
            $this->meta_title = '编辑配置';
            $this->display();
        }
    }

}
