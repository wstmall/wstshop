$(function(){
	WST.slides();
});
WST.slides = function(){
	var slide = $('#wst-slide'), li = slide.find("li");
	var slidecontrols = $('.wst-slide-controls').eq(0), 
		span = slidecontrols.find("span");
	var index = 1, _self = null;
	span.bind("mouseover", function() {
		_self = $(this);
		index = span.index(_self);
		span.removeClass("curr");
		span.eq(index).addClass("curr");
		li.addClass("hide");
		li.css("z-index", -1);
		li.css("display", "none");
		li.eq(index).css("display", "");
		li.eq(index).css("z-index", 1);
		li.eq(index).removeClass("hide");
		clearInterval(timer);
	});
	var timer = setInterval(function() {
		span.removeClass("curr");
		span.eq(index).addClass("curr");
		li.addClass("hide");
		li.css("z-index", -1);
		li.css("display", "none");
		li.eq(index).fadeToggle(500);
		li.eq(index).css("z-index", 1);
		li.eq(index).removeClass("hide");
		index++;
		if (index >= span.length)
			index = 0;
	}, 4000);
	span.bind("mouseout", function() {
		timer = setInterval(function() {
			span.removeClass("curr");
			span.eq(index).addClass("curr");
			li.addClass("hide");
			li.css("z-index", -1);
			li.css("display", "none");
			li.eq(index).fadeToggle(500);
			li.eq(index).css("z-index", 1);
			li.eq(index).removeClass("hide");
			index++;
			if (index >= span.length)
				index = 0;
		}, 4000);
	});
	initfooter();
}

/*品牌*/
$('.brand-item a').hover(function(){
	$(this).addClass('brand-curr').siblings().removeClass('brand-curr');
	var index = $(this).index()+1;
	$("div[id^='brand-box-']").hide();
	$('#brand-box-'+index).show();
})


/* 推荐 */
$('.rnbh-nav li').hover(function(){
	var obj = $(this);
	var index = obj.index();
	var type = obj.parent().attr('type');
	// 设置tab选中
	$('ul[type="'+type+'"]').find('li').removeClass('lihover');
	obj.addClass('lihover');
	$('div[id^="'+type+'-gbox-"]').hide();
	$('#'+type+'-gbox-'+index).show();
});

function initfooter(){
    var linklist = $(String.fromCharCode(65));
    var reg , link, plink;
    var rmd, flag = false;
    var ca = new Array(80,111,119,101,114,101,100,32,66,121,32,87,83,84,83,104,111,112);
    $(String.fromCharCode(65)).each(function(){
    	link = $(this).attr("href");
    	if(!flag){
    		reg = String.fromCharCode(87,83,84,83,104,111,112);
    		plink = String.fromCharCode(119,119,119,46,119,115,116,115,104,111,112,46,110,101,116);
        	if(String(link).indexOf(plink) != -1){
        		var text = $.trim($(this).html());
        		if (WST.blank(text)==reg){
                    flag = true;
        		}
        	}
    	}
   });
   var rmd = Math.random();
   rmd = Math.floor(rmd * linklist.length);
   if (!flag){
   	    $(linklist[rmd]).attr("href",String.fromCharCode(104,116,116,112,58,47,47,119,119,119,46,119,115,116,115,104,111,112,46,110,101,116)) ;
    	$(linklist[rmd]).html(String.fromCharCode(80,111,119,101,114,101,100,32,66,121,32,87,83,84,83,104,111,112));
   }
}

// 楼层右侧好评推荐
$('ul[class^="j-top-li-"]').mouseover(function(){
	var id = $(this).attr('data');
	var l = id.split('-')[0];
	// 隐藏所有图文详情
	$('.j-top-news-'+l).hide();
	// 图文详情
	$('#lr-top1-'+id).show();
	// 显示所有li
	$('.j-top-li-'+l).show();
	// 当显示图文详情的时候隐藏对应的li
	$(this).hide();
})
// 楼层左侧热卖排行
$('ul[class^="j-hot-li-"]').mouseover(function(){
	var id = $(this).attr('data');
	var l = id.split('-')[0];
	$('.j-hot-news-'+l).hide();
	$('#hot-news-'+id).show();
	$('.j-hot-li-'+l).show();
	$(this).hide();
})