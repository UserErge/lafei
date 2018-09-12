var mysql=require('mysql');
var parse=require('./kj-data/parse-calc-count.js');
var played={};

// 彩票开奖配置
exports.cp=[
{
		title:'重庆时时彩',
		source:'官方网站',
		name:'cqssc',
		enable:true,
		timer:'cqssc_official',
		option: {
			host: 'data.shishicai.cn',
			timeout: 30000,
			path: '/cqssc/haoma/',
			headers: {'User-Agent': 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)'},
		},
		parse: function(str) {
			try {
				var exp = /重庆时时彩第(\d+\-\d+)期开奖号码\:(\d+)\,开奖时间\:([\d\:\- ]+?)\./;
				var match = str.match(exp);
				if (match) {
					var data = '';
					var max = match[2].length;
					for (var i=0;i<max;i++) data += match[2][i] + ',';
					return {
						type: 1,
						time: match[3],
						number: match[1],
						data: data.substr(0, max * 2 - 1)
					};
				}
			} catch(err) {
				throw('重庆时时彩解析数据不正确');
			}
		},
	},
	
	{
		title:'重庆时时彩',
		source:'360彩票网',
		name:'cqssc',
		enable:true,
		timer:'cqssc', 

		option:{
			host:"cp.360.cn",
			timeout:50000,
			path: '/ssccq/',
			headers:{
				"User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)"
			}
		},
		parse:function(str){
			try{
				return getFrom360CP(str,1);
			}catch(err){
				//throw('重庆时时彩解析数据不正确');
			}
		}
	},////////////

{
		title:'天津时时彩',
		source:'开彩网',
		name:'tjssc',
		enable:true,
		timer:'tjssc',
 

		option:{
			host:"c.apiplus.net",
			timeout:50000,
			path: '/a0ad7d27dcacb615/tjssc.json',
			headers:{
				"User-Agent": ""
			}
		},
		parse:function(str){
			try{
				var json = JSON.parse(str);
				var r = json.data[0];
				var data={
					type:3,
					time:r['opentime'],
					number:r['expect'].replace(/(\d{8})(\d{3})/,'$1-$2'),
					data:r['opencode']
				};
				//console.log(data);
				return data;
			}catch(err){
				throw('天津时时彩解析数据不正确');
			}
		}
	},////////////
	
	//{{{
	{
		title:'新疆时时彩',
		source:'开彩网',
		name:'xjssc',
		enable:true,
		timer:'xjssc',

		option:{
			host:"c.apiplus.net",
			timeout:50000,
			path: '/a0ad7d27dcacb615/xjssc.json',
			headers:{
				"User-Agent": ""
			}
		},
		parse:function(str){
			try{
				var json = JSON.parse(str);
				var r = json.data[0];
				var data={
					type:12,
					time:r['opentime'],
					number:r['expect'].replace(/(\d{8})\d(\d{2})/,'$1-$2'),
					data:r['opencode']
				};
				//console.log(data);
				return data;
			}catch(err){
				throw('新疆时时彩解析数据不正确');
			}
		}
	},
	
	{
		title:'韩国1.5分彩(官网)',
		source:'官网',
		name:'hgssc',
		enable:true,
		timer:'hgssc',

		option:{
			host:"www.jlotto.kr",
			timeout:50000,
			path: '/keno.aspx?method=kenoWinNoList',
			headers:{
				"User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)"
			}
		},
		
		parse:function(str){
			try {
				var exp = /<td>(\d{4}\/\d{1,2}\/\d{1,2} \d{2}:\d{2}:\d{2})<\/td><td>(\d{7})<\/td><td>((\d{1,2},){19}\d{1,2})<\/td>/;
				var match = str.match(exp);
				var units = match[3].split(',');
				var index = -1;
				var data = '';
				for (var a=0;a<5;a++) {
					var num = 0;
					for (var b=0;b<4;b++) {
						index += 1;
						num += parseInt(units[index]);
					}
					num = num.toString().substr(-1);
					data += num + ',';
				}
				data = data.substr(0, data.length - 1);
				var time = new Date();
				time.setTime((new Date(match[1])).getTime() - 3600000);
				return {
					type: 35,
					time: time,
					number: match[2],
					data: data,
				};
			} catch(e) {
				throw('韩国1.5分彩数据解析错误');
			}
		}
	},
	
	{
		title:'韩国1.5分彩(接口)',
		source:'接口',
		name:'hgsscjk',
		enable:true,
		timer:'hgsscjk',

		option:{
			host:"c.apiplus.cn",
			timeout:50000,
			path: '/a0ad7d27dcacb615/krkeno.xml',
			headers:{
				"User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)"
			}
		},
		
		parse:function(str){
			try {
				var exp = /<row expect="(\d{7})" opencode="((\d{1,2},){19}\d{1,2})" opentime="([a-z0-9\-\: ]+)"\/>/;
				var match = str.match(exp);
				var units = match[2].split(',');
				var index = -1;
				var data = '';
				for (var a=0;a<5;a++) {
					var num = 0;
					for (var b=0;b<4;b++) {
						index += 1;
						num += parseInt(units[index]);
					}
					num = num.toString().substr(-1);
					data += num + ',';
				}
				data = data.substr(0, data.length - 1);
				var time = new Date();
				time.setTime((new Date(match[3])).getTime());
				return {
					type: 35,
					time: time,
					number: match[1],
					data: data,
				};
			} catch(e) {
				throw('韩国1.5分彩数据解析错误');
			}
		}
	},
	//{{{
	{
		title:'福彩3D',
		source:'官网',
		name:'fc3d',
		enable:true,
		timer:'fc3d',

		option:{
			host:"www.500wan.com",
			timeout:50000,
			path: '/static/info/kaijiang/xml/sd/list10.xml',
			headers:{
				"User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)"
			}
		},
		
		parse:function(str){
			try{
				str=str.substr(0,300);
				var m;
				var reg=/<row expect="(\d+?)" opencode="([\d\,]+?)" opentime="([\d\:\- ]+?)" trycode="[\d\,]*?" tryinfo="" \/>/;
                                        
				if(m=str.match(reg)){
					return {
						type:9,
						time:m[3],
						number:m[1],
						data:m[2]
					};
				}
			}catch(err){
				throw('福彩3D解析数据不正确');
			}
		}
	},
	
	{
		title:'排列3',
		source:'官网',
		name:'pai3',
		enable:true,
		timer:'pai3',

		option:{
			host:"www.500wan.com",
			timeout:50000,
			path: '/static/info/kaijiang/xml/pls/list10.xml',
			headers:{
				"User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)"
			}
		},
		
		parse:function(str){
			try{
				str=str.substr(0,300);
				var m;	 
				var reg=/<row expect="(\d+?)" opencode="([\d\,]+?)" opentime="([\d\:\- ]+?)"/;
				if(m=str.match(reg)){
					return {
						type:10,
						time:m[3],
						number:20+m[1],
						data:m[2]
					};
				}
			}catch(err){
				throw('排3解析数据不正确');
			}
		}
	},
	//}}}
	
	
	{
		title:'广东11选5',
		source:'官网',
		name:'gd11x5',
		enable:true,
		timer:'gd11x5',

 

		option:{
			host:"cp.360.cn",
			timeout:50000,
			path: '/gd11/',
			headers:{
				"User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)"
			}
		},
		parse:function(str){
			try{
				return getFrom360CP(str,6);
			}catch(err){
				//throw('广东11选5解析数据不正确');
			}
		}
	},////

	//{{{
	{
		title:'GD115',
		source:'CLE',
		name:'gd11x5',
		enable:true,
		timer:'gd11x5',

		option:{
			host:"www.cailele.com",
			timeout:30000,
			path: '/static/gd11x5/newlyopenlist.xml',
			headers:{
				"User-Agent": "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/28.0.1271.64 Safari/537.11"
			}
		},
		
		parse:function(str){
			try{
				//return getFromCaileWeb(str,6);
				str=str.substr(0,200);
				var reg=/<row expect="(\d+?)" opencode="([\d\,]+?)" opentime="([\d\:\- ]+?)"/; 
				//<row expect="2013071984" opencode="04,11,05,03,07" opentime="2013-07-19 23:00:15"/>
				var m;
	
				if(m=str.match(reg)){
					return {
						type:6,
						time:m[3],
						number:m[1].replace(/^(\d{8})(\d{2})$/, '$1-0$2'),
						data:m[2]
					};
				}					
			}catch(err){
				throw('广东11选5解析数据不正确');
			}
		}
	},
	//}}}
	
	//{{{
	{
		title:'江西多乐彩',
		source:'官网',
		name:'jx11x5',
		enable:true,
		timer:'jx11x5',

		option:{
			host:"www.cailele.com",
			timeout:30000,
			path: '/static/jxdlc/newlyopenlist.xml',
			headers:{
				"User-Agent": "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/28.0.1271.64 Safari/537.11"
			}
		},
		
		parse:function(str){
			try{
				//return getFromCaileWeb(str,6);
				str=str.substr(0,200);
				var reg=/<row expect="(\d+?)" opencode="([\d\,]+?)" opentime="([\d\:\- ]+?)"/; 
				//<row expect="2013071984" opencode="04,11,05,03,07" opentime="2013-07-19 23:00:15"/>
				var m;
	
				if(m=str.match(reg)){
					return {
						type:16,
						time:m[3],
						number:m[1].replace(/^(\d{8})(\d{2})$/, '$1-0$2'),
						data:m[2]
					};
				}					
			}catch(err){
				throw('广东11选5解析数据不正确');
			}
		}
	},
	//}}}
	
	{
		title:'江西多乐彩',
		source:'官网',
		name:'jx11x5',
		enable:true,
		timer:'jx11x5',
 

		option:{
			host:"cp.360.cn",
			timeout:50000,
			path: '/dlcjx/',
			headers:{
				"User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)"
			}
		},
		parse:function(str){
			try{
				return getFrom360CP(str,16);
			}catch(err){
				//throw('江西多乐彩解析数据不正确');
			}
		}
	},////////////
	
	//{{{
	{
		title:'北京PK10',
		source:'官网',
		name:'bjpk10',
		enable:true,
		timer:'bjpk10',

		option:{

			host:"www.bwlc.net",
			timeout:50000,
			path: '/bulletin/trax.html',
			headers:{
				"User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)"
			}
		},
		
		parse:function(str){
			try{
				return getFromPK10(str,20);
			}catch(err){
				throw('解析数据不正确');
			}
		}
	},//(((
	
	
	
	//{{{
	{
		title:'北京快乐8',
		source:'官网',
		name:'bjk8',
		enable:true,
		timer:'bjk8',

		option:{

			host:"www.bwlc.net",
			timeout:50000,
			path: '/bulletin/keno.html',
			headers:{
				"User-Agent": "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)"
			}
		},
		
		parse:function(str){
			try{
				return getFromK8(str,24);
			}catch(err){
				throw('解析数据不正确');
			}
		}
	},//(((

	
		
	{
		title:'五分彩',
		source:'官网',
		name:'qtllc',
		enable:true,
		timer:'qtllc', 
		option:{
			host:"127.0.0.3",
			timeout:50000,
			path: '/index.php?s=/home/wufencai/info5',
			headers:{
				"User-Agent": "Mozilla/5.0 (Windows; U; Windows NT 5.2) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.2.149.27 Safari/525.13"
			}
		},
		parse:function(str){
			try{
				return getFromWANJINYULE(str,14);
			}catch(err){
				throw('5分彩解析数据不正确');
			}
		}
	},////////////
	
				{
		title:'二分彩',
		source:'官网',
		name:'efssc',
		enable:true,
		timer:'efssc', 
		option:{
			host:"127.0.0.3",
			timeout:50000,
			path: '/index.php?s=/home/wufencai/info2',
			headers:{
				"User-Agent": "Mozilla/5.0 (Windows; U; Windows NT 5.2) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.2.149.27 Safari/525.13"
			}
		},
		parse:function(str){
			try{
				return getFromWANJINYULE(str,34);
			}catch(err){
				throw('二分彩解析数据不正确');
			}
		}
	},////////////
	
	
		{
		title:'分分彩',
		source:'官网',
		name:'ffc',
		enable:true,
		timer:'ffc', 
		option:{
			host:"127.0.0.3",
			timeout:50000,
			path: '/index.php?s=/home/wufencai/info1',
			headers:{
				"User-Agent": "Mozilla/5.0 (Windows; U; Windows NT 5.2) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.2.149.27 Safari/525.13"
			}
		},
		parse:function(str){
			try{
				return getFromWANJINYULE(str,5);
			}catch(err){
				throw('分分彩解析数据不正确');
			}
		}
	},////////////
	

	
];

