$(function(){

	//{{{ 游戏快速操作部分
	// 选号按钮点击事件
	
	$('a.code').live('click', function(){
		
		var $this=$(this);
		
		if($this.is('.checked')){
			$this.removeClass('checked');
		}else{
			$this.addClass('checked');
		}
		
		// 重新计算总预投注数和金额
		//gameCalcAmount();
	});
	
	// IE,IPHONE下禁止下连接、按钮、单选框和复选框获得焦点
	$('a, :button, :radio, :checkbox').live('focus', function(){
		this.blur();
	});
	
	// 操作快速选号按钮点击事件
	$('a.action').live('click', function(){
		var $this=$(this),
		call=$this.attr('action'),
		pp=$this.parent().parent();
		$this.addClass("on").siblings(".action").removeClass("on");
		if(call && $.isFunction(call=window[call])){
			call.call(this, pp);
		}else if($this.is('.all')){
			// 全－全部选中
			$('a.code',pp).addClass('checked');
		}else if($this.is('.large')){
			// 大－选中5到9
			$('a.code.max',pp).addClass('checked');
			$('a.code.min',pp).removeClass('checked');
		}else if($this.is('.small')){
			// 小－选中0到4
			$('a.code.min',pp).addClass('checked');
			$('a.code.max',pp).removeClass('checked');
		}else if($this.is('.odd')){
			// 单－选中单数
			$('a.code.d',pp).addClass('checked');
			$('a.code.s',pp).removeClass('checked');
		}else if($this.is('.even')){
			// 双－选中双数
			$('a.code.s',pp).addClass('checked');
			$('a.code.d',pp).removeClass('checked');
		}else if($this.is('.none')){
			// 清－全不选
			$('a.code',pp).removeClass('checked');
		}
	});
	
	// 点击选号按钮时提示信息
	$('.pp .code').live('click', gameMsgAutoTip);
	$('.pp .action').live('click', gameMsgAutoTip);
	$('.pp :checkbox').live('click', gameMsgAutoTip);
	$('#unit').live('change', gameMsgAutoTip);
	$('#beishu').live('keyup', gameMsgAutoTip);//firefox
	$('#beishu').live('propertychange', gameMsgAutoTip);//ie 
	$('#beishu').live('change', gameMsgAutoTip);//ie 
	$('.surbeishu').live('click', function(){
		var newVal=parseInt($('#beishu').val())-1;
		if(newVal<1) newVal=1;
		$('#beishu').val(newVal);
		gameMsgAutoTip();
	});
	$('.addbeishu').live('click', function(){
		var newVal=parseInt($('#beishu').val())+1;
		$('#beishu').val(newVal);
		gameMsgAutoTip();
	});
	
	//录入式投注录入框键盘事件
	$('#textarea-code').live('keypress', function(event){
		//console.log(event);
		event.keyCode=event.keyCode||event.charCode;
		return !!(
			// 按Ctrl、Alt、Shift时有效
			event.ctrlKey
			|| event.altKey
			|| event.shiftKey
			
			// 回车键有效
			|| event.keyCode==13
			
			// 退格键有效
			|| event.keyCode==8
			// 空格键有效
			|| event.keyCode==32
			// 数字键有效
			|| (event.keyCode>=48
			&& event.keyCode<=57)
		);

	}).live('keyup', gameMsgAutoTip);
	
	
	$('#textarea-code').live('change', function(){
		var str=$(this).val();
		if(/[a-zA-Z]+/.test(str)){
			winjinAlert('投注号码不能含有字母字符',"alert");
			$(this).val('');
		}
	});
	
	//确认投注
	$('.postcode').unbind('click');
	$('.postcode').bind('click',gamePostCode);

	//11选5 ??
	$('.dantuo :radio').live('click', function(){
		var $dom=$(this).closest('.dantuo');
		
		if(this.value){
			$dom.next().hide().next().show();
		}else{
			$dom.next().show().next().hide();
		}
	});
	
	
	$('.dmtm a.code').live('click',function(event){
		var $this=$(this),
		$dom=$this.closest('.dmtm');
		if($('.code.checked[value=' + this.innerHTML +']', $dom).not(this).length==1){
			$this.removeClass('checked');
			winjinAlert('选择胆码不能与拖码相同',"alert");
			return false;
		}
	});
	
	$('.zhixu115 a.code').live('click',function(event){
		var $this=$(this);
		if(!$this.is('.checked')) return false;
		
		var $dom=$('.zhixu115');
		$('.code.checked[value=' + this.innerHTML +']', $dom).removeClass('checked');
		$this.addClass('checked');
	});
});


