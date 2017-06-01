$(function(){
	$('.goodsImg2').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 100,placeholder:window.conf.ROOT+'/'+window.conf.GOODS_LOGO});//商品默认图片
	WST.dropDownLayer(".item",".dorp-down-layer");
	$('.item-more').click(function(){
		if($(this).attr('v')==1){
			$('.hideItem').show(300);
			$(this).find("span").html("收起");
			$(this).find("i").attr({"class":"drop-up"});
			$(this).attr('v',0);
		}else{
			$('.hideItem').hide(300);
			$(this).find("span").html("更多选项");
			$(this).find("i").attr({"class":"drop-down-icon"});
			$(this).attr('v',1);
		}
	});
	
	$(".item-more").hover(function(){
		if($(this).find("i").hasClass("drop-down-icon")){
			$(this).find("i").attr({"class":"down-hover"});
		}else{
			$(this).find("i").attr({"class":"up-hover"});
		}
		
	},function(){
		if($(this).find("i").hasClass("down-hover")){
			$(this).find("i").attr({"class":"drop-down"});
		}else{
			$(this).find("i").attr({"class":"drop-up"});
		}
	});
});

function goodsFilter(obj,vtype){
	if(vtype==1){
		$('#brand').val($(obj).attr('v'));
	}else if(vtype==2){
		var price = $(obj).attr('v');
		price = price.split('_');
		$('#sprice').val(price[0]);
		$('#eprice').val(price[1]);
	}else if(vtype==3){
		$('#v_'+$(obj).attr('d')).val($(obj).attr('v'));
		var vs = $('#vs').val();
		vs = (vs!='')?vs.split(','):[];
		vs.push($(obj).attr('d'));
		$('#vs').val(vs.join(','));
	}
	var ipts = WST.getParams('.sipt');
	if(vtype==4)ipts['order']='1';
	var params = [];
	for(var key in ipts){
		if(ipts[key]!='')params.push(key+"="+ipts[key]);
	}
	location.href=WST.U('home/goods/lists',params.join('&'));
}
function goodsOrder(orderby){
	if($('#orderBy').val()!=orderby){
		$('#order').val(1);
	}
	$('#orderBy').val(orderby);
	goodsFilter(null,0);
}
/*筛选条件下搜索商品名称*/
function fsGoods(){
	$('#goodsName').val($('#sGoodsName').val());
	goodsOrder();
}



function removeFilter(id){
	if(id!='price'){
		$('#'+id).val('');
		if(id.indexOf('v_')>-1){
			id = id.replace('v_','');
			var vs = $('#vs').val();
			vs = (vs!='')?vs.split(','):[];
			var nvs = [];
			for(var i=0;i<vs.length;i++){
				if(vs[i]!=id)nvs.push(vs[i]);
			}
			$('#vs').val(nvs.join(','));
		}
	}else{
		$('#sprice').val('');
		$('#eprice').val('');
	}
	var ipts = WST.getParams('.sipt');
	var params = [];
	for(var key in ipts){
		if(ipts[key]!='')params.push(key+"="+ipts[key]);
	}
	location.href=WST.U('home/goods/lists',params.join('&'));
}
/*搜索列表*/
function searchFilter(obj,vtype){
	if(vtype==1){
		$('#brand').val($(obj).attr('v'));
	}else if(vtype==2){
		var price = $(obj).attr('v');
		price = price.split('_');
		$('#sprice').val(price[0]);
		$('#eprice').val(price[1]);
	}else if(vtype==3){
		$('#v_'+$(obj).attr('d')).val($(obj).attr('v'));
		var vs = $('#vs').val();
		vs = (vs!='')?vs.split(','):[];
		vs.push($(obj).attr('d'));
		$('#vs').val(vs.join(','));
	}
	var ipts = WST.getParams('.sipt');
	if(vtype==4)ipts['order']='1';
	var params = [];
	for(var key in ipts){
		if(ipts[key]!='')params.push(key+"="+ipts[key]);
	}
	location.href=WST.U('home/goods/search',params.join('&'));
}
function searchOrder(orderby){
	if($('#orderBy').val()!=orderby){
		$('#order').val(1);
	}
	$('#orderBy').val(orderby);
	searchFilter(null,0);
}


/*加入购物车*/
$('.goods').hover(function(){
	$(this).find('.sale-num').hide();
	$(this).find('.p-add-cart').show();
},function(){
	$(this).find('.sale-num').show();
	$(this).find('.p-add-cart').hide();
})