// 出错时等待 15
exports.errorSleepTime=5;

// 重启时间间隔，以小时为单位，0为不重启
//exports.restartTime=0.4;
//exports.restartTime={0};
exports.restartTime = {
	0: 300, //采集互联网进程5分钟重启一次
	1: 60, //采集本机进程1分钟重启一次
};

exports.submit={

	host:'localhost',
	path:'/wjadmin.php/dataSource/kj'
}

exports.dbinfo={
	host:'localhost',
	user:'root',
	password:'root',
	database:'lafei'

}

global.log=function(log){
	var date=new Date();
	console.log('['+date.toDateString() +' '+ date.toLocaleTimeString()+'] '+log)
}
function getFromXJFLCPWeb(str, type){
	str=str.substr(str.indexOf('<strong>时时彩</strong>'), 300).replace(/[\r\n]+/g,''); 
	
	if(!str) throw new Error('数据不正确');
	var number = str.substr(str.indexOf('第')+1,10);
	
	var myDate = new Date();
	var year = myDate.getFullYear();       //年   
    var month = myDate.getMonth() + 1;     //月   
    var day = myDate.getDate();            //日
	if(month < 10) month="0"+month;
	if(day < 10) day="0"+day;
	var mytime=year + "-" + month + "-" + day + " " +myDate.toLocaleTimeString();
	
	str=str.substr(str.indexOf('ballWrap')+10, 200).replace(/[^0-9]/ig,"");
	//console.log(str);    
	var data= str.split('').join(',');
	//console.log('期号：%s，开奖时间：%s，开奖数据：%s', number, mytime, data);
	
	try{
		var data={
			type:type,
			time:mytime,
			number:number.replace(/^(\d{8})(\d{2})$/, '$1-$2'),
			data:data
		};
		//console.log(data);
		return data;
	}catch(err){
		throw('解析数据失败');
	}
}


