<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo ($meta_title); ?>|时彩后台管理</title>
    <link href="/lf/html/Public/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <link rel="stylesheet" type="text/css" href="/lf/html/Public/Admin/css/base.css" media="all">
    <link rel="stylesheet" type="text/css" href="/lf/html/Public/Admin/css/common.css" media="all">
    <link rel="stylesheet" type="text/css" href="/lf/html/Public/Admin/css/module.css">
    <link rel="stylesheet" type="text/css" href="/lf/html/Public/Admin/css/style.css" media="all">
	<link rel="stylesheet" type="text/css" href="/lf/html/Public/Admin/css/blue_color.css" media="all">
	<link rel="stylesheet" type="text/css" href="/lf/html/Public/Admin/css/jquery-ui-1.8.21.custom.css" media="all">
     <!--[if lt IE 9]>
    <script type="text/javascript" src="/lf/html/Public/static/jquery-1.10.2.min.js"></script>
    <![endif]--><!--[if gte IE 9]><!-->
	<script type="text/javascript" src="/lf/html/Public/static/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="/lf/html/Public/Admin/js/jquery-1.8.0.min.js"></script>
    <script type="text/javascript" src="/lf/html/Public/Admin/js/jquery.mousewheel.js"></script>
	<script type="text/javascript" src="/lf/html/Public/Admin/js/jquery-ui-1.8.23.custom.min.js"></script>
	
	<script> 
		function goToDealWithCash(){
			window.location.href = "<?php echo U('business/cash');?>";
			//$('.yw_b_2').trigger('click');
			$(this).dialog('destroy');
		}
		
		function goToDealWithRec(){
			window.location.href = "<?php echo U('business/recharge');?>";
			//$('.yw_b_2').trigger('click');
			$(this).dialog('destroy');
		}
		function defaultCloseModal(){
			$(this).dialog('destroy');
		}
	</script>
    <!--<![endif]-->
    
