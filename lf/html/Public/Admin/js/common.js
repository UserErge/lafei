

var TIP=true;

//dom加载完成后执行的js
;$(function(){

	
	// form扩展
	/**
	 * 简单AJAX表单
	 * target="ajax"：AJAX提交
	 * onajax：AJAX调用前触发，this指向from元素，返回false时阻止
	 * call：AJAX完后触发，callback(err, data, xhr) this指向当前html元素，err当出错的时间有值，data为服务器返回值(解析过)，xhr为HttpRequest对象
	 * 服务器响应类型为json
	 */
	$('form[target=ajax]').live('submit', function(){
		var data	= [], 
		$this		= $(this),
		self		= this,
		onajax		= window[$this.attr('onajax')],
		call		= window[$this.attr('call')];
		
		if(typeof call!='function'){
			// 设置一个默认的响应回调
			call=function(){}
		}
		
		if('function'==typeof onajax){
			// 如果ajax请求前事件处理返回false
			// 则阻止后继事件
			try{
				if(onajax.call(this)===false) return false;
			}catch(err){
				call.call(self, err);
				return false;
			}
		}

		$(':input[name]', this).each(function(){
			var $this=$(this),
			value=$this.data('value'),
			name=$this.attr('name');
			
			if($this.is(':radio, :checked') && this.checked==false) return true;
			
			if(value===undefined) value=this.value;
			
			data.push({name:name, value:value});
		});
		
		$.ajax({
			url:$this.attr('action'),
			
			// 异步请求
			async:true,

			data:data,
			
			// 默认用GET请求，也可以用method属性设置
			type:$this.attr('method')||'get',
			
			// dataType属性用于设置响应数据格式，默认json，可选html、json和xml
			dataType:$this.attr('dataType')||'json',
			
			headers:{"x-form-call":1},
			
			error:function(xhr, textStatus, errThrow){
				// 据jQuery官方说，textStatus和errThrow中只有一个包括错误信息
				updateAlert(errThrow||textStatus,'alert-error');
								
				setTimeout(function(){
						$('#top-alert').find('button').click();
					},1000);
			},
			
			success:function(data2, textStatus, xhr, headers){
				var status = data2.status;
				if(status==0){
					updateAlert(data2.info,'alert-error');
				}else if(status==1){
					updateAlert(data2.info,'alert-success');
				}
				else{
					updateAlert(data2.info,'alert-error');
				}
				
				setTimeout(function(){
						if (data2.url) {
							location.href=data2.url;
						}else{
							$('#top-alert').find('button').click();
						}
					},1000);
			}
		});
		
		return false;
	});
	
	// A弹出层打开链接
	/**
	 * target="modal"
	 * title="弹出层标题"
	 * width="弹出宽度"
	 * heigth=""
	 * modal=false
	 * buttons="确定:onsure|取消:oncancel"
	 * method="get"
	 */
	$('a[target=modal]').bind('click', function(){
		var self=this,
		$self=$(self),
		title=$self.attr('title')||'',
		width=$self.attr('width')||'auto',
		heigth=$self.attr('heigth')||'auto',
		modal=($self.attr('modal')),
		method=$self.attr('method')||'get',
		buttons=$self.attr('button')||null;

		if(buttons) buttons=buttons.split('|').map(function(b){
			b=b.split(':');
			return {text:b[0], click:window[b[1]]};
		});
		
		$[method]($self.attr('href'), function(html){
			$(html).dialog({
				title:title,
				width:width,
				height:heigth,
				modal:true,
				buttons:buttons
			});
		});
		
		return false;
	});
	
	
	
	// A链接扩展
	/**
	 * AJAX链接
	 * target="ajax"：AJAX请求
	 * onajax：AJAX调用前触发，返回false时阻止
	 * call：AJAX完后触发，callback(err, data, xhr) this指向当前html元素，err当出错的时间有值，data为服务器返回值(解析过)，xhr为HttpRequest对象
	 * dataType：默认html，服务器响应类型，可用json，xml
	 */
	$('a[target=ajax]').bind('click', function(){
		var $this	= $(this),
		self		= this,
		title		= $this.attr('title');
		
		var $this=$(this);
		$this.closest('tr').find(':input[name]').each(function(){
			var $input=$(this);
			if($input.is(':radio, :checkbox') && this.checked==false) return true;
			$this.data($input.attr('name'), this.value);
		});
	
		if(title && !confirm(title)) return false;

		$.ajax({
			url:$this.attr('href'),
			
			// 异步请求
			async:true,
			
			// 把当前存储的数据做为参数传递
			data:$this.data(),
			
			// 默认用GET请求，也可以用method属性设置
			type:$this.attr('method')||'get',
			
			// dataType属性用于设置响应数据格式，默认html，可选json和xml
			dataType:'json',
			
			error:function(xhr, textStatus, errThrow){
				// 据jQuery官方说，textStatus和errThrow中只有一个包括错误信息
				//call.call(self, errThrow||textStatus);
				updateAlert(errThrow||textStatus,'alert-error');
								
				setTimeout(function(){
						$('#top-alert').find('button').click();
					},1000);
			},
			
			success:function(data2, textStatus, xhr, headers){
				//var errorMessage=xhr.getResponseHeader('X-Error-Message');
				var status = data2.status;
				if(status==0){
					updateAlert(data2.info,'alert-error');
				}else if(status==1){
					updateAlert(data2.info,'alert-success');
				}
				else{
					updateAlert(data2.info,'alert-error');
				}
				
				setTimeout(function(){
						if (data2.url) {
							location.href=data2.url;
						}else{
							$('#top-alert').find('button').click();
						}
					},1000);
			}
		});
		
		return false;
	});
	
	
	//全选的实现
	$(".check-all").click(function(){
		$(".ids").prop("checked", this.checked);
	});
	$(".ids").click(function(){
		var option = $(".ids");
		option.each(function(i){
			if(!this.checked){
				$(".check-all").prop("checked", false);
				return false;
			}else{
				$(".check-all").prop("checked", true);
			}
		});
	});

    //ajax get请求
    $('.ajax-get').click(function(){
        var target;
        var that = this;
        if ( $(this).hasClass('confirm') ) {
            if(!confirm('确认要执行该操作吗?')){
                return false;
            }
        }
        if ( (target = $(this).attr('href')) || (target = $(this).attr('url')) ) {
            $.get(target).success(function(data){
                if (data.status==1) {
                    if (data.url) {
                        updateAlert(data.info + ' 页面即将自动跳转~','alert-success');
                    }else{
                        updateAlert(data.info,'alert-success');
                    }
                    setTimeout(function(){
                        if (data.url) {
                            location.href=data.url;
                        }else if( $(that).hasClass('no-refresh')){
                            $('#top-alert').find('button').click();
                        }else{
                            location.reload();
                        }
                    },1000);
                }else{
                    updateAlert(data.info);
                    setTimeout(function(){
                        if (data.url) {
                            location.href=data.url;
                        }else{
                            $('#top-alert').find('button').click();
                        }
                    },1000);
                }
            });

        }
        return false;
    });

    //ajax post submit请求
    $('.ajax-post').click(function(){
        var target,query,form;
        var target_form = $(this).attr('target-form');
        var that = this;
        var nead_confirm=false;
        if( ($(this).attr('type')=='submit') || (target = $(this).attr('href')) || (target = $(this).attr('url')) ){
            form = $('.'+target_form);

            if ($(this).attr('hide-data') === 'true'){//无数据时也可以使用的功能
            	form = $('.hide-data');
            	query = form.serialize();
            }else if (form.get(0)==undefined){
            	return false;
            }else if ( form.get(0).nodeName=='FORM' ){
                if ( $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                if($(this).attr('url') !== undefined){
                	target = $(this).attr('url');
                }else{
                	target = form.get(0).action;
                }
                query = form.serialize();
            }else if( form.get(0).nodeName=='INPUT' || form.get(0).nodeName=='SELECT' || form.get(0).nodeName=='TEXTAREA') {
                form.each(function(k,v){
                    if(v.type=='checkbox' && v.checked==true){
                        nead_confirm = true;
                    }
                })
                if ( nead_confirm && $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                query = form.serialize();
            }else{
                if ( $(this).hasClass('confirm') ) {
                    if(!confirm('确认要执行该操作吗?')){
                        return false;
                    }
                }
                query = form.find('input,select,textarea').serialize();
            }
            $(that).addClass('disabled').attr('autocomplete','off').prop('disabled',true);
            $.post(target,query).success(function(data){
                if (data.status==1) {
                    if (data.url) {
                        updateAlert(data.info + ' 页面即将自动跳转~','alert-success');
                    }else{
                        updateAlert(data.info ,'alert-success');
                    }
                    setTimeout(function(){
                        if (data.url) {
                            location.href=data.url;
                        }else if( $(that).hasClass('no-refresh')){
                            $('#top-alert').find('button').click();
                            $(that).removeClass('disabled').prop('disabled',false);
                        }else{
							$(that).removeClass('disabled').prop('disabled',false);
                            location.reload();
                        }
                    },1500);
                }else{
                    updateAlert(data.info);
                    setTimeout(function(){
                        if (data.url) {
                            location.href=data.url;
                        }else{
                            $('#top-alert').find('button').click();
                            $(that).removeClass('disabled').prop('disabled',false);
                        }
                    },1500);
                }
            });
        }
        return false;
    });

	/**顶部警告栏*/
	var content = $('#main');
	var top_alert = $('#top-alert');
	top_alert.find('.close').on('click', function () {
		top_alert.removeClass('block').slideUp(200);
		// content.animate({paddingTop:'-=55'},200);
	});

    window.updateAlert = function (text,c) {
		text = text||'default';
		c = c||false;
		if ( text!='default' ) {
            top_alert.find('.alert-content').text(text);
			if (top_alert.hasClass('block')) {
			} else {
				top_alert.addClass('block').slideDown(200);
				// content.animate({paddingTop:'+=55'},200);
			}
		} else {
			if (top_alert.hasClass('block')) {
				top_alert.removeClass('block').slideUp(200);
				// content.animate({paddingTop:'-=55'},200);
			}
		}
		if ( c!=false ) {
            top_alert.removeClass('alert-error alert-warn alert-info alert-success').addClass(c);
		}
	};

    //按钮组
    (function(){
        //按钮组(鼠标悬浮显示)
        $(".btn-group").mouseenter(function(){
            var userMenu = $(this).children(".dropdown ");
            var icon = $(this).find(".btn i");
            icon.addClass("btn-arrowup").removeClass("btn-arrowdown");
            userMenu.show();
            clearTimeout(userMenu.data("timeout"));
        }).mouseleave(function(){
            var userMenu = $(this).children(".dropdown");
            var icon = $(this).find(".btn i");
            icon.removeClass("btn-arrowup").addClass("btn-arrowdown");
            userMenu.data("timeout") && clearTimeout(userMenu.data("timeout"));
            userMenu.data("timeout", setTimeout(function(){userMenu.hide()}, 100));
        });

        //按钮组(鼠标点击显示)
        // $(".btn-group-click .btn").click(function(){
        //     var userMenu = $(this).next(".dropdown ");
        //     var icon = $(this).find("i");
        //     icon.toggleClass("btn-arrowup");
        //     userMenu.toggleClass("block");
        // });
        $(".btn-group-click .btn").click(function(e){
            if ($(this).next(".dropdown").is(":hidden")) {
                $(this).next(".dropdown").show();
                $(this).find("i").addClass("btn-arrowup");
                e.stopPropagation();
            }else{
                $(this).find("i").removeClass("btn-arrowup");
            }
        })
        $(".dropdown").click(function(e) {
            e.stopPropagation();
        });
        $(document).click(function() {
            $(".dropdown").hide();
            $(".btn-group-click .btn").find("i").removeClass("btn-arrowup");
        });
    })();

    // 独立域表单获取焦点样式
    $(".text").focus(function(){
        $(this).addClass("focus");
    }).blur(function(){
        $(this).removeClass('focus');
    });
    $("textarea").focus(function(){
        $(this).closest(".textarea").addClass("focus");
    }).blur(function(){
        $(this).closest(".textarea").removeClass("focus");
    });
});

/* 上传图片预览弹出层 */
$(function(){
    $(window).resize(function(){
        var winW = $(window).width();
        var winH = $(window).height();
        $(".upload-img-box").click(function(){
        	//如果没有图片则不显示
        	if($(this).find('img').attr('src') === undefined){
        		return false;
        	}
            // 创建弹出框以及获取弹出图片
            var imgPopup = "<div id=\"uploadPop\" class=\"upload-img-popup\"></div>"
            var imgItem = $(this).find(".upload-pre-item").html();

            //如果弹出层存在，则不能再弹出
            var popupLen = $(".upload-img-popup").length;
            if( popupLen < 1 ) {
                $(imgPopup).appendTo("body");
                $(".upload-img-popup").html(
                    imgItem + "<a class=\"close-pop\" href=\"javascript:;\" title=\"关闭\"></a>"
                );
            }

            // 弹出层定位
            var uploadImg = $("#uploadPop").find("img");
            var popW = uploadImg.width();
            var popH = uploadImg.height();
            var left = (winW -popW)/2;
            var top = (winH - popH)/2 + 50;
            $(".upload-img-popup").css({
                "max-width" : winW * 0.9,
                "left": left,
                "top": top
            });
        });

        // 关闭弹出层
        $("body").on("click", "#uploadPop .close-pop", function(){
            $(this).parent().remove();
        });
    }).resize();

    // 缩放图片
    function resizeImg(node,isSmall){
        if(!isSmall){
            $(node).height($(node).height()*1.2);
        } else {
            $(node).height($(node).height()*0.8);
        }
    }
	
	var timeout1;
	//{{{系统提现提示
	if(typeof(TIP)!='undefined' && TIP){
		timeout1 = setInterval(function(){
			cash_tip();
		}, 10000);
	}
	
	function cash_tip(){
		clearInterval(timeout1);
		$.getJSON('/gygyhou.php?s=admin/business/getTip_cash', function(tip){
			if(tip.flag){
				// 只处理正确返回的数据
				playVoice(window.Think['PUBLIC'] + '/Admin/sound/cash.mp3', 'cash-voice');
				
				var buttons=[];
				tip.buttons.split('|').forEach(function(button){
					button=button.split(':');
					//buttons[button[0]]=window[button[1]];
					buttons.push({text:button[0], click:window[button[1]]});
				});
				
				if(tip.isDialog){
					$('<div>').append(tip.message).dialog({
						position:['right','bottom'],
						minHeight:40,
						title:'系统提示',
						buttons:buttons
					});
				}
			}
		})
		
		timeout1 = setInterval(function(){
			cash_tip();
		}, 10000);
	}
	
	var timeout2;
	//{{{系统充值提示
	if(typeof(TIP)!='undefined' && TIP){
		timeout2 = setTimeout(function(){
			recharge_tip();
		}, 10000);
	}
	
	function recharge_tip(){
		clearTimeout(timeout2);
		$.getJSON('/gygyhou.php?s=admin/business/getTip_recharge', function(tip){
			if(tip.flag){
				// 只处理正确返回的数据
				playVoice(window.Think['PUBLIC'] + '/Admin/sound/msg.mp3', 'recharge-voice');
				
				var buttons=[];
				tip.buttons.split('|').forEach(function(button){
					button=button.split(':');
					//buttons[button[0]]=window[button[1]];
					buttons.push({text:button[0], click:window[button[1]]});
				});
				if(tip.isDialog){
					$('<div>').append(tip.message).dialog({
						position:['right','bottom'],
						minHeight:40,
						title:'系统提示',
						buttons:buttons
					});
				}

			}
		})
		
		timeout2 = setTimeout(function(){
			recharge_tip();
		}, 10000);
	}
})


// 播放声音
function playVoice(src, domId){
	var $dom=$('#'+domId)
	if($.browser.msie){
		// IE用bgsound标签处理声音
		
		if($dom.length){
			$dom[0].src=src;
		}else{
			$('<bgsound>',{src:src, id:domId}).appendTo('body');
		}
	}else{
		// IE以外的其它浏览器用HTML5处理声音
		if($dom.length){
			$dom[0].play();
		}else{
			$('<audio>',{src:src, id:domId}).appendTo('body')[0].play();
		}
	}
}

//标签页切换(无下一步)
function showTab() {
    $(".tab-nav li").click(function(){
        var self = $(this), target = self.data("tab");
        self.addClass("current").siblings(".current").removeClass("current");
        window.location.hash = "#" + target.substr(3);
        $(".tab-pane.in").removeClass("in");
        $("." + target).addClass("in");
    }).filter("[data-tab=tab" + window.location.hash.substr(1) + "]").click();
}

//标签页切换(有下一步)
function nextTab() {
     $(".tab-nav li").click(function(){
        var self = $(this), target = self.data("tab");
        self.addClass("current").siblings(".current").removeClass("current");
        window.location.hash = "#" + target.substr(3);
        $(".tab-pane.in").removeClass("in");
        $("." + target).addClass("in");
        showBtn();
    }).filter("[data-tab=tab" + window.location.hash.substr(1) + "]").click();

    $("#submit-next").click(function(){
        $(".tab-nav li.current").next().click();
        showBtn();
    });
}

// 下一步按钮切换
function showBtn() {
    var lastTabItem = $(".tab-nav li:last");
    if( lastTabItem.hasClass("current") ) {
        $("#submit").removeClass("hidden");
        $("#submit-next").addClass("hidden");
    } else {
        $("#submit").addClass("hidden");
        $("#submit-next").removeClass("hidden");
    }
}

function defaultCloseModal(){
	$(this).dialog('destroy');
}

function dataAddCode(){
	$('form', this).trigger('submit');
	$(this).dialog('destroy');
}