function getFromCaileleWeb(str, type, slen){
	if(!slen) slen=580;
	str=str.substr(str.indexOf('<p class="cz_name_period">')+26,slen);
	var mynumber = str.substr(0,10);
	mynumber = mynumber.substr(0,8) + "-0" + mynumber.substr(8);
	var mytime = str.substr(str.indexOf('<span>')+6,16);
	
	var text = str.substr(str.indexOf('red_ball')+10,200);
	text = text.replace(/[^0-9]/ig,""); 
	//console.log(text);
	
	//var mynumber = str
	var reg=/<td.*?>(\d+)<\/td>[\s\S]*?<td.*?>([\d\- \:]+)<\/td>[\s\S]*?<td.*?>((?:[\s\S]*?<span class="red_ball">\d+<\/span>){3,5})\s*<\/td>/,
	match=str.match(reg);
	
	
		
	try{
		var data={
			type:type,
			time:mytime,
			number:mynumber,
			data:text.substr(0,2) + ","+text.substr(2,2) + ","+text.substr(4,2) + ","+text.substr(6,2) + ","+text.substr(8,2)
		}
		
		
		
		//console.log(data);
		return data;
	}catch(err){
		throw('解析数据失败');
	}
}

function getFrom360CP(str, type){

	str=str.substr(str.indexOf('<em class="red" id="open_issue">'),380);
	//console.log(str);
	var reg=/[\s\S]*?(\d+)<\/em>[\s\S].*?<ul id="open_code_list">((?:[\s\S]*?<li class=".*?">\d+<\/li>){3,5})[\s\S]*?<\/ul>/,
	match=str.match(reg);
	var myDate = new Date();
	var year = myDate.getFullYear();       //年   
    var month = myDate.getMonth() + 1;     //月   
    var day = myDate.getDate();            //日
	if(month < 10) month="0"+month;
	if(day < 10) day="0"+day;
	var mytime=year + "-" + month + "-" + day + " " +myDate.toLocaleTimeString();
	//console.log(match);
	if(match.length>1){
		if(match[1].length==6) match[1]='2016'+match[1].replace(/(\d{4})(\d{2})/,'$1-0$2');
		if(match[1].length==7) match[1]='2016'+match[1].replace(/(\d{4})(\d{3})/,'$1-$2');
		if(match[1].length==8) match[1]='20'+match[1].replace(/(\d{6})(\d{2})/,'$1-0$2');
		if(match[1].length==9) match[1]='20'+match[1].replace(/(\d{6})(\d{2})/,'$1-$2');
		if(match[1].length==10) match[1]=match[1].replace(/(\d{8})(\d{2})/,'$1-0$2');
		var mynumber=match[1].replace(/(\d{8})(\d{3})/,'$1-$2');
		
		try{
			var data={
				type:type,
				time:mytime,
				number:mynumber
			}
			
			reg=/<li class=".*?">(\d+)<\/li>/g;
			data.data=match[2].match(reg).map(function(v){
				var reg=/<li class=".*?">(\d+)<\/li>/;
				return v.match(reg)[1];
			}).join(',');
			
			//console.log(data);
			return data;
		}catch(err){
			throw('解析数据失败');
		}
	}
}

