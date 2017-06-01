$(window).resize(function(){
	var h = WST.pageHeight()-100;
    $('.l-tab-content').height(h);
    $('.l-tab-content-item').height(h);
    $('.wst-iframe').each(function(){
    	$(this).height(h);
    });
    $('.wst-accordion').each(function(){
    	liger.get($(this).attr('id')).setHeight(h-26);
    });
});
function changeTab(obj,n){
	$('.wst-link-selected').removeClass('wst-link-selected');
	$(obj).addClass('wst-link-selected');
    $('#wst-lframe-'+n).attr('src',$(obj).attr('url'));
}
// 获取定时任务
function getSysMsg(task){
	$.post(WST.U('admin/index/getSysMsg'),{task:task},function(data){
		var json = WST.toAdminJson(data);
		if(WST.blank(json.order)){
			for(key in json.order){
				var obj = $('#'+key).hide();
				if(json.order[key]>0){
					obj.show();
					obj.html(json.order[key]);
				}
			}
		}else if(WST.blank(json.warnStock)){
			var obj = $('#warnStock');
			if(json.warnStock>0){
				obj.show();
				obj.html(json.warnStock);
			}
		}

	})
}
function initTabMenus(menuId){
	var order = '';
	if(menuId==35 || menuId==22){order='style="position:relative;"'}

	$.post(WST.U('admin/index/getSubMenus'),{id:menuId},function(data,textStatus){
		 var json = WST.toAdminJson(data);
		 var html = [];
		 html.push('<div id="wst-layout-'+menuId+'" style="width:99.2%; margin:0 auto; margin-top:4px; ">'); 
		 html.push('<div position="left" id="wst-accordion-'+menuId+'" title="管理菜单" class="wst-accordion">');
		 if(json && json.length>0){
			 for(var i=0;i<json.length;i++){
       		 html.push('<div title="'+json[i]['menuName']+'">'); 
       		 html.push('     <div style=" height:7px;"></div>');
       		 if(json[i]['list']){
	        		 for(var j=0;j<json[i]['list'].length;j++){
		        		 html.push('<a '+order+' class="wst-link" href="javascript:void(0)" url="'+WST.U(WST.blank(json[i]['list'][j]['privilegeUrl'],''))+'" onclick="javascript:changeTab(this,'+menuId+')">'+json[i]['list'][j]['menuName']);  
		        		 if(menuId==35){
		        		 	if(WST.blank(json[i]['list'][j]['privilegeUrl'],'').indexOf('waitDelivery')!=-1)html.push('<span id="waitDelivery" class="orderStatus">0</span>')
		        		 	if(WST.blank(json[i]['list'][j]['privilegeUrl'],'').indexOf('waitPay')!=-1)html.push('<span id="waitPay" class="orderStatus">0</span>')
		        		 	if(WST.blank(json[i]['list'][j]['privilegeUrl'],'').indexOf('refund')!=-1)html.push('<span id="refund" class="orderStatus">0</span>')
		        		 }else if(menuId==22){
		        		 	if(WST.blank(json[i]['list'][j]['privilegeUrl'],'').indexOf('warn')!=-1)html.push('<span id="warnStock" class="orderStatus">0</span>')
		        		 }
		        		 html.push('</a>');
	        		 }
       		 }
       		 html.push('     </div> ');
			 }
		 }
		 html.push('</div>');
		 html.push('<div id="wst-ltabs-'+menuId+'" position="center" class="wst-lnavtabs">'); 
		 html.push('  <div tabid="wst-ltab-'+menuId+'" title="我的主页" style="display:none;height:30px;line-height:30px;padding-left:5px;background:#EAF5FB;"></div>');
		 html.push('  <iframe frameborder="0" class="wst-iframe" id="wst-lframe-'+menuId+'" src="'+(initFrame?"":WST.U('admin/index/main'))+'"></iframe>');
		 html.push('  ');
		 html.push('</div>'); 
		 html.push('</div>');
		 initFrame = true;
		 $('#wst-tab-'+menuId).html(html.join(''));

		 // 触发定时任务
		 if(menuId==35){
		 	// 订单数量提示
		 	getSysMsg('order');setInterval(function(){getSysMsg('order')},5000)
		 }else if(menuId==22){
		 	// 库存预警
		 	getSysMsg('warnStock');setInterval(function(){getSysMsg('warnStock')},10000)
		 }

		 $("#wst-layout-"+menuId).ligerLayout({
	         leftWidth: 190,
	         height: '100%',
	         space: 0
	     });
		 var height = $(".l-layout-center").height();
		 $("#wst-accordion-"+menuId).ligerAccordion({
		      height: height - 25, speed: null
		 });
		 if(initFrame)$('.l-tab-loading').remove();
	 });
}
var mMgrs = {},tab,initFrame = false;
$(function (){   
    tab = $("#wst-tabs").ligerTab({
         height: '100%',
         changeHeightOnResize:true,
         showSwitchInTab : false,
         showSwitch: false,
         onAfterSelectTabItem:function(n){
        	 var menuId = n.replace('wst-tab-','');
        	 if(!mMgrs['m'+menuId]){
	        	 var ltab = $("#wst-tab-"+menuId);
	        	 mMgrs['m'+menuId] = true;
	        	 if(menuId=='market'){
	        		 $('#wst-market').attr('src','http://market.shangtaosoft.com');
	        	 }else{
	        	     initTabMenus(menuId);
        	     }
        	 }
         }
    });
    var tabId = tab.getSelectedTabItemID();
    mMgrs['m'+tabId.replace('wst-tab-','')] = true;
    $('#wst-tabs .l-tab-links > ul').css('left','195px');
    initTabMenus(tabId.replace('wst-tab-',''));
    $('.l-tab-content').height(WST.pageHeight()-65);
    $('.l-tab-content-item').height(WST.pageHeight()-65);
    $('.wst-iframe').each(function(){
    	$(this).height(h);
    });
});
function getLastVersion(){
	$.post(WST.U('admin/index/getVersion'),{},function(data,textStatus){
		var json = {};
		try{
	      if(typeof(data )=="object"){
			  json = data;
	      }else{
			  json = eval("("+data+")");
	      }
		}catch(e){}
	    if(json){
		   if(json.version && json.version!='same'){
			   $('#wstshop-version-tips').show();
			   $('#wstshop_version').html(json.version);
			   $('#wstshop_down').attr('href',json.downloadUrl);
		   }
		   if(json.accredit=='no'){
			   $('#wstshop-accredit-tips').show();
		   }
		   if(json.licenseStatus)$('#licenseStatus').html(json.licenseStatus);
	   }
	});
}
function logout(){
	WST.confirm({content:"您确定要退出该系统吗?",yes:function(){
		var loading = WST.msg('正在退出，请稍后...', {icon: 16,time:60000});
		$.post(WST.U('admin/index/logout'),WST.getParams('.ipt'),function(data,textStatus){
			layer.close(loading);
			var json = WST.toAdminJson(data);
			if(json.status=='1'){
				location.reload();
			}
		});
	}});
}
function clearCache(){
	var loading = WST.msg('正在清理缓存，请稍后...', {icon: 16,time:60000});
	$.post(WST.U('admin/index/clearcache'),{},function(data,textStatus){
		layer.close(loading);
		var json = WST.toAdminJson(data);
		if(json.status && json.status=='1'){
			WST.msg(json.msg,{icon:1});
		}else{
			WST.msg(json.msg,{icon:2});
		}
	});
}
function editPassBox(){
	var w = WST.open({type: 1,title:"修改密码",shade: [0.6, '#000'],border: [0],content:$('#editPassBox'),area: ['450px', '250px'],
	    btn: ['确定', '取消'],yes: function(index, layero){
	    	$('#editPassFrom').isValid(function(v){
	    		if(v){
		        	var params = WST.getParams('.ipt');
		        	var ll = WST.msg('数据处理中，请稍候...');
				    $.post(WST.U('admin/Staffs/editMyPass'),params,function(data){
				    	layer.close(ll);
				    	var json = WST.toAdminJson(data);
						if(json.status==1){
							WST.msg(json.msg, {icon: 1});
							layer.close(w);
						}else{
							WST.msg(json.msg, {icon: 2});
						}
				   });
	    		}})
        }
	});
}