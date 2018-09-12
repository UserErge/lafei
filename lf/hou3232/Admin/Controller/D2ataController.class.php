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
class DataController extends AdminController {

    static protected $allow = array( 'verify');

    /**
     * 后台首页
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index(){
		if(!I('type'))
			$type = 1;
		else
			$type=I('type');
		$this->assign('type',$type);
		
		// 默认取今天的数据
		if(I('date')){
			$date=strtotime(I('date'));
		}else{
			$date=strtotime('00:00');
		}
		$this->assign('date',$date);
		
		$types = M('type')->where(array('enable'=>1))->select();
		$this->assign('types',$types);
		
		$kjData=array();
		$i=0;
		if(I('number'))
		{
			$data = M('data')->where(array('type'=>$type,'number'=>I('number')))->find();
			$bet = M('bets')->field('sum(mode * beiShu * actionNum) betAmount,sum(bonus) zjAmount, sum(fanDianAmount) fanDianAmount')->where(array('type'=>$type,'isDelete'=>0,'actionNo'=>$data['number']))->select();
			
			$kjData[$i]['actionNo']=I('number');
			$kjData[$i]['actionTime']=$data['time']?date('Y-m-d H:i:s',$data['time']):'--';
			$kjData[$i]['betAmount'] = $bet[0]['betAmount'];
			$kjData[$i]['zjAmount'] = $bet[0]['zjAmount'];
			$kjData[$i]['fanDianAmount'] = $bet[0]['fanDianAmount'];
			
			if($data['data'])
				$kjData[$i]['data']=$data['data'];
			else
				$kjData[$i]['data']='--';
		}
		else
		{
			$Model = M('data_time');
			$list = $Model->where(array('type'=>intval($type)))->order('actionNo')->select();
			//dump($Model->getLastSql());
			
			foreach($list as $var)
			{
				if($type==1){
					// 重庆彩特殊处理
					$number=1000+$var['actionNo'];
					if($var['actionNo']==120){ 
						$number=date('Ymd-', strtotime(date('Y-m-d',$date - 1*24*60*60))).substr($number,1);
					}else{
						$number=date('Ymd-', $date).substr($number,1);
							
					}
					$data = M('data')->where(array('type'=>$type,'number'=>$number))->find();
				}else if($type==34){
					// 新疆彩特殊处理
					$number=1000+$var['actionNo'];
					if($var['actionNo']>719){ 
						$number=date('Ymd-', strtotime(date('Y-m-d',$date - 1*24*60*60))).substr($number,1);
					}else{
						$number=date('Ymd-', $date).substr($number,1);						
					}
					$data = M('data')->where(array('type'=>$type,'number'=>$number))->find();
					
				}else if($type==12){
					// 新疆彩特殊处理
					$number=100+$var['actionNo'];
					if($var['actionNo']>83){ 
						$number=date('Ymd-', strtotime(date('Y-m-d',$date - 1*24*60*60))).substr($number,1);
					}else{
						$number=date('Ymd-', $date).substr($number,1);						
					}
					$data = M('data')->where(array('type'=>$type,'number'=>$number))->find();
					
				}else if($type==9 || $type==10){
					// 福彩3D
					$number=date('Yz', $date)-7;
					$number=substr($number,0,4).substr(substr($number,4)+1000,1);
					
					$data = M('data')->where(array('type'=>$type,'number'=>$number))->find();
					
				}else if($type==20){
					// PK10
					$number = 179*(strtotime(date('Y-m-d', $date))-strtotime('2007-11-11'))/3600/24+$var['actionNo']-14;
					$data = M('data')->where(array('type'=>$type,'number'=>$number))->find();
					
				}else if($type==24){
					// 快8
					$number = 179*(strtotime(date('Y-m-d', $date))-strtotime('2004-09-19'))/3600/24+$var['actionNo']-77;;
					$data = M('data')->where(array('type'=>$type,'number'=>$number))->find();
					
				}else if($type==11){
					// 时时乐
					$number=100+$var['actionNo'];
					$number=date('Ymd-', $date).substr($number,1);
					$data = M('data')->where(array('type'=>$type,'number'=>$number))->find();
					
				}else{
					//$data=$this->getRow($sql . 'time='. strtotime($dateString . $var['actionTime']));
					$number=1000+$var['actionNo'];
					$number=date('Ymd-', $date).substr($number,1);
					$data = M('data')->where(array('type'=>$type,'number'=>$number))->find();
				}
				
				$bet = M('bets')->field('sum(mode * beiShu * actionNum) betAmount,sum(bonus) zjAmount, sum(fanDianAmount) fanDianAmount')->where(array('type'=>$type,'isDelete'=>0,'actionNo'=>$data['number']))->select();
				
				$kjData[$i]=$var;
				$kjData[$i]['actionNo'] = $number;
				$kjData[$i]['actionTime']=date('Y-m-d ',$date).$kjData[$i]['actionTime'];
				$kjData[$i]['betAmount'] = $bet[0]['betAmount'];
				$kjData[$i]['zjAmount'] = $bet[0]['zjAmount'];
				$kjData[$i]['fanDianAmount'] = $bet[0]['fanDianAmount'];
				
				if($data['data'])
					$kjData[$i]['data']=$data['data'];
				else
					$kjData[$i]['data']='--';
				$i++;
			}
		}
		
		//dump($kjData);
		
		$this->recordList($kjData);
        $this->meta_title = '开奖数据';
        $this->display();
    }
	
	private $encrypt_key='I/On?u8v(^PQGlCMKGLt#2R&ylR06nc315/r`k-(b94@bV<sOo#u:1@BW27,N^TQOlkRvhuu,U5mUhpfWnfZu$>-=rx)rNlSH-Oc,*oUGI*a/X`YFmgM-Bre(wfFf5Gx*qE_L@~pCtr0BOfCO#n=0w:+@l,k$y5r75Q/)~.z%&kTZDjR0OM7*xlnyyfO1JL&VtRzYrF(a~2(yz1Fz:ZB#uAR;pt`4Wg;*$+G<EWhZ~o5G$8,u=PHsGr@NI*N%sdF';	// 256位随便密码
	
	private $node_access='http://localhost:8801';	// node访问基本路径
	
	//添加开奖号码
	public final function add(){
		if(IS_POST){
			$para=I('post.');
			$para['key']=$this->encrypt_key;
			
			$url=$this->node_access. '/data/add';
			
			
			if($data = M('data')->where(array('type'=>$para['type'],'number'=>$para['number']))->find()) 
				$this->error('开奖号已经存在，不需要手动添加');
			
			$this->addLog(17, $para['type'], '[期号:'.$para['number'].']'.'[开奖号码：'.$para['data'].']');
			
			$this->http_post($url, $para);
			$this->success('添加开奖结果成功',U('Data/index?type='.I('type')));
		}
		else{
			$this->display();
		}
	}
	
	//手动派奖
	public final function pai_jiang(){
		$para=I('get.');
		$para['key']=$this->encrypt_key;

		$url=$this->node_access. '/data/kj';
		//$this->addLog(171, $para['type'], '[期号:'.$para['number'].']'.'[开奖号码：'.$para['data'].']');
		$this->addLog(171, $para['type'], '[期号:'.$para['number'].']'.'[开奖号码：'.$para['data'].']');
		$this->http_post($url, $para);
		$this->success('派奖成功',U('Data/index?type='.I('type')));
	}
	
	private function http_post($url, $data) {
		$data_url = http_build_query ($data);
		$data_len = strlen ($data_url);
		
		//\Think\Log::write('xieff	'.$data_url);
		return file_get_contents ($url, false, stream_context_create (array ('http'=>array ('method'=>'POST'
				, 'header'=>"Connection: close\r\nContent-Length: $data_len\r\n"
				, 'content'=>$data_url
				))));
	}

}