</head>
<body>
    <!-- 头部 -->
    <?php $__base_menu__ = $__controller__->getMenus(); ?>
    <div class="header">
        <!-- Logo -->
        <span class="logo"></span>
        <!-- /Logo -->

        <!-- 主导航 -->
        <ul class="main-nav">
            <?php if(is_array($__base_menu__["main"])): $i = 0; $__LIST__ = $__base_menu__["main"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li class="<?php echo ((isset($menu["class"]) && ($menu["class"] !== ""))?($menu["class"]):''); ?>"><a href="<?php echo (u($menu["url"])); ?>"><?php echo ($menu["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
        <!-- /主导航 -->

        <!-- 用户栏 -->
        <div class="user-bar">
            <a href="javascript:;" class="user-entrance"><i class="icon-user"></i></a>
            <ul class="nav-list user-menu hidden">
                <li class="manager">你好，<em title="<?php echo session('user_auth.username');?>"><?php echo session('user_auth.username');?></em></li>
                <li><a href="<?php echo U('User/updatePassword');?>">修改密码</a></li>
                <!-- <li><a href="<?php echo U('User/updateNickname');?>">修改昵称</a></li> -->
                <li><a href="<?php echo U('Public/logout');?>">退出</a></li>
            </ul>
        </div>
    </div>
    <!-- /头部 -->

    <!-- 边栏 -->
    <div class="sidebar">
        <!-- 子导航 -->
        
            <div id="subnav" class="subnav">
                <?php if(!empty($_extra_menu)): ?>
                    <?php echo extra_menu($_extra_menu,$__base_menu__); endif; ?>
                <?php if(is_array($__base_menu__["child"])): $i = 0; $__LIST__ = $__base_menu__["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub_menu): $mod = ($i % 2 );++$i;?><!-- 子导航 -->
                    <?php if(!empty($sub_menu)): if(!empty($key)): ?><h3><i class="icon icon-unfold"></i><?php echo ($key); ?></h3><?php endif; ?>
                        <ul class="side-sub-menu">
                            <?php if(is_array($sub_menu)): $i = 0; $__LIST__ = $sub_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li>
                                    <a class="item" href="<?php echo (u($menu["url"])); ?>"><?php echo ($menu["title"]); ?></a>
                                </li><?php endforeach; endif; else: echo "" ;endif; ?>
                        </ul><?php endif; ?>
                    <!-- /子导航 --><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        
        <!-- /子导航 -->
    </div>
    <!-- /边栏 -->

    <!-- 内容区 -->
    <div id="main-content">
        <div id="top-alert" class="fixed alert alert-error" style="display: none;">
            <button class="close fixed" style="margin-top: 4px;">&times;</button>
            <div class="alert-content">这是内容</div>
        </div>
        <div id="main" class="main">
            
            <!-- nav -->
            <?php if(!empty($_show_nav)): ?><div class="breadcrumb">
                <span>您的位置:</span>
                <?php $i = '1'; ?>
                <?php if(is_array($_nav)): foreach($_nav as $k=>$v): if($i == count($_nav)): ?><span><?php echo ($v); ?></span>
                    <?php else: ?>
                    <span><a href="<?php echo ($k); ?>"><?php echo ($v); ?></a>&gt;</span><?php endif; ?>
                    <?php $i = $i+1; endforeach; endif; ?>
            </div><?php endif; ?>
            <!-- nav -->
            

            
	<script type="text/javascript" src="/lf/html/Public/static/uploadify/jquery.uploadify.min.js"></script>
	
		<article class="module width_full">
	<form action="<?php echo U('group');?>" method="post" class="form-horizontal">
	<div class="data-table table-striped">
		<table>

			<tbody>
				<tr>
					<td>平台名称</td>
					<td><input type="text" value="<?=$settings['webName']?>" name="webName"/></td>
				</tr>
				<tr>
					<td>网站开关</td>
					<td>
						<label><input type="radio" value="1" name="switchWeb" <?=$this->iff($settings['switchWeb'],'checked="checked"')?>/>开启</label>
						<label><input type="radio" value="0" name="switchWeb" <?=$this->iff(!$settings['switchWeb'],'checked="checked"')?>/>关闭</label>
					</td>
				</tr>
			
				<!--<tr>
					<td>关闭网站公告</td>
					<td>
						<textarea name="webCloseServiceResult" cols="56" rows="2"><?=$settings['webCloseServiceResult']?></textarea>
					</td>
				</tr>-->
				<tr>
					<td>投注开关</td>
					<td>
						<label><input type="radio" value="1" name="switchBuy" <?=$this->iff($settings['switchBuy'],'checked="checked"')?>/>开启</label>
						<label><input type="radio" value="0" name="switchBuy" <?=$this->iff(!$settings['switchBuy'],'checked="checked"')?>/>关闭</label>
					</td>
				</tr>
				<tr>
					<td>投注模式</td>
					<td>
						<label><input type="radio" value="1" name="yuan_mode" <?=$this->iff($settings['yuan_mode'],'checked="checked"')?>/>元开启</label>
						<label><input type="radio" value="0" name="yuan_mode" <?=$this->iff(!$settings['yuan_mode'],'checked="checked"')?>/>元关闭</label>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<label><input type="radio" value="1" name="jiao_mode" <?=$this->iff($settings['jiao_mode'],'checked="checked"')?>/>角开启</label>
						<label><input type="radio" value="0" name="jiao_mode" <?=$this->iff(!$settings['jiao_mode'],'checked="checked"')?>/>角关闭</label>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<label><input type="radio" value="1" name="fen_mode" <?=$this->iff($settings['fen_mode'],'checked="checked"')?>/>分开启</label>
						<label><input type="radio" value="0" name="fen_mode" <?=$this->iff(!$settings['fen_mode'],'checked="checked"')?>/>分关闭</label>
						&nbsp;&nbsp;&nbsp;&nbsp;
						
						
						
					</td>
				</tr>
				<tr>
					<td>代理投注开关</td>
					<td>
						<label><input type="radio" value="1" name="switchDLBuy" <?=$this->iff($settings['switchDLBuy'],'checked="checked"')?>/>开启</label>
						<label><input type="radio" value="0" name="switchDLBuy" <?=$this->iff(!$settings['switchDLBuy'],'checked="checked"')?>/>关闭</label>
					</td>
				</tr>
				<tr>
					<td>最大返点限制</td>
					<td>
						  元模式：<input type="text" class="textWid1" value="<?=$settings['betModeMaxFanDian0']?>" name="betModeMaxFanDian0"/>%
						　角模式：<input type="text" class="textWid1" value="<?=$settings['betModeMaxFanDian1']?>" name="betModeMaxFanDian1"/>%
						　分模式：<input type="text" class="textWid1" value="<?=$settings['betModeMaxFanDian2']?>" name="betModeMaxFanDian2"/>%
					</td>
				</tr>
				<tr>
					<td>最大投注限制</td>
					<td>
						  最大注数：<input type="text" class="textWid1" value="<?=$settings['betMaxCount']?>" name="betMaxCount"/>注
						　最大中奖：<input type="text" class="textWid1" value="<?=$settings['betMaxZjAmount']?>" name="betMaxZjAmount"/>元
					</td>
				</tr>
			   
				<tr>
					<td>充值限制</td>
					<td>
						最低金额：<input type="text" class="textWid1" value="<?=$settings['rechargeMin']?>" name="rechargeMin"/>元&nbsp;&nbsp; 
						最高金额：<input type="text" class="textWid1" value="<?=$settings['rechargeMax']?>" name="rechargeMax"/>元
						<br /><br />
						支付宝/财付通：最低金额 <input type="text" class="textWid1" value="<?=$settings['rechargeMin1']?>" name="rechargeMin1"/>元&nbsp;&nbsp;最高金额 <input type="text" class="textWid1" value="<?=$settings['rechargeMax1']?>" name="rechargeMax1"/>元&nbsp;&nbsp;
						
					</td>
				</tr>
				<tr>
					<td>提现限制</td>
					<td>
						消费满：<input type="text" class="textWid1" value="<?=$settings['cashMinAmount']?>" name="cashMinAmount"/>%&nbsp;&nbsp;
						最低金额：<input type="text" class="textWid1" value="<?=$settings['cashMin']?>" name="cashMin"/>元&nbsp;&nbsp;
						最高金额：<input type="text" class="textWid1" value="<?=$settings['cashMax']?>" name="cashMax"/>元&nbsp;&nbsp;
						时间段： 从 <input type="time" value="<?=$settings['cashFromTime']?>" name="cashFromTime" class="textWid1"/> 到 <input type="time" value="<?=$settings['cashToTime']?>" name="cashToTime" class="textWid1"/>
						<br /><br />
						支付宝/财付通：最低金额 <input type="text" class="textWid1" value="<?=$settings['cashMin1']?>" name="cashMin1"/>元&nbsp;&nbsp;
						最高金额 <input type="text" class="textWid1" value="<?=$settings['cashMax1']?>" name="cashMax1"/>元&nbsp;&nbsp;
						提现次数 <input type="text" class="textWid1" value="<?=$settings['cashTimes']?>" name="cashTimes"/>次&nbsp;&nbsp;
					</td>
				</tr>
				<tr>
					<td>清理账号规则</td>
					<td>账户金额低于&nbsp;<input type="text" value="<?=$settings['clearMemberCoin']?>" name="clearMemberCoin" id="clearMemberCoin"/>元，&nbsp;且&nbsp;<input type="text" value="<?=$settings['clearMemberDate']?>" name="clearMemberDate" id="clearMemberDate"/> &nbsp;天未登录&nbsp;&nbsp;<a method="post" target="ajax" title="数据清除不可修复，是否继续！" dataType="json" id="alt_btn3" href="<?php echo U('config/clearUser');?>">清理</a></td>
				</tr>
				<tr>
					<td>清理数据</td>
					<td>清除当前 <input type="date" name="date" class="time" value="" placeholder="请选择时间" />日期及以前数据&nbsp;&nbsp;<a method="post" target="ajax" title="数据清除不可修复，是否继续！" id="alt_btn3" href="<?php echo U('config/clearData');?>">清理</a></td>
				</tr>
				<tr>
					<td>赠送活动</td>
					<td>首次注册绑定银行卡送<input class="textWid1" type="text" value="<?=$settings['huoDongRegister']?>" name="huoDongRegister"/>元 &nbsp;&nbsp;每天签到每次送<input type="text" class="textWid1" value="<?=$settings['huoDongSign']?>" name="huoDongSign"/>元，如果为0则关闭活动</td>
				</tr>
				<tr>
					<td>充值佣金活动</td>
					<td>每天首次充值金额<input class="textWid1" type="text" value="<?=$settings['rechargeCommissionAmount']?>" name="rechargeCommissionAmount"/>元以上，上家送<input type="text" class="textWid1" value="<?=$settings['rechargeCommission']?>" name="rechargeCommission"/>元佣金，上上家送<input class="textWid1" type="text" value="<?=$settings['rechargeCommission2']?>" name="rechargeCommission2"/>元佣金，如果为0则关闭活动</td>
				</tr>
				<tr>
					<td>消费佣金活动</td>
					<td>
					<p>每天消费达<input class="textWid1" type="text" value="<?=$settings['conCommissionBase']?>" name="conCommissionBase"/>元时，上家送<input  class="textWid1"type="text" value="<?=$settings['conCommissionParentAmount']?>" name="conCommissionParentAmount"/>元佣金，上上家送<input  class="textWid1"type="text" value="<?=$settings['conCommissionParentAmount2']?>" name="conCommissionParentAmount2"/>元佣金，如果为0则关闭活动</p>
					</p></td>
				</tr>
				<tr>
					<td>返点最大值</td>
					<td><input type="text" value="<?=$settings['fanDianMax']?>" name="fanDianMax"/>% &nbsp;&nbsp;不定位返点最大值<input type="text" value="<?=$settings['fanDianBdwMax']?>" name="fanDianBdwMax"/>%</td>
				</tr>
				<tr>
					<td>上下级返点最小差值</td>
					<td><input type="text" value="<?=$settings['fanDianDiff']?>" name="fanDianDiff"/>%</td>
				</tr>
				<tr>
					<td>最低限制人数返点</td>
					<td><input type="text" value="<?=$settings['minFanDianUserCount']?>" name="minFanDianUserCount"/>%</td>
				</tr>
				<!--<tr>
					<td>积分比例</td>
					<td>
						<input type="text" value="<?=$settings['scoreProp']?>" name="scoreProp"/> 每消费1元积的分数
					</td>
				</tr>
				<tr>
					<td>积分规则</td>
					<td>
						<textarea name="scoreRule" cols="56" rows="5"><?=$settings['scoreRule']?></textarea>
					</td>
				</tr>-->
				<tr>
					<td>客服状态</td>
					<td>
						<label><input type="radio" value="1" name="kefuStatus" <?=$this->iff($settings['kefuStatus'],'checked="checked"')?>/>开启</label>
						<label><input type="radio" value="0" name="kefuStatus" <?=$this->iff(!$settings['kefuStatus'],'checked="checked"')?>/>关闭</label>

					</td>
				</tr>
				<tr>
					<td>客服链接</td>
					<td>
						<textarea name="kefuGG" cols="56" rows="1"><?=$settings['kefuGG']?></textarea>
					</td>
				</tr>
				
				<tr>
					<td>五/二分彩盈亏率</td>
					<td>
						<input type="text" value="<?=$settings['wufencai']?>" name="wufencai"/> 
					</td>
				</tr>
				<tr>
					<td>消费活动时间</td>
					<td>
						从<input type="text" value="<?=$settings['activity_first_time']?>" name="activity_first_time"/> 
						到<input type="text" value="<?=$settings['activity_end_time']?>" name="activity_end_time"/> 
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<footer>
		<div class="form-item">
			<label class="item-label"></label>
			<div class="controls">
				<button type="submit" class="btn submit-btn ajax-post" target-form="form-horizontal">确 定</button>				
				<button class="btn btn-return" onclick="javascript:history.back(-1);return false;">返 回</button>
			</div>
		</div>
	</footer>
	</form>
</article>

        </div>
        <div class="cont-ft">
            <div class="copyright">
                <div class="fl">感谢使用<a href="http://www.onethink.cn" target="_blank">OneThink</a>管理平台</div>
                
            </div>
        </div>
    </div>
    <!-- /内容区 -->
    <script type="text/javascript">
    (function(){
        var ThinkPHP = window.Think = {
            "ROOT"   : "/lf/html", //当前网站地址
            "APP"    : "/lf/html/admin.php?s=", //当前项目地址
            "PUBLIC" : "/lf/html/Public", //项目公共目录地址
            "DEEP"   : "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
            "MODEL"  : ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
            "VAR"    : ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
        }
    })();
    </script>
    <script type="text/javascript" src="/lf/html/Public/static/think.js"></script>
    <script type="text/javascript" src="/lf/html/Public/Admin/js/common.js"></script>
    <script type="text/javascript">
        +function(){
            var $window = $(window), $subnav = $("#subnav"), url;
            $window.resize(function(){
                $("#main").css("min-height", $window.height() - 130);
            }).resize();

            /* 左边菜单高亮 */
            url = window.location.pathname + window.location.search;
            url = url.replace(".html", "")
                .replace(/(\/(p)\/\d+)|(&p=\d+)|(\/(id)\/\d+)|(&id=\d+)/, "");
            $subnav.find("a[href^='" + url + "']").parent().addClass("current");

            /* 左边菜单显示收起 */
            $("#subnav").on("click", "h3", function(){
                var $this = $(this);
                $this.find(".icon").toggleClass("icon-fold");
                $this.next().slideToggle("fast").siblings(".side-sub-menu:visible").
                      prev("h3").find("i").addClass("icon-fold").end().end().hide();
            });

            $("#subnav h3 a").click(function(e){e.stopPropagation()});

            /* 头部管理员菜单 */
            $(".user-bar").mouseenter(function(){
                var userMenu = $(this).children(".user-menu ");
                userMenu.removeClass("hidden");
                clearTimeout(userMenu.data("timeout"));
            }).mouseleave(function(){
                var userMenu = $(this).children(".user-menu");
                userMenu.data("timeout") && clearTimeout(userMenu.data("timeout"));
                userMenu.data("timeout", setTimeout(function(){userMenu.addClass("hidden")}, 100));
            });

	        /* 表单获取焦点变色 */
	        $("form").on("focus", "input", function(){
		        $(this).addClass('focus');
	        }).on("blur","input",function(){
				        $(this).removeClass('focus');
			        });
		    $("form").on("focus", "textarea", function(){
			    $(this).closest('label').addClass('focus');
		    }).on("blur","textarea",function(){
			    $(this).closest('label').removeClass('focus');
		    });

            // 导航栏超出窗口高度后的模拟滚动条
            var sHeight = $(".sidebar").height();
            var subHeight  = $(".subnav").height();
            var diff = subHeight - sHeight; //250
            var sub = $(".subnav");
            if(diff > 0){
                $(window).mousewheel(function(event, delta){
                    if(delta>0){
                        if(parseInt(sub.css('marginTop'))>-10){
                            sub.css('marginTop','0px');
                        }else{
                            sub.css('marginTop','+='+10);
                        }
                    }else{
                        if(parseInt(sub.css('marginTop'))<'-'+(diff-10)){
                            sub.css('marginTop','-'+(diff-10));
                        }else{
                            sub.css('marginTop','-='+10);
                        }
                    }
                });
            }
        }();
    </script>
    
<link href="/lf/html/Public/static/datetimepicker/css/datetimepicker.css" rel="stylesheet" type="text/css">
<link href="/lf/html/Public/static/datetimepicker/css/datetimepicker_blue.css" rel="stylesheet" type="text/css">
<link href="/lf/html/Public/static/datetimepicker/css/dropdown.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/lf/html/Public/static/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="/lf/html/Public/static/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>

<script>
$(function(){
	$('.time').datetimepicker({
        format: 'yyyy-mm-dd',
        language:"zh-CN",
        minView:2,
        autoclose:true
    });
    showTab();
});
</script>

</body>
</html>