function getFromPK10(str, type){

	str=str.substr(str.indexOf('<div class="lott_cont">'),350).replace(/[\r\n]+/g,'');
    //console.log(str);
	var reg=/<tr class=".*?">[\s\S]*?<td>(\d+)<\/td>[\s\S]*?<td>(.*)<\/td>[\s\S]*?<td>([\d\:\- ]+?)<\/td>[\s\S]*?<\/tr>/,
	match=str.match(reg);
	if(!match) throw new Error('数据不正确');
	//console.log(match);
	try{
		var data={
			type:type,
			time:match[3],
			number:match[1],
			data:match[2]
		};
		//console.log(data);
		return data;
	}catch(err){
		throw('解析数据失败');
	}
	
}

function getFromK8(str, type){

	str=str.substr(str.indexOf('<div class="lott_cont">'),450).replace(/[\r\n]+/g,'');
    //console.log(str);
	var reg=/<tr class=".*?">[\s\S]*?<td>(\d+)<\/td>[\s\S]*?<td>(.*)<\/td>[\s\S]*?<td>(.*)<\/td>[\s\S]*?<td>([\d\:\- ]+?)<\/td>[\s\S]*?<\/tr>/,
	match=str.match(reg);
	if(!match) throw new Error('数据不正确');
	//console.log(match);
	try{
		var data={
			type:type,
			time:match[4],
			number:match[1],
			data:match[2]+'|'+match[3]
		};
		//console.log(data);
		return data;
	}catch(err){
		throw('解析数据失败');
	}
	
}


