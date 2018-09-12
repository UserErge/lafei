var mysql=require('mysql');
var parse=require('./kj-data/parse-calc-count.js');
var played={};

// 彩票开奖配置
exports.cp=[
	{
		title:'二分彩',
		source:'官网',
		name:'efssc',
		enable:true,
		timer:'efssc', 
		option:{
			host:"flb.hanluyan.com",
			timeout:50000,
			path: '/',
			headers:{
				"User-Agent": "Mozilla/5.0 (Windows; U; Windows NT 5.2) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.2.149.27 Safari/525.13"
			}
		},
		parse:function(str){
			try{
				return getFromFLB(str,34);
			}catch(err){
				throw('二分彩解析数据不正确');
			}
		}
	},////////////
	
];


function getFromFLB(str, type){

	str=str.substr(str.indexOf('<td class="num"></td>')-130,130).replace(/[\r\n]+/g,'');
    //console.log(str);
	var date = new Date();
	var y = date.getFullYear(),
	m = date.getMonth()+1,
	d = date.getDate(); 	

	var no1= str.substr(str.indexOf('td i=')+6, 3);
	if(no1!=parseInt(no1))
		throw('采集开奖位数错误');
	var no = y +(m>9? m:'0'+m)+(d>9?d:'0'+d) +'-' + no1;
	var data = str.substr(str.indexOf('td class=')+15, 5);
	if(data!=parseInt(data))
		throw('采集开奖位数错误');
	data= data.replace(/\B(?=(?:\d{1})+\b)/g, ',');
	//console.log(no);console.log(data);
	var timestamp = Date.parse(new Date(y + '/' +(m>9? m:'0'+m)+ '/' +(d>9?d:'0'+d)))/1000;
	var optime = timestamp+ parseInt(no1-1)*2*60;
	var time = new Date(parseInt(optime) * 1000).toLocaleString().replace(/年|月/g, "-");
	//console.log(time);

	try{
		var data={
			type: type,
			time: time,
			number: no,
			data: data
		};
		//console.log(data);
		return data;
	}catch(err){
		throw('解析数据失败');
	}
	
}

// 出错时等待 15
exports.errorSleepTime=15;

// 重启时间间隔，以小时为单位，0为不重启
//exports.restartTime=0.4;
exports.restartTime=0;

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