/**
 * 添加投注
 */
function gamePostCode(){
	var code=[],	// 存放投注号特有信息
	zhuiHao,		// 存放追号信息
	data={};		// 存放共同信息

	
	if(document.getElementById('amount')=='undefined' || parseFloat($('#amount').text())<0.01){
		winjinAlert('请先输入投注号码',"alert");
		return false;
	}
	
	try{
		code[0] = gameActionAddCode();
	}catch(err)
	{
		return false;
	}
	
	if(code=='undefined' || code[0]=='undefined') return false;
	
	wait();
	var actionNo=$.parseJSON($.ajax('/index.php?s=/mobile/game/getNo/type/'+game.type,{async:false}).responseText);
	destroyWait();
	if(!actionNo){
		winjinAlert('获取投注期号出错',"alert");
		return false;
	}
	
	var tipString='确定要购买第'+actionNo['actionNo']+'期彩票？';
	
	
	$('#wanjinDialog').html(tipString).dialog({
		title:'投注提示',
		resizable: false,
		width:300,
		minHeight:100,
		modal: true,
		buttons: {
		"确定购买": function() {
			$( this ).dialog( "close" );
			
			data['type']=game.type;
			data['actionNo']=actionNo.actionNo;
			data['kjTime']=actionNo.actionTime;
		
			wait();
			$.ajax('/index.php?s=/mobile/game/postCode', {
				data:{
					code:code,
					para:data,
					zhuiHao:zhuiHao
				},
				type:'post',
				dataType:'json',
				error:function(xhr, textStatus, errorThrown){
					gamePostedCode(errorThrown||textStatus);
				},
				success:function(data, textStatus, xhr){
					if(data.status==0)//失败
					{
						gamePostedCode(data.info);
					}
					else
					{
						gamePostedCode(null, data.info);
						if(data) winjinAlert(data.info,"ok");
					}
					
				},
				complete:function(xhr, textStatus){
					// 服务器运行异常
					// 尝试获取服务器抛出
					destroyWait();
					var errorMessage=xhr.getResponseHeader('X-Error-Message');
					if(errorMessage) gamePostedCode(decodeURIComponent(errorMessage));
				}
			});
	}, 
	"取消购买": function() {
		$( this ).dialog( "close" );
		return false;
	}
	}
	});//dialog end	
}


/**
 * 添加预投注
 */

function gameActionAddCode(){
	//奖金返点限制[如奖金模式在1920以下才能购买分模式(返点大于最大返点-11)]
	
	var $slider=$('#slider');	

	var obj,$game=$('#num-select .pp'),
	calcFun=$game.attr('action');
	if(calcFun && (calcFun=window[calcFun]) && (typeof calcFun=='function')){
		try{
			obj=calcFun.call($game);
			// 单笔投注注数限额
			var maxBetCount=$slider.data().betCount;
			if(maxBetCount && obj.actionNum>maxBetCount){
				winjinAlert('单笔投注注数最大不能超过'+maxBetCount+'注',"alert");
				return false;
			}
			
			if(typeof obj!='object'){
				throw('未知出错');
			}else{
				return gameAddCode(obj);
			}
		}catch(err){
			winjinAlert(err,"alert");
			throw(err);
		}
	}
}


