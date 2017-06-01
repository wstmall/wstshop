//关注的商品列表
function freGoodsList(pages){
	var param = {};
	param.pagesize = 15;
	param.page = pages;
    $.post(WST.U('home/favorites/listGoodsQuery'),param,function(data){
        var json = WST.toJson(data);
        if(json.status==1){
        	json = json.data;
	        var gettpl = document.getElementById('list').innerHTML;
	        laytpl(gettpl).render(json.Rows, function(html){
	            $('#list-goods').html(html);
	        });
	        if(json.TotalPage>1){
	            laypage({
	               cont: 'goodsPage',
	               pages:json.TotalPage, 
	               curr: json.CurrentPage,
	               skip: true, //是否开启跳页
	               skin: '#f46442',
	               groups: 3,
	               prev: '<<',
	               next: '>>',
	               jump: function(e, first){
	                    if(!first){
	                    	freGoodsList(e.curr);
	                    }
	                  } 
	            });
	        }else{
	            $('#goodsPage').empty();
	        }
	    	$(".wst-fav-goods").hover(function(){
	    		$(this).find(".js-operate").slideDown(100);
	    	},function(){
	    		$(this).find(".js-operate").slideUp(100);
	    	});
	    	$('.goodsImg2').lazyload({ effect: "fadeIn",failurelimit : 10,skip_invisible : false,threshold: 200,placeholder:window.conf.ROOT+'/'+window.conf.GOODS_LOGO});//商品默认图片
        }
    });
}
function getGoods(id){
	location.href=WST.U('home/goods/detail','id='+id);
}
function cancelFavorite(id,type){
	WST.confirm({content:"您确定要取消关注吗？", yes:function(tips){
	    var load = WST.load({msg:'请稍后...'});
		var param = {};
		param.id = id;
		param.type = type;
	    $.post(WST.U('home/favorites/cancel'),param,function(data,textStatus){
	      layer.close(load);
	      var json = WST.toJson(data);
	      if(json.status=='1'){
	        WST.msg(json.msg,{icon:1},function(){
	        	if(type==0){
	        		freGoodsList();
	        	}else{
	        		freShopList();
	        	}
	        	
	        });
	      }else{
	        WST.msg(json.msg,{icon:5});
	      }
	    });
	}});
}