function getFromCJCPWeb(str, type){

	//console.log(str);
	str=str.substr(str.indexOf('<table class="qgkj_table">'),1200);
	
	//console.log(str);
	
	var reg=/<tr>[\s\S]*?<td class=".*">(\d+).*?<\/td>[\s\S]*?<td class=".*">([\d\- \:]+)<\/td>[\s\S]*?<td class=".*">((?:[\s\S]*?<input type="button" value="\d+" class=".*?" \/>){3,5})[\s\S]*?<\/td>/,
	match=str.match(reg);
	
	//console.log(match);
	
	if(!match) throw new Error('数据不正确');
	try{
		var data={
			type:type,
			time:match[2],
			number:match[1].replace(/(\d{8})(\d{2})/,'$1-0$2')
		}
		
		reg=/<input type="button" value="(\d+)" class=".*?" \/>/g;
		data.data=match[3].match(reg).map(function(v){
			var reg=/<input type="button" value="(\d+)" class=".*?" \/>/;
			return v.match(reg)[1];
		}).join(',');
		
		//console.log(data);
		return data;
	}catch(err){
		throw('解析数据失败');
	}
	
}

var wfFullData;
var efFullData;
var wfGaiLv=100;
var wfNo='0';
var efNo='0';
//万金娱乐自主研发五分彩开奖
function getFromWANJINYULE(str, type){
	
	try{
		var client=mysql.createClient(exports.dbinfo);
	}catch(err){
		throw('连接数据库失败');
	}			

	var jd = JSON.parse(str);
	
	var match = [];
	match[1] = jd.actionNo;
	match[2] = jd.wufencai;
	match[3] = jd.actionTime;
	match[4] = 1;

	wfGaiLv=match[2];

	console.log("读取盈亏率成功："+wfGaiLv);
	if( wfNo!=match[1] && efNo!=match[1]){
		sql="select * from gygy_bets where type=? and actionNo=? and isDelete=0 and lotteryNo=''";
		client.query(sql, [type, match[1]], function(err, bets){
			if(err){
				log("读取投注出错："+err);
			}else{					
				var myDate = new Date();
				var year = myDate.getFullYear();       //年   
				var month = myDate.getMonth() + 1;     //月   
				var day = myDate.getDate();            //日
				if(month < 10) month="0"+month;
				if(day < 10) day="0"+day;
				var mytime=year + "-" + month + "-" + day + " " +myDate.toLocaleTimeString(); 
				var mydata=GetRandomNum(0,9)+','+GetRandomNum(0,9)+','+GetRandomNum(0,9)+','+GetRandomNum(0,9)+','+GetRandomNum(0,9);
				var yingLv = GetRandomNum(0,100);
				
				//控制盈亏													
				for(var go=0;go<1000000;go++){
					if(yingLv>wfGaiLv){
						go=1000000;
						console.log("概率超出必赢范围，随机开奖");
					}else{
						go=go+1;							
						var all=0;
						var win=0;
						bets.forEach(function(bet){
							var fun;
									
							try{
								fun=parse[global.played[bet.playedId]];
								if(typeof fun!='function') throw new Error('算法不是可用的函数');
							}catch(err){
								log('计算玩法[%f]中奖号码算法不可用：%s'.format(bet.playedId, err.message));
								return;
							}
									
							try{
								var zjCount=fun(bet.actionData, mydata, bet.weiShu)||0;
							}catch(err){
								log('计算中奖号码时出错：'+err);
								return;
							}
							win+=bet.bonusProp * zjCount * bet.beiShu * bet.mode/2;
							all+=bet.mode*bet.beiShu*bet.actionNum; 
						});
						console.log("投注总额：盈亏总额----"+all + "：" +win);
						if(all>win || bets.length==0){
							go=1000000;											
						}
						else
							mydata=GetRandomNum(0,9)+','+GetRandomNum(0,9)+','+GetRandomNum(0,9)+','+GetRandomNum(0,9)+','+GetRandomNum(0,9);
					}
					
					if(!match) throw new Error('数据不正确');
					if(parseInt(match[4])==1){
						try{
								var data={
									type:type,
									time:mytime,
									number:match[1],
									data:mydata
								}
								
								if(type==14)
								{
									wfFullData=data;
									wfNo=match[1];
								}
								else
								{
									efFullData=data;
									efNo=match[1];
								}
							}
							catch(err){
								throw('解析数据失败');
						}
				   }
				}		
			} 				
		});
	}
	
	client.end();
	if(type==14)
	{
		return wfFullData;
	}
	else
	{
		return efFullData;
	}
	
}

//随机数 GetRandomNum(0,9);   
function GetRandomNum(Min,Max)
{   
	var Range = Max - Min;   
	var Rand = Math.random();   
	return(Min + Math.round(Rand * Range));   
}    