/**
 * 添加预投注
 * code {actionNo:'12,3,4,567,8', actionNum:6}
 */
function gameAddCode(code){
	wait();
	var actionNo=$.parseJSON($.ajax('/index.php?s=/mobile/game/checkBuy',{async:false}).responseText);
	destroyWait();
	
	if(actionNo){
		winjinAlert('本期投注已截止，请下一期再投注',"alert");
		return false;
	}
	if($.isArray(code)){
		for(var i=0; i<code.length; i++) gameAddCode(code[i]);
		return;
	}
	
	var jiangjin = document.getElementById("lt_sel_dyprize").value;
	if(code.actionNum==0) throw('号码不正确');
	try{
		code=$.extend({
			
			// 反点
			fanDian: jiangjin.split('-')[1].replace("%",""),
			bonusProp: jiangjin.split('-')[0],
			// 模式
			mode: gameGetMode(),
			
			// 倍数
			beiShu: gameGetBeiShu(),

			// 预定单ID
			orderId: (new Date())-2147483647*623
		}, code);
		
		var weiShu=0, wei='',
		modeName={'2':'元', '0.2':'角', '0.02':'分'},
		amount=code.mode * code.beiShu * code.actionNum,
		$wei=$('#wei-shu'),
		weiCount=parseInt($wei.attr('length'));
		delete code.isZ6;
		
		
		if($wei.length){
			if($(':checked', $wei).length!=weiCount) throw('请选择'+weiCount+'位数！');
			$(':checked', $wei).each(function(){
				weiShu|=parseInt(this.value);
			});
		}
		code.weiShu=weiShu;
		
		if(weiShu){
			var w={16:'万', 8:'千', 4:'百', 2:'十',1:'个'}
			for(var p in w){
				if(weiShu & p) wei+=w[p];
			}
			wei+=':';
		}
		
		$('#num-select input:hidden').each(function(){
			code[$(this).attr('name')]=this.value;
		});

		delete code.undefined;
		
		$('#textarea-code').val("");
		
		$('#num-select :button.checked').removeClass('checked');
		
		code['flag'] = 1;
		return code;
	}catch(err){
		winjinAlert(err,"alert");
		throw(err);
	}
}


/**
 * 投注后置函数
 */
function gamePostedCode(err, data){
	if(err){
		if('您的可用资金不足，是否充值？'==err){
			if(window.confirm(err)) location='/index.php?s=/mobile/cash/recharge';
		}else{
			winjinAlert(err,"alert");
		}
	}else{
		//gameActionRemoveCode();
		//gameFreshOrdered();
		//reloadMemberInfo();
		//gameCalcAmount();
		//$('#game-tip-dom').text('');
		//reload();
	}
}


function wait(){
	var html = '<img src="' + 'Public/Mobile/images/game/wait.gif" />';
	
	$(html).modal({
		modal:true,
		escClose:false,
		overlayCss:{
			background:'#000'
		},
		dataCss:{
			padding:'0px',
			margin:'0px'
		}
	});
}

function destroyWait(){
	$.modal.close();
}

function gameMsgAutoTip(){
	var obj,$game=$('#num-select .pp'),
	calcFun=$game.attr('action');
	
	if(calcFun && (calcFun=window[calcFun]) && (typeof calcFun=='function')){
		try{
			obj=calcFun.call($game);
			if($.isArray(obj)){
				o={actionNum:0};
				obj.forEach(function(v,i){
					o.actionNum+=v.actionNum;
				});
				obj=o;
			}
			var amount = (gameGetMode()*gameGetBeiShu()*obj.actionNum).toFixed(2);
			var html = '已选择了<font id="count">' + obj.actionNum + '</font>注,共￥<font id="amount">' + amount + '</font>元';
			$('#count-amount').html(html);
			//$('#count').text(obj.actionNum);
			//$('#amount').text((gameGetMode()*gameGetBeiShu()*obj.actionNum).toFixed(2));
			//$('#game-tip-dom').text('共'+obj.actionNum+'注，金额'+(gameGetMode()*gameGetBeiShu()*obj.actionNum).round(2)+'元');
		}catch(err){
			//console.log(err);
			//$('#game-tip-dom').text(err);
			//$('#count').text(err);
			$('#count-amount').html(err);
		}
	}
}

