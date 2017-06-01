$(function(){
	$('.h-cat>span').click(function(t){
		var li = $(this).parent();
		if( li.find('ul').is(":hidden") ){
			li.addClass('h-show').removeClass('h-hide');
			li.siblings().addClass('h-hide').removeClass('h-show');
		}else{
			li.addClass('h-hide').removeClass('h-show');
		}
		
	});
});
