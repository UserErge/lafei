$(function(){

	//{{{ 游戏快速操作部分
	// 选号按钮点击事件
	$('input.code').live('click', function(){
		var $this=$(this);
		
		if($this.is('.checked')){
			$this.removeClass('checked');
		}else{
			$this.addClass('checked');
		}
	});
	// IE,IPHONE下禁止下连接、按钮、单选框和复选框获得焦点
	$('a, :button, :radio, :checkbox').live('focus', function(){
		this.blur();
	});
	
	// 操作快速选号按钮点击事件
	$('input.action').live('click', function(){
		var $this=$(this),
		call=$this.attr('action'),
		pp=$this.parent();
		$this.addClass("on").siblings(".action").removeClass("on");
		if(call && $.isFunction(call=window[call])){
			call.call(this, pp);
		}else if($this.is('.all')){
			// 全－全部选中
			$('input.code',pp).addClass('checked');
		}else if($this.is('.large')){
			// 大－选中5到9
			$('input.code.max',pp).addClass('checked');
			$('input.code.min',pp).removeClass('checked');
		}else if($this.is('.small')){
			// 小－选中0到4
			$('input.code.min',pp).addClass('checked');
			$('input.code.max',pp).removeClass('checked');
		}else if($this.is('.odd')){
			// 单－选中单数
			$('input.code.d',pp).addClass('checked');
			$('input.code.s',pp).removeClass('checked');
		}else if($this.is('.even')){
			// 双－选中双数
			$('input.code.s',pp).addClass('checked');
			$('input.code.d',pp).removeClass('checked');
		}else if($this.is('.none')){
			// 清－全不选
			$('input.code',pp).removeClass('checked');
		}
	});
	
	// 点击选号按钮时提示信息
	$('.pp :button').live('click', gameMsgAutoTip);
	$('.pp :checkbox').live('click', gameMsgAutoTip);
	$('#lt_sel_modes').live('change', gameMsgAutoTip);
	$('#lt_sel_times').live('input', gameMsgAutoTip);
	$('#beishu').live('keyup', gameMsgAutoTip);//firefox
	$('#beishu').live('propertychange', gameMsgAutoTip);//ie 
	$('#reducetime').live('click', function(){
		var newVal=parseInt($('#lt_sel_times').val())-1;
		if(newVal<1) newVal=1;
		$('#lt_sel_times').val(newVal);
		gameMsgAutoTip();
	});
	$('#plustime').live('click', function(){
		var newVal=parseInt($('#lt_sel_times').val())+1;
		$('#lt_sel_times').val(newVal);
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
	

	//11选5 ??
	$('.dantuo :radio').live('click', function(){
		var $dom=$(this).closest('.dantuo');
		
		if(this.value){
			$dom.next().hide().next().show();
		}else{
			$dom.next().show().next().hide();
		}
	});	
	
	$('.dmtm :input.code').live('click',function(event){
		var $this=$(this),
		$dom=$this.closest('.dmtm');
		if($('.code.checked[value=' + this.value +']', $dom).not(this).length==1){
			$this.removeClass('checked');
			winjinAlert('选择胆码不能与拖码相同',"alert");
			return false;
		}
	});
	
	$('.zhixu115 :input.code').live('click',function(event){
		var $this=$(this);
		if(!$this.is('.checked')) return false;
		
		var $dom=$('.zhixu115');
		$('.code.checked[value=' + this.value +']', $dom).removeClass('checked');
		$this.addClass('checked');
	});
	
	//历史开奖
	$(".jrkj").click(function() {
		$(".lskj").show();
		return false
	});
	$("body").click(function() {
		$(".lskj").hide()
	});
	$(".lskj").click(function() {
		return false
	});
	
	//玩法信息
	$('.showexample').live("mouseover",function(){
		var $id=$(this).attr('id');
		var ps = $(this).position();
		$('#'+$id+'s_div').siblings('.game_eg').hide();
		$('#'+$id+'s_div').css({top:ps.top + 20,left:ps.left + 20}).fadeIn(100);		
	})
	$('.showexample').live("mouseout",function(){
		$('#played-content').find('.game_eg').hide();		
	})
	
	//获取投注内容
	$('a[rel=projectinfo]').live('click', function(){
		var href = $(this).attr('action');
		var v_id = $(this).attr('data-value');
		var me = this;
		
		wait();
		$.ajax({
			type: "GET",
			url: href,
			data: { },
			dataType: "html",
			global: false,
			success: function (data) {
				destroyWait();
				$(data).dialog({
					title:'投注详情',
					minWidth:850,
					height:520,
					modal:true,
					resizable: false,
					buttons: {
						"关闭": function() {$( this ).dialog( "close" );}
				   }
				});
				
				$("#cancelproject").live('click',function () {
					if (true) {
						$.ajax({
							type: "POST",
							url: "/index.php?s=/home/game/deleteCode",
							data: { id: v_id },
							dataType: "json",
							global: false,
							success: function (data) {
								try {
									if (data.status == 0) {
										$('a[role=button]').click();//关闭dialog
										winjinAlert(data.info,'error');
									} else {
										$('a[role=button]').click();//关闭dialog
										$(me).parent().siblings("td:last").html('<label class="gray">已撤单</label>');
										winjinAlert('撤单成功','ok');
									}
								} catch (e) {
									$('a[role=button]').click();//关闭dialog
									winjinAlert('撤单失败，请梢后重试','error');
								}
							},
							error: null,
							cache: false
						})
					}
				});
			},
			error: function (err){
				destroyWait();
			},
			cache: false
		})
	});
	
	//一键投注、马上投注、预投注、全部清除
	$('#lt_bet_immediate').unbind('click');
	$('#lt_bet_immediate').bind('click',gamePostCode);
	$('#lt_buy').bind('click',gamePostCode2);
	$('#lt_cf_clear').bind('click',gameActionRemoveCode);
	
	//追号
	$('#lt_trace_if').click(zhuihao);
	$('#lt_trace_if2').click(zhuihao);
	$('#lt_trace_qissueno').live('change', function(){
		$('#lt_trace_count_input').val(this.value);
	});
	$('#lt_trace_ok').live('click', function(){
		var no_count = $('#lt_trace_count_input').val();
		var beishu = $('#lt_trace_times_margin').val();
		var money = parseFloat($('#lt_cf_money').text());
		var i=0;
		$('#lt_trace_issues_table tr').each(function(){
			if(i<=no_count){
				var $this=$(this);
				var node=$('td:eq(0)', $this)[0];
				var child=node.children[0];
				child.checked=true;
				
				var node=$('td:eq(2)', $this)[0];
				var child=node.children[0];
				child.disabled=false;
				child.value=beishu;
				
				var node=$('td:eq(3)', $this)[0];
				var child=node.children[0];
				child.innerHTML=money.toFixed(2);
			}else {
				var $this=$(this);
				var node=$('td:eq(0)', $this)[0];
				var child=node.children[0];
				child.checked=false;
				
				var node=$('td:eq(2)', $this)[0];
				var child=node.children[0];
				child.disabled=true;
				child.value=0;
				
				var node=$('td:eq(3)', $this)[0];
				var child=node.children[0];
				child.innerHTML='0.00';
			}
			i++;
		});
	});
	
	$('input[rel=zhuihao]').live('click', function(){
		var beishu = $('#lt_trace_times_margin').val();
		var money = parseFloat($('#lt_cf_money').text());
		
		if(this.checked==true){
			var child = this.parentNode.parentNode.children[2].children[0];
			child.disabled=false;
			child.value=beishu;
			var child = this.parentNode.parentNode.children[3].children[0];
			child.innerHTML=money.toFixed(2);
		}
		else{
			var child = this.parentNode.parentNode.children[2].children[0];
			child.disabled=true;
			child.value=0;
			var child = this.parentNode.parentNode.children[3].children[0];
			child.innerHTML='0.00';
		}
	});
	
	$('input[data=zhuihao]').live('keyup', function(){
		var beishu = $('#lt_trace_times_margin').val();
		var money = $('#lt_cf_money').text();

		var child = this.parentNode.parentNode.children[3].children[0];
		child.innerHTML=(money * this.value).toFixed(2);
		
	});
	$('input[data=zhuihao]').live('propertychange', function(){
		var beishu = $('#lt_trace_times_margin').val();
		var money = $('#lt_cf_money').text();

		var child = this.parentNode.parentNode.children[3].children[0];
		child.innerHTML=(money * this.value).toFixed(2);
		
	});
	
	$('#lt_buy_trace').live('click', gamePostCode3);
	function zhuihao(){
		
		if(parseFloat($('#lt_cf_nums').text())<1){
			winjinAlert('您还未添加预投注',"alert");
			return false;
		}
		
		$('#lt_trace_box').css('display', 'block');
		
		return false;
	}
});


/**
 * 添加投注
 */
function gamePostCode(){
	var code=[],	// 存放投注号特有信息
	zhuiHao,		// 存放追号信息
	data={};		// 存放共同信息

	
	if(parseFloat($('#lt_sel_nums').text())<1){
		winjinAlert('请先输入投注号码',"alert");
		return false;
	}
	
	try{
		code[0] = gameActionAddCode(1);
	}catch(err)
	{
		return false;
	}
	
	if(code=='undefined' || code[0]=='undefined') return false;
	
	var actionNo=$('#current_issue').text();
	if(!actionNo){
		winjinAlert('获取投注期号出错(1)',"alert");
		return false;
	}
	
	data['type']=game.type;
	data['actionNo']=actionNo;

	wait();
	$.ajax('/index.php?s=/home/game/postCode', {
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
	
	return;
	
	var tipString='<b><span class="ui-wjicon-confirm"></span>确定要购买第'+actionNo+'期彩票？</b>';
	tipString+='<br /><table width="100%"><tr><th>玩法</th><th>号码</th><th>注数</th><th>倍数</th><th>模式</th></tr>';

		tipString+="<tr><td>"+code[0].playedName+"</td><td class='code-list'>"+code[0].actionData+"</td><td>"+code[0].actionNum+"</td><td>"+code[0].beiShu+"</td><td>"+code[0].mode+"</td></tr>";
	
	tipString+='</table>';
	tipString+='<br />'+'共'+code[0].actionNum+'注，总金额：'+code[0].mode * code[0].beiShu * code[0].actionNum+'元';
	
	$('#wanjinDialog').html(tipString).dialog({
		title:'投注提示',
		resizable: false,
		width:500,
		minHeight:100,
		modal: true,
		buttons: {
		"确定购买": function() {
			$( this ).dialog( "close" );
			
			data['type']=game.type;
			data['actionNo']=actionNo;
		
			wait();
			$.ajax('/index.php?s=/home/game/postCode', {
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
 * 添加投注
 */
function gamePostCode2(){
	var code=[],	// 存放投注号特有信息
	zhuiHao,		// 存放追号信息
	data={};		// 存放共同信息

	
	if(parseFloat($('#lt_cf_nums').text())<1){
		winjinAlert('您还未添加预投注',"alert");
		return false;
	}
	
	$('#lt_cf_content tr').each(function(){
		code.push($(this).data('code'));
	});
	
	if(code==""){
		winjinAlert('您还未添加预投注',"alert");
		return false;
	}
	
	var actionNo=$('#lt_issue_start').val();
	if(!actionNo){
		winjinAlert('获取投注期号出错(2)',"alert");
		return false;
	}
	
	var tipString='<b><span class="ui-wjicon-confirm"></span>确定要购买第'+actionNo+'期彩票？</b>';
	tipString+='<br /><table width="100%"><tr><th>玩法</th><th>号码</th><th>注数</th><th>倍数</th><th>模式</th></tr>';

	$('#lt_cf_content tr').each(function(){
		var $this=$(this);
		tipString+="<tr><td>"+$('td:eq(1)', $this).text()+"</td><td class='code-list'>"+$('td:eq(2)', $this).text()+"</td><td>"+$('td:eq(3)', $this).data('value')+"</td><td>"+$('td:eq(4)', $this).text()+"</td><td>"+$('td:eq(5)', $this).text()+"</td></tr>";
	});
	
	tipString+='</table>';
	tipString+='<br />'+'共'+$('#lt_cf_nums').text()+'注，总金额：'+$('#lt_cf_money').text()+'元';
	
	$('#wanjinDialog').html(tipString).dialog({
		title:'投注提示',
		resizable: false,
		width:500,
		minHeight:100,
		modal: true,
		buttons: {
		"确定购买": function() {
			$( this ).dialog( "close" );
			
			data['type']=game.type;
			data['actionNo']=actionNo;
		
			wait();
			$.ajax('/index.php?s=/home/game/postCode', {
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
						gameActionRemoveCode();
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
 * 追号
 */
function gamePostCode3(){
	var code=[],	// 存放投注号特有信息
	zhuiHao=1,		// 存放追号信息
	data={};		// 存放共同信息

	
	if(parseFloat($('#lt_cf_nums').text())<1){
		winjinAlert('您还未添加预投注',"alert");
		return false;
	}
	
	$('#lt_cf_content tr').each(function(){
		code.push($(this).data('code'));
	});
	
	if(code==""){
		winjinAlert('您还未添加预投注',"alert");
		return false;
	}
	
	var actionNo='';
	var beishu='';
	$('#lt_trace_issues_table tr').each(function(){
		var $this=$(this);
		var node=$('td:eq(0)', $this)[0];
		var child=node.children[0];
		if(child.checked){
			var node=$('td:eq(1)', $this)[0];
			actionNo += node.innerHTML + '|';
			var node=$('td:eq(2)', $this)[0];
			var child=node.children[0];
			beishu += child.value + '|';
		}
	});
	if(!actionNo){
		winjinAlert('请至少勾选一期',"alert");
		return false;
	}
	actionNo = actionNo.substr(0, actionNo.length-1);
	beishu = beishu.substr(0, beishu.length-1);
	
	var tipString='<b><span class="ui-wjicon-confirm"></span>确定要追号吗？</b>';
	
	
	$('#wanjinDialog').html(tipString).dialog({
		title:'投注提示',
		resizable: false,
		width:500,
		minHeight:100,
		modal: true,
		buttons: {
		"确定购买": function() {
			$( this ).dialog( "close" );
			
			data['type']=game.type;
			data['actionNo']=actionNo;
			data['beishu']=beishu;
		
			wait();
			$.ajax('/index.php?s=/home/game/postCode', {
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
						gameActionRemoveCode();
						$('#lt_trace_box').css('display', 'none');
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
 * 投注后置函数
 */
function gamePostedCode(err, data){
	if(err){
		if('您的可用资金不足，是否充值？'==err){
			if(window.confirm(err)) location='/index.php?s=/home/cash/recharge';
		}else{
			winjinAlert(err,"alert");
		}
	}else{
		gameFreshOrdered();
		reloadMemberInfo();
		//gameActionRemoveCode();
		//gameCalcAmount();
		//$('#game-tip-dom').text('');
		//reload();
	}
}

/**
 * 更新余额
 */
function reloadMemberInfo(){
	//子frame调用父窗口函数
	if(parent.window.autoupdate)
		parent.window.autoupdate('/index.php?s=/home/index/userinfo');
	// $.ajax({
        // type: "POST",
        // url: '/index.php?s=/home/index/userinfo',
        // dataType: "json",
        // global: false,
        // success: function (data) {
            // $("#j-refresh").removeClass("fa-spin").removeClass("fa-2x");
            // $("#user_sscmoney").html(data.coin);
            // $("#user_nickname").html(data.nickname);
            // if (data.enable == "0")
            // {
                // alert("您帐号被冻结，请联系在线客服");
                // //document.location.href = "/public/logout";
                // return;
            // }
        // },
        // error: null,
        // cache: false
    // });
}	
/**
 * 更新定单列表
 */
function gameFreshOrdered(err, msg){
	if(err){
		winjinAlert(err,"alert");
	}else{
		$('#order-history').load('/index.php?s=/home/game/getOrdered');
	}
}
/**
 * 加载历史开奖数据
 */
function freshKaiJiangData(type){
	$('#historylot').load('/index.php?s=/home/index/getHistoryData/type/'+type);
}
/**
 * 添加预投注
 */

function gameActionAddCode(type){
	//奖金返点限制[如奖金模式在1920以下才能购买分模式(返点大于最大返点-11)]
	
	var $slider=$('#lt_sel_dyprize');	

	var obj,$game=$('#lt_selector .pp'),
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
				return gameAddCode(obj,type);
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
function gameAddCode(code,type){
	
	if($.isArray(code)){
		for(var i=0; i<code.length; i++) gameAddCode(code[i],1);
		return;
	}
	
	if(code.actionNum==0) throw('号码不正确');
	var jiangjin = document.getElementById("lt_sel_dyprize").value;
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
		
		$('#lt_selector input:hidden').each(function(){
			code[$(this).attr('name')]=this.value;
		});

		delete code.undefined;
		playedName=code.playedName||$('#tabbar-div-s3 .act .method-tab-front').text(),
		code.playedName=playedName;
		
		if(type==2){
			$('#lt_cf_content tr[class="nr"]').remove();
			
			$('<tr>').data('code', code)
			.append('<td class="tl_li_l" width="4"></td>')
			.append(
				// 玩法
				$('<td>').append(playedName)
			)
			.append(
				// 号码列表
				$('<td class="code-list">').append(wei+(code.actionData.length>18?(code.actionData.substr(0,5)+'...'):code.actionData))
			)
			.append(
				// 注数
				$('<td>').data('value', code.actionNum).append('['+code.actionNum+'注]')
			)
			.append(
				// 倍数
				$('<td>').append(code.beiShu+'倍')
			)
			.append(
				// 单价
				$('<td>').append(modeName[code.mode])
			)
			.append(
				// 总金额
				$('<td>').data('value', amount).append(amount.toFixed(2)+"元")
			)
			.append(
				// 操作
				$('<td title="删除" class="c tl_li_r" width="16" onclick="deleteCode(this)"><input name="lt_project[]" type="hidden"></td>')
			)
			.appendTo('#lt_cf_content');
			
			var tab = document.getElementById("lt_cf_content") ;
			$('#lt_cf_count').html(tab.rows.length);
			//计算总金额
			gameCalcAmount();
		}
		
		$('#textarea-code').val("");
		
		$('#lt_selector :button.checked').removeClass('checked');
		$('#lt_sel_nums').text('0');
		$('#lt_sel_money').text('0.00');
		
		code['flag'] = 1;
		return code;
	}catch(err){
		winjinAlert(err,"alert");
		throw(err);
	}
}


/**
 * 计算总注数与总金额，并显示
 * fpcount 是否飞盘 费用翻倍
 */
function gameCalcAmount(){
	var count=0, fpcount=1, amount=0.0, $zhuiHao=$(':checkbox[name=zhuiHao]'), $feipan=$(':checkbox[name=fpEnable]');
	if($feipan.prop('checked')) fpcount=2;
	if($zhuiHao.prop('checked')){
		var data=$('.touzhu-cont tr').data('code');
		$zhuiHao.data('zhuiHao').split(';').forEach(function(v){
			count+=parseInt(v.split('|')[1]);
		});
		amount=data.mode*data.actionNum*count*fpcount;
	}else{
		$('#lt_cf_content tr').each(function(){
			var $this=$(this);
			count+=$('td:eq(3)', $this).data('value');
			amount+=$('td:eq(6)', $this).data('value');
		});
	}

	$('#lt_cf_nums').text(count);
	$('#lt_cf_money').text(amount.toFixed(2));

}

/**
 * 清除号码
 *
 * @params bool isSelected	是否只清除选中的项，默认false
 */
function gameActionRemoveCode(isSelected){
	$('#lt_cf_content tr').remove();
	gameCalcAmount();
	$('<tr class="nr"><td class="tl_li_l" width="4"></td><td colspan="6" class="noinfo">暂无投注项</td><td class="tl_li_rn" width="4"></td></tr>').appendTo('#lt_cf_content');
}

//清除单项号码
function deleteCode(self){
	$(self).parent().remove();
	var tab = document.getElementById("lt_cf_content") ;
	$('#lt_cf_count').html(tab.rows.length);
	gameCalcAmount();
	if(parseInt(tab.rows.length)<1){
		$('<tr class="nr"><td class="tl_li_l" width="4"></td><td colspan="6" class="noinfo">暂无投注项</td><td class="tl_li_rn" width="4"></td></tr>').appendTo('#lt_cf_content');
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
	var obj,$game=$('#lt_selector .pp'),
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
			
			$('#lt_sel_nums').text(obj.actionNum);
			$('#lt_sel_money').text((gameGetMode()*gameGetBeiShu()*obj.actionNum).toFixed(2));
			
		}catch(err){
			$('#lt_sel_nums').text('0');
			$('#lt_sel_money').text('0.00');
			//$('#count-amount').html(err);
		}
	}
}

//万金提示  
function winjinAlert(tips,style,minH){
	
	$( "#wanjinDialog" ).html('<span class="ui-wjicon-'+style+'"></span><b>'+tips+'</b>').dialog({
		title:'温 馨 提 示',
		modal: true,
		resizable: false,
		minWidth:250,
		minHeight:(minH?minH:180),
		buttons: {
		"确定": function() {$( this ).dialog( "close" );}
	   }
	});	//dialog end	
}

// 读取模式
function gameGetMode(){
	var mode = $('#lt_sel_modes').val();
	return parseFloat(mode||1);
}
// 读取倍数
function gameGetBeiShu(){
	var txt=$('#lt_sel_times').val();
	if(!txt) return 1;
	var re=/^[1-9][0-9]*$/;
	if(!re.test(txt)){
		throw('倍数只能为大于1正整数');
		$('#lt_sel_times').val(1);
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
	var $dom=$('#count_down');
	
	if(diffTime<0){
				
		if(T) clearTimeout(T);
		
		setKjing();
		getQiHao();
		kjTimer = setTimeout(loadKjData, 10000);
		
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
		$dom.html((h<10?"0"+h:h)+"&nbsp;"+(s<10?"0"+s:s)+"&nbsp;"+m);
	}else{
		h=0;
		$dom.html("00"+"&nbsp;"+s+"&nbsp;"+m);
	}
	
	if(T) clearTimeout(T);
	T=setTimeout(gameKanJiangDataC, 1000, diffTime);	
  }
}

function loadKjData(){
	var type=game.type;
	$.ajax('/index.php?s=/home/index/getLastKjData/type/'+type,{
		dataType:'json',
		cache:false,
		error:function(){
			if(kjTimer) clearTimeout(kjTimer);
			kjTimer = setTimeout(loadKjData, 10000);
		},
		success:function(data, textStatus, xhr){
			
			if(!data){
				if(kjTimer) clearTimeout(kjTimer);
				kjTimer = setTimeout(loadKjData, 10000);
				setKjing();				
			}else{
				try{
					//停止开奖转动
					if(moveno) clearInterval(moveno);
					
					var $dom=$('#kaijiang'),$kjHaoS,$feipan,hao;
					
					if(parseInt(type)==24){ //快8
						$kjHaoS=data.data.split('|');
						hao=$kjHaoS[0].split(',');
						$feipan=$kjHaoS[1];
					}else{
						hao=data.data.split(',');
					}
		
					$('#last_issue').html(data.actionNo);
					var ctype=$('#showcodebox').attr('ctype');
					var times=3000;
					if(ctype=='g1'){
						$('.kjhao li').each(function(i){
							$(this).html(hao[i]);
						});
						if($dom.find('.feipan')) $dom.find('.feipan').html("快乐飞盘：<em>"+$feipan+"</em>");
					}else if(ctype=='k8'){ //k8
						$('#showcodebox div').each(function(i){
							$(this).text(hao[i]);
						});
					}else if(ctype=='pk10'){ //pk10
						$('#showcodebox div').each(function(i){
							$(this).attr('class','gr_c gr_c' + hao[i]);
						});
					}else{
						$('#showcodebox div').each(function(i){
							$(this).attr('class','gr_s gr_s' + hao[i]);
						});
					}
					
					//更新
					gameFreshOrdered();
					reloadMemberInfo();
					freshKaiJiangData(game.type);
					
					if((typeof $('#wanjinDialog').dialog("isOpen")=='object') || $('#wanjinDialog').dialog('isOpen')){
						$('#wanjinDialog').dialog('close');
					}										
				}catch(err){
					if(kjTimer) clearTimeout(kjTimer);
					kjTimer = setTimeout(loadKjData, 10000);
				}
			}
		}
	});	
}

function getQiHao(){
	$.getJSON('/index.php?s=/home/index/getQiHao/type/'+game.type, function(data){
		if(data && data.lastNo && data.thisNo){
			$('#current_issue').html(data.thisNo.actionNo);
			$('#last_issue').html(data.lastNo.actionNo);			
			futureNo(data.thisNo.actionNo,game.type);
			
			S=true;
			if(T) clearTimeout(T);
			kjTime=parseInt(data.kjdTime);
			gameKanJiangDataC(data.diffTime-kjTime, data.thisNo.actionNo);
		}
	});
}

//等待开奖旋转
var  moveno;
function setKjing(){
	var ctype=$('#showcodebox').attr('ctype');
	var cnum=$('#showcodebox').attr('cnum'),num;
		cnum=parseInt(cnum);
	
	//停止开奖转动
	if(moveno) clearInterval(moveno);
					
	if(ctype=='g1'){
		moveno = window.setInterval(function () {
			$.each($("showcodebox").find("div"), function (i, n) {
				if ($(this).attr("flag") == "move") {
					num=Math.floor((cnum-1) * Math.random() + 1);
					if(num<10) num='0'+num;
					$(this).html(num);
				}
			})
		}, 500);
	}else if(ctype=='g2'){  //快3
		moveno = window.setInterval(function () {
			$.each($("showcodebox").find("div"), function (i, n) {
				if ($(this).attr("flag") == "move") {
					$(this).attr("class", "gr_ks gr_ksm" + Math.floor(6 * Math.random() + 1));
				}
			})
		}, 500);
	}else if(ctype=='k8'){ //快乐8
		moveno = window.setInterval(function () {
		$.each($("#showcodebox").find("div"), function (i, n) {
				$(this).text(Math.floor(9 * Math.random()));
			})
		}, 500);
	}else if(ctype=='pk10'){
		moveno = window.setInterval(function () {
		$.each($("#showcodebox").find("div"), function (i, n) {
				$(this).attr("class", "gr_c gr_c0" + Math.floor(9 * Math.random()));
			})
		}, 500);
	}
	else{
		moveno = window.setInterval(function () {
		$.each($("#showcodebox").find("div"), function (i, n) {
				$(this).attr("class", "gr_s gr_s" + Math.floor(10 * Math.random()));
			})
		}, 500);
	}
}

function futureNo(action,type)
{
	var html='';
	var length=0,maxNo=0,classtype=1;
	if(type==34)
	{
		length=720;
		maxNo=720;
	}
	else if(type==35)
	{
		classtype=7;
		length=960;
		maxNo=960;
	}
	else if(type==1)
	{
		length=120;
		maxNo=120;
	}
	else if(type==3)
	{
		length=84;
		maxNo=84;
	}
	else if(type==12)
	{
		classtype=2;
		length=96;
		maxNo=96;
	}
	else if(type==14)
	{
		length=288;
		maxNo=288;
	}
	//11选5
	else if(type==6)
	{
		length=84;
		maxNo=84;
	}
	else if(type==15)
	{
		length=85;
		maxNo=85;
	}
	else if(type==16)
	{
		length=78;
		maxNo=78;
	}
	//3d 排列三
	else if(type==9 || type==10)
	{
		classtype=3;
		length=20;
	}
	//pk10
	else if(type==20 || type==24)
	{
		classtype=6;
		length=179;
	}
	if(classtype==1){
		var no = action.substr(9,3);
		var qian = action.substr(0,8);
		var num=1;
		for(var i=0;i<length;i++){
			num=parseInt(no)+parseInt(i);
			if(num<=maxNo){
				no2 = qian+'-'+(num+1000+' ').substr(1,3);
				html = html + '<option value="'+no2+'">'+no2+'</option>';
			}
			else{
				num=num-maxNo+1000;
				var time = new Date(new Date().valueOf() + 1*24*60*60*1000);
				var y = time.getFullYear();
				var m = time.getMonth()+1;
				var d = time.getDate();
				qian = y+''+(m<10?'0'+m:m)+(d<10?'0'+d:d);
				no2=qian+'-'+(num+' ').substr(1,3);
				html = html + '<option value="'+no2+'">'+no2+'</option>';
			}
			$('<tr>').append('<td class="r1"><input name="lt_trace_issues[]" rel="zhuihao" value="'+ no2 + '" type="checkbox"></td>')
			.append('<td>'+no2+'</td>')
			.append('<td class="nosel"><input name="lt_trace_times_20160430-003" data="zhuihao" class="r2" value="0" disabled="disabled" type="text">倍</td>')
			.append('<td>￥<span id="lt_trace_money_20160430-003">0.00</span></td>')
			.appendTo('#lt_trace_issues_table');
		}
	}
	else if(classtype==2){
		var no = action.substr(9,2);
		var qian = action.substr(0,8);
		var num=1;
		for(var i=0;i<length;i++){
			num=parseInt(no)+parseInt(i);
			if(num<=maxNo){
				no2 = qian+'-'+(num+100+' ').substr(1,2);
				html = html + '<option value="'+no2+'">'+no2+'</option>';
			}
			else{
				num=num-maxNo+100;
				var time = new Date(new Date().valueOf() + 1*24*60*60*1000);
				var y = time.getFullYear();
				var m = time.getMonth()+1;
				var d = time.getDate();
				qian = y+''+(m<10?'0'+m:m)+(d<10?'0'+d:d);
				no2=qian+'-'+(num+' ').substr(1,2);
				html = html + '<option value="'+no2+'">'+no2+'</option>';
			}
		}
	}
	else if(classtype==3){
		var no = action.substr(4,3);
		var qian = action.substr(0,4);
		var num=1;
		for(var i=0;i<length;i++){
			num=parseInt(no)+parseInt(i);
			no2 = qian+''+(num+1000+' ').substr(1,3);
			html = html + '<option value="'+no2+'">'+no2+'</option>';			
		}
	}
	else if(classtype==6){
		var num=1;
		for(var i=0;i<length;i++){
			num=parseInt(action)+parseInt(i);
			html = html + '<option value="'+num+'">'+num+'</option>';			
		}
	}
	else if(classtype==7){
		var num=1;
		for(var i=0;i<length;i++){
			num=parseInt(action)+parseInt(i);
			html = html + '<option value="'+num+'">'+num+'</option>';
			$('<tr>').append('<td class="r1"><input name="lt_trace_issues[]" rel="zhuihao" value="'+ num + '" type="checkbox"></td>')
			.append('<td>'+num+'</td>')
			.append('<td class="nosel"><input name="lt_trace_times_20160430-003" data="zhuihao" class="r2" value="0" disabled="disabled" type="text">倍</td>')
			.append('<td>￥<span id="lt_trace_money_20160430-003">0.00</span></td>')
			.appendTo('#lt_trace_issues_table');
		}
	}
	$('#lt_issue_start').html(html);
}

//显示玩法组
function selectGroup(self){
	$('#lt_sel_nums').text('0');
	$('#lt_sel_money').text('0.00');
	if($(self).is('.tab-front'))
		return false;
	var url = $(self).attr('data');	
	wait();
	$('#played-span').load(url,function(){
		destroyWait();
		var $old = $(self).closest('#tabbar-div-s2').find('.tab-front');
		$old.removeClass('tab-front');
		$old.addClass('tab-back');
		$(self).removeClass('tab-back');
		$(self).addClass('tab-front');
	});
	
	return false;
}
//显示分玩法
function selectPlayed(self){
	$('#lt_sel_nums').text('0');
	$('#lt_sel_money').text('0.00');
	if($(self).is('.act'))
		return false;
	var url = $(self).attr('data');
	wait();
	$('#played-content').load(url,function(re){
		destroyWait();
		var $old = $(self).closest('.tz_li').find('.act');
		$old.removeClass('act');
		$old.addClass('back');
		$(self).removeClass('back');
		$(self).addClass('act');
		var a = re;
	});
	
	return false;
}