//万金提示  
function winjinAlert(tips,style,minH){
	
	$( "#wanjinDialog" ).html('<span class="ui-wjicon-'+style+'"></span><b>'+tips+'</b>').dialog({
		title:'温馨提示',
		modal: true,
		resizable: false,
		width:250,
		minHeight:(minH?minH:180),
		buttons: {
		"确定": function() {$( this ).dialog( "close" );}
	   }
	});	//dialog end	
}

// 读取模式
function gameGetMode(){
	var mode = $('#unit').val();
	return parseFloat(mode||1);
}
// 读取倍数
function gameGetBeiShu(){
	var txt=$('#beishu').val();
	if(!txt) return 1;
	var re=/^[1-9][0-9]*$/;
	if(!re.test(txt)){
		throw('倍数只能为大于1正整数');
		$('#beishu').val(1);
	}
	
	if(isNaN(txt=parseInt(txt))) throw('倍数设置不正确');
	return txt;
}

/**
 * 读取陪率
 */
function gameGetPl(){
	var a = document.getElementById('lt_sel_dyprize');
	return parseFloat(a.value);
}
/**
 * 设置赔率
 */
var FANDIAN=0;
function gameSetPl(value, flag, fanDianBdw){
	
	var $dom=$('#lt_sel_dyprize');
	
	if(fanDianBdw){		
		var maxfd=parseFloat($dom.attr('maxfd'));
		var myfandian=parseFloat($dom.attr('fan-dian'));
		var prop = parseFloat(value.bonusProp);
		var base = parseFloat(value.bonusPropBase);
		var fandian = ((prop-base)/maxfd*myfandian + base).toFixed(2);
		var fan1 = fandian + "-" + "0.0%";
		var fan2 = base.toFixed(2) + "-" + myfandian.toFixed(1) + "%";

		$dom.html("<option value="+fan1+">"+fan1+"</option>");
	}else{
		var maxfd=parseFloat($dom.attr('maxfd'));
		var myfandian=parseFloat($dom.attr('fan-dian'));
		var prop = parseFloat(value.bonusProp);
		var base = parseFloat(value.bonusPropBase);
		var fandian = ((prop-base)/maxfd*myfandian + base).toFixed(2);
		var fan1 = fandian + "-" + "0.0%";
		var fan2 = base.toFixed(2) + "-" + myfandian.toFixed(1) + "%";
		//$dom.text(fandian.toFixed(2));
		if(parseFloat(myfandian)!=0)
			$dom.html("<option value="+fan1+">"+fan1+"</option>"+"<option value="+fan2+">"+fan2+"</option>");
		else
			$dom.html("<option value="+fan2+">"+fan2+"</option>");
	}
}

//{{{ 开奖相关函数
var T;
var kjTimer;
function gameKanJiangDataC(diffTime, actionNo){
	//console.log('gameKanJiangDataC');
	var $dom=$('#time');
	
	if(diffTime<=0){
		
		$('.kjhao li').text('?');
		
		var tipString='<span class="ui-wjicon-confirm"></span>当前期结束。';
		var wjDialog=$('#wanjinDialog').html(tipString).dialog({
			title:'温馨提示',
			resizable: false,
			width:250,
			minHeight:100,
			modal: true,
			buttons: {
			"确定": function() {
				$( this ).dialog( "close" );
			},
			"取消": function() {
				$( this ).dialog( "close" );				
			}
			
			}
		});//dialog end
		
		if(T) clearTimeout(T);
		
		getQiHao();
		kjTimer = setTimeout(loadKjData, 30000);
		
	}else{
	
	var m=Math.floor(diffTime % 60),
	s=(diffTime---m)/60,
	h=0;
	
	if(s<10){
		s="0"+s;
	}
	
	if(m<10){
		m="0"+m;
	}

	if(s>60){
		h=Math.floor(s/60);
		s=s-h*60;
		$dom.text((h<10?"0"+h:h)+":"+(s<10?"0"+s:s)+":"+m);
	}else{
		h=0;
		$dom.text("00:"+s+":"+m);
	}
	
	if(T) clearTimeout(T);
	T=setTimeout(gameKanJiangDataC, 1000, diffTime);	
  }
}

