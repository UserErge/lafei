function getFromShishicaiWeb(str, type){
	var reg=/var\s*listIssue\s*\=\s*\[(.*?)\,{/,
	match=str.match(reg);
	
	if(!match) throw new Error('数据不正确');
	
	// 解析数据
	try{
		var data=JSON.parse(match[1]);

		data={
			type:type,
			time:data.BonusTime,
			number:data.IssueNumber,
			data:data.BonusNumberString.substr(0,9)
		}
		//console.log(data);
		return data;
		
	}catch(err){
		throw('解析数据失败：'+match[1]);
	}
}

exports.cqssc=function(str){
	//return getFromShishicaiWeb(str,1);
	try{
		//console.log(str);
		var data=JSON.parse(str).Table[0];
		//d={};
		//d.type=1;
		//d.time=data.AwardTime;
		//d.number=data.IssueNum.replace(/^(\d{8})(\d{3})$/, "$1-$2");
		//d.data=data.Result.split(' ').join(',');
		//return d;
		return {
			type:1,
			time:data.AwardTime,
			number:data.IssueNum.replace(/^(\d{8})(\d{3})$/, "$1-$2"),
			data:data.Result.split(' ').join(',')
		};
	}catch(err){
		throw('解析数据不正确');
	}
}
exports.xjssc=function(str){
	return getFromShishicaiWeb(str,12);
}
exports.tjssc=function(str){
	return getFromShishicaiWeb(str,4);
}
exports.jxssc=function(str){
	return getFromShishicaiWeb(str,3);
}