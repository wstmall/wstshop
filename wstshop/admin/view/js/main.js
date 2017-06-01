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
			   $('#wstwstshop_down').attr('href',json.downloadUrl);
		   }
		   if(json.accredit=='no'){
			   $('#wstshop-accredit-tips').show();
		   }
		   if(json.licenseStatus)$('#licenseStatus').html(json.licenseStatus);
	   }
	});
}
$(function(){
    getLastVersion();
})