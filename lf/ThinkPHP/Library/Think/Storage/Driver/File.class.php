<?php
// +----------------------------------------------------------------------
// | TOPThink [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://topthink.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Think\Storage\Driver;
use Think\Storage;
// 本地文件写入存储类
class File extends Storage{

    private $contents=array();

    /**
     * 架构函数
     * @access public
     */
    public function __construct() {
    }

    /**
     * 文件内容读取
     * @access public
     * @param string $filename  文件名
     * @return string     
     */
    public function read($filename,$type=''){
        return $this->get($filename,'content',$type);
    }

    /**
     * 文件写入
     * @access public
     * @param string $filename  文件名
     * @param string $content  文件内容
     * @return boolean         
     */
    public function put($filename,$content,$type=''){
        $dir         =  dirname($filename);
        if(!is_dir($dir))
            mkdir($dir,0755,true);
        if(false === file_put_contents($filename,$content)){
            E(L('_STORAGE_WRITE_ERROR_').':'.$filename);
        }else{
            $this->contents[$filename]=$content;
            return true;
        }
    }

    /**
     * 文件追加写入
     * @access public
     * @param string $filename  文件名
     * @param string $content  追加的文件内容
     * @return boolean        
     */
    public function append($filename,$content,$type=''){
        if(is_file($filename)){
            $content =  $this->read($filename,$type).$content;
        }
        return $this->put($filename,$content,$type);
    }

    /**
     * 加载文件
     * @access public
     * @param string $filename  文件名
     * @param array $vars  传入变量
     * @return void        
     */
    public function load($filename,$vars=null,$type=''){
        if(!is_null($vars))
            extract($vars, EXTR_OVERWRITE);
        include $filename;
    }

    /**
     * 文件是否存在
     * @access public
     * @param string $filename  文件名
     * @return boolean     
     */
    public function has($filename,$type=''){
        return file_exists($filename);
    }

    /**
     * 文件删除
     * @access public
     * @param string $filename  文件名
     * @return boolean     
     */
    public function unlink($filename,$type=''){
        unset($this->contents[$filename]);
        return unlink($filename);
    }

    /**
     * 读取文件信息
     * @access public
     * @param string $filename  文件名
     * @param string $name  信息名 mtime或者content
     * @return boolean     
     */
    public function get($filename,$name,$type=''){
        if(!isset($this->contents[$filename])){
            if(!is_file($filename)) return false;
           $this->contents[$filename]=file_get_contents($filename);
        }
        $content=$this->contents[$filename];
        $info   =   array(
            'mtime'     =>  filemtime($filename),
            'content'   =>  $content
        );
        return $info[$name];
    }
	
	public function iff($b,$str1,$str2=''){
		if($b)
			return $str1;
		else
			return $str2;		
	}
	
	public static final function ifs(){
		$args=func_get_args();
		for($i=0; $i<count($args); $i++){
			if($args[$i]==='0' || $args[$i]) return $args[$i];
		}
	}
	
	public static function CsubStr($str,$start,$len,$suffix='...'){
		preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $str, $info);
		$len*=2;
		$i=0;
		$tmpstr = '';
		while($i < $len && array_key_exists($start,$info[0])) {
			if (strlen($info[0][$start]) > 1) {
				$i+=2;
				if ($i <= $len)  $tmpstr .= $info[0][$start];
			}else {
				if (++$i <= $len)  $tmpstr .= $info[0][$start];
			}
			$start++;
		}
		return array_key_exists($start,$info[0]) ? $tmpstr.=$suffix : $tmpstr;
	}
	
	public final function shortUrl($url){
		$key = '2270845191';
		$r = file_get_contents('http://api.t.sina.com.cn/short_url/shorten.json?source=' .$key. '&url_long='.$url);
		if($r) {
			$items = json_decode($r);
			foreach($items as $item) {
				return $item->url_short;
			}
		}
	}
}
