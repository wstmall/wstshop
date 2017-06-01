var grid;
function initGrid(){
	grid = $('#maingrid').WSTGridTree({
		url:WST.U('admin/freights/pageQuery'),
		rownumbers:true,
        columns: [
	        { display: '地区名称', name: 'areaName', id:'areaId', align: 'left',isSort: false},
	        { display: '运费', name: 'freight',isSort: false,width:200,render: function (rowdata, rowindex, value){
	        	var html = [];
	        	if(rowdata['areaType']==0) {
                    html.push('<div ondblclick="javascript:toEditFreights(0,'+rowdata['areaId']+',1)">');
                    html.push('<input class="ipt_0_'+rowdata['areaId']+'" onkeyup="javascript:WST.isChinese(this,1)" onkeypress="return WST.isNumberKey(event)" onblur="javascript:editFreights(0,'+rowdata['areaId']+',1)" style="display:none;width:100%;border:1px solid red;width:40px;" maxlength="6"/>');
                    html.push('<span class="span_0_'+rowdata['areaId']+'" style="display: inline;cursor:pointer;color:green;">双击可批量修改</span>');
                }else{
                	if(typeof(rowdata['freight'])=='object'){
                		rowdata['freight'] = $('#deFreight').val();
                	}
                    html.push('<div ondblclick="javascript:toEditFreights(1,'+rowdata['areaId']+',\'\')">');
                    html.push('<input class="ipt_1_'+rowdata['areaId']+' ipt" id="'+rowdata['areaId']+'"  value="'+rowdata['freight']+'" onkeyup="javascript:WST.isChinese(this,1)" onkeypress="return WST.isNumberKey(event)" onblur="javascript:editFreights(1,'+rowdata['areaId']+')" style="display:none;width:100%;border:1px solid red;width:40px;" maxlength="6"/>');
                    html.push('<span class="span_1_'+rowdata['areaId']+'" style="display: inline;cursor:pointer;color:green;">'+rowdata['freight']+'</span>');
                }
                html.push('</div>');
                return html.join('');
	        }},
        ]
	});
}

//双击修改
function toEditFreights(fv,id,flag){
	if(fv==0 && flag==1){
		$(".ipt_"+fv+"_"+id).show();
		$(".span_"+fv+"_"+id).hide();
		$(".ipt_"+fv+"_"+id).focus();
		return;
	}else{
		$(".ipt_"+fv+"_"+id).show();
		$(".span_"+fv+"_"+id).hide();
		$(".ipt_"+fv+"_"+id).focus();
		$(".ipt_"+fv+"_"+id).val($(".span_"+fv+"_"+id).html());
	}
}

function endEditFreights(fv,id){
	$('.span_'+fv+'_'+id).html($('.ipt_'+fv+'_'+id).val());
	$('.span_'+fv+'_'+id).show();
    $('.ipt_'+fv+'_'+id).hide();
}

function editFreights(fv,id,flag){
	var params = {};
	if(fv==0 && flag==1){
		var vtext = $('.ipt_'+fv+'_'+id).val();
		params.vtext = vtext;
		params.areaId = id;
	}
	params.list = WST.getParams('.ipt');
	$.post(WST.U('admin/freights/edit'),params,function(data,textStatus){
		var json = WST.toAdminJson(data);
		if(json.status>0){
			if(fv==0 && flag==1 && id>0){
				grid.reload(id);
				endEditFreights(fv,id);
			}else if(fv==0 && flag==1 && id==-1){
				grid.reload();
			}else{
				endEditFreights(fv,id);
			}
	        WST.msg(json.msg,{icon:1});
		}else{
			WST.msg(json.msg, {icon: 5}); 
		}
	});
}