function loadKjData(){
	var type=game.type;
	$.ajax('/index.php?s=/mobile/game/getLastKjData/type/'+type,{
		dataType:'json',
		cache:false,
		error:function(){
			if(kjTimer) clearTimeout(kjTimer);
			kjTimer = setTimeout(loadKjData, 30000);
		},
		success:function(data, textStatus, xhr){
			
			if(!data){
				if(kjTimer) clearTimeout(kjTimer);
				kjTimer = setTimeout(loadKjData, 30000);
				
			}else{
				try{
					
					var $dom=$('#kaijiang'),$kjHaoS,$feipan,hao;
					
					if(parseInt(type)==24){ //快8
						$kjHaoS=data.data.split('|');
						hao=$kjHaoS[0].split(',');
						$feipan=$kjHaoS[1];
					}else{
						hao=data.data.split(',');
					}
					// old code
					// $('#actionNo').html(data.actionNo);
					$('#actionNo').html(data.thisNo);
					var ctype=$('.kjhao').attr('ctype');
					var times=3000;
					if(ctype=='g1'){
						$('.kjhao li').each(function(i){
							$(this).html(hao[i]);
						});
						if($dom.find('.feipan')) $dom.find('.feipan').html("快乐飞盘：<em>"+$feipan+"</em>");
					}else if(ctype=='g2'){ //快3
						$('.kjhao li').each(function(i){
							times-=500;
							setTimeout("setKjing1("+i+",'gr_ks gr_ks"+hao[i]+"')",times);
						});
					}else{
						$('.kjhao li').each(function(i){
							$(this).html(hao[i]);
						});
					}
					
					if((typeof $('#wanjinDialog').dialog("isOpen")=='object') || $('#wanjinDialog').dialog('isOpen')){
						$('#wanjinDialog').dialog('close');
					}										
				}catch(err){
					if(kjTimer) clearTimeout(kjTimer);
					kjTimer = setTimeout(loadKjData, 30000);
				}
			}
		}
	});	
}

function getQiHao(){
	$.getJSON('/index.php?s=/mobile/game/getQiHao/type/'+game.type, function(data){
		if(data && data.lastNo && data.thisNo){
			// old code
			// $('#actionNo').text(data.lastNo.actionNo);
			$('#actionNo').text(data.thisNo.actionNo);
			
			S=true;
			if(T) clearTimeout(T);
			kjTime=parseInt(data.kjdTime);
			gameKanJiangDataC(data.diffTime-kjTime, data.thisNo.actionNo);
		}
	});
}

//显示玩法组
function selectGroup(self){
	var html = '已选择了<font id="count">0</font>注,共￥<font id="amount">0.00</font>元';
	$('#count-amount').html(html);
	
	document.getElementById('groupMenu').innerHTML = self.innerHTML + '▽';
	var url = $(self).attr('data');
	
	$('#played-span').load(url,function(){
		//$('#played a[href]:first').trigger('click');
	});
	
	return false;
}
//显示分玩法
function selectPlayed(self){
	var html = '已选择了<font id="count">0</font>注,共￥<font id="amount">0.00</font>元';
	$('#count-amount').html(html);
	
	document.getElementById('playedMenu').innerHTML = self.innerHTML + '▽';
	var url = $(self).attr('data');
	
	$('#played-content').load(url,function(re){
		var a = re;
	});
	
	return false;
}
