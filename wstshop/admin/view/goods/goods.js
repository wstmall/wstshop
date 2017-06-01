/**删除批量上传的图片**/
function delBatchUploadImg(obj){
	var c = WST.confirm({content:'您确定要删除商品图片吗?',yes:function(){
		$(obj).parent().remove("li");
		layer.close(c);
	}});
}
function lastGoodsCatCallback(opts){
	if(opts.isLast){
	    getSpecAttrs(opts.val);
	}else{
		$('#specsAttrBox').empty();
	}
}
/**初始化**/

function initEdit(){
	var initBatchUpload = false;
	var h = WST.pageHeight();
    $('.l-tab-content').height(h-32);
    $('.l-tab-content-item').height(h-32);
    $('.l-tab-content-item').css('overflow-y','auto');
    var tab = $("#wst-tabs").ligerTab({
         height: '99%',
         changeHeightOnResize:true,
         showSwitchInTab : false,
         showSwitch: false,
         onAfterSelectTabItem:function(n){
            if(n=='wst-tab-2'){
              $('.j-specImg').children().each(function(){
				if(!$(this).hasClass('webuploader-pick'))$(this).css({width:'80px',height:'25px'});
			  });
            }else if(n=='wst-tab-3' && !initBatchUpload){
           	    initBatchUpload = true;
			    var uploader = batchUpload({uploadPicker:'#batchUpload',uploadServer:WST.U('home/index/uploadPic'),formData:{dir:'goods',isWatermark:1,isThumb:1},uploadSuccess:function(file,response){
					var json = WST.toAdminJson(response);
					if(json.status==1){
						$li = $('#'+file.id);
						$li.append('<input type="hidden" class="j-gallery-img" iv="'+json.savePath + json.thumb+'" v="' +json.savePath + json.name+'"/>');
						//$li.append('<span class="btn-setDefault">默认</span>' );
		                var delBtn = $('<span class="btn-del">删除</span>');
		                $li.append(delBtn);
		                delBtn.on('click',function(){
		                	delBatchUploadImg($(this),function(){
		                		uploader.removeFile(file);
		        				uploader.refresh();
		                	});
		    			});
		                $('.filelist li').css('border','1px solid rgb(59, 114, 165)');
					}else{
						WST.msg(json.msg,{icon:2});
					}
			    }});
           }
           $('.btn-del').click(function(){
			delBatchUploadImg($(this),function(){
        		$(this).parent().remove();
           });
		})
         }
    });
	WST.upload({
	  	  pick:'#goodsImgPicker',
	  	  formData: {dir:'goods',isWatermark:1,isThumb:1},
	  	  accept: {extensions: 'gif,jpg,jpeg,bmp,png',mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif'},
	  	  callback:function(f){
	  		  var json = WST.toAdminJson(f);
	  		  if(json.status==1){
	  			  $('#uploadMsg').empty().hide();
	              $('#preview').attr('src',WST.conf.ROOT+"/"+json.savePath+json.thumb);
	              $('#goodsImg').val(json.savePath+json.name);
	              $('#msg_goodsImg').hide();
	  		  }
		  },
		  progress:function(rate){
		      $('#uploadMsg').show().html('已上传'+rate+"%");
		  }
	});
	KindEditor.ready(function(K) {
		editor1 = K.create('textarea[name="goodsDesc"]', {
		  height:'350px',
		  width:'800px',
		  uploadJson : WST.conf.ROOT+'/home/goods/editorUpload',
		  allowFileManager : false,
		  allowImageUpload : true,
		  items:[
			          'source', '|', 'undo', 'redo', '|', 'preview', 'print', 'template', 'code', 'cut', 'copy', 'paste',
			          'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
			          'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
			          'superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'fullscreen', '/',
			          'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
			          'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|','image','multiimage','table', 'hr', 'emoticons', 'baidumap', 'pagebreak',
			          'anchor', 'link', 'unlink', '|', 'about'
		  ],
		  afterBlur: function(){ this.sync(); }
		});
	});
	if(OBJ.goodsId>0){
		var goodsCatIds = OBJ.goodsCatIdPath.split('_');
		getBrands('brandId',goodsCatIds[0],OBJ.brandId);
		if(goodsCatIds.length>1){
			var objId = goodsCatIds[0];
			$('#cat_0').val(objId);
			var opts = {id:'cat_0',val:goodsCatIds[0],childIds:goodsCatIds,className:'j-goodsCats',afterFunc:'lastGoodsCatCallback'}
        	WST.ITSetGoodsCats(opts);
	    }
	}
	
}
/**获取品牌**/
function getBrands(objId,catId,objVal){
	$('#'+objId).empty();
	$.post(WST.U('admin/brands/listQuery'),{catId:catId},function(data,textStatus){
	     var json = WST.toAdminJson(data);
	     var html = [],cat;
	     html.push("<option value='' >-请选择-</option>");
	     if(json.status==1 && json.list){
	    	 json = json.list;
			 for(var i=0;i<json.length;i++){
			     cat = json[i];
			     html.push("<option value='"+cat.brandId+"' "+((objVal==cat.brandId)?"selected":"")+">"+cat.brandName+"</option>");
			 }
	     }
	     $('#'+objId).html(html.join(''));
	});
}
/**保存商品数据**/
function save(){
	$('#editform').isValid(function(v){
		if(v){
			var params = WST.getParams('.j-ipt');
			params.goodsCatId = WST.ITGetGoodsCatVal('j-goodsCats');
			params.specNum = specNum;
			var specsName,specImg;
			$('.j-speccat').each(function(){
				specsName = 'specName_'+$(this).attr('cat')+'_'+$(this).attr('num');
				specImg = 'specImg_'+$(this).attr('cat')+'_'+$(this).attr('num');
				if($(this)[0].checked){
					params[specsName] = $.trim($('#'+specsName).val());
					params[specImg] = $.trim($('#'+specImg).attr('v'));
				}
			});
			var gallery = [];
			$('.j-gallery-img').each(function(){
				gallery.push($(this).attr('v'));
			});
			params.gallery = gallery.join(',');




			var loading = WST.msg('正在提交数据，请稍后...', {icon: 16,time:60000});
		    $.post(WST.U('admin/goods/'+((params.goodsId==0)?"add":"edit")),params,function(data,textStatus){
		    	layer.close(loading);
		    	var json = WST.toAdminJson(data);
		    	if(json.status=='1'){
		    		WST.msg(json.msg,{icon:1});
		    		location.href=WST.U('admin/goods/'+src);
		    	}else{
		    		WST.msg(json.msg,{icon:2});
		    	}
		    });
		}
	});
}
var id2SepcNumConverter = {};





/**获取商品属性**/
function getSpecAttrs(goodsCatId){
	$('#specsAttrBox').empty();
	specNum = 0;
	$.post(WST.U('admin/goods/getSpecAttrs'),{goodsCatId:goodsCatId},function(data,textStatus){
		var json = WST.toAdminJson(data);
		if(json.status==1 && json.data){
			var html = [],tmp,str;
			if(json.data.attrs){
				html.push('<div class="spec-head">商品属性</div>');
				html.push('<div class="spec-body">');
				html.push('<table class="attr-table">');
				for(var i=0;i<json.data.attrs.length;i++){
					tmp = json.data.attrs[i];
					html.push('<tr><th width="120" nowrap>'+tmp.attrName+'：</th><td>');
					if(tmp.attrType==1){		
						str = tmp.attrVal.split(',');
						for(var j=0;j<str.length;j++){
						    html.push('<label><input type="checkbox" class="j-ipt" name="attr_'+tmp.attrId+'" value="'+str[j]+'"/>'+str[j]+'</label>');
						}
					}else if(tmp.attrType==2){
						html.push('<select name="attr_'+tmp.attrId+'" id="attr_'+tmp.attrId+'" class="j-ipt">');
						html.push('<option value="0">请选择</option>');
						str = tmp.attrVal.split(',');
						for(var j=0;j<str.length;j++){
							html.push('<option value="'+str[j]+'">'+str[j]+'</option>');
						}
						html.push('</select>');
					}else{
						html.push('<input type="text" name="attr_'+tmp.attrId+'" id="attr_'+tmp.attrId+'" class="spec-sale-text j-ipt"/>');
					}
					html.push('</td></tr>');
				}
				html.push('</table>');
				html.push('</div>');
			}
			$('#specsAttrBox').html(html.join(''));
			//如果是编辑的话，第一次要设置之前设置的值
			if(OBJ.goodsId>0 && specNum==0){
				//设置商品属性值
				var tmp = null;
				if(OBJ.attrs.length){
					for(var i=0;i<OBJ.attrs.length;i++){
						if(OBJ.attrs[i].attrType==1){
							tmp = OBJ.attrs[i].attrVal.split(',');
							WST.setValue("attr_"+OBJ.attrs[i].attrId,tmp);
						}else{
						    WST.setValue("attr_"+OBJ.attrs[i].attrId,OBJ.attrs[i].attrVal);
						}
					}
				}
				
			}
		}
	});
}
function edit(goodsId,src){
	location.href = WST.U('admin/goods/toEdit','id='+goodsId+"&src="+src);
}
function add(src){
	location.href = WST.U('admin/goods/toAdd','src='+src);
}
var grid;
function goodsStatus(goodsId,s,statusVal){
	return "<div status='"+statusVal+"' title='双击可修改' ondblclick='changSaleStatus(\""+s+"\",this," + goodsId + ")' class='w-r "+((statusVal==1)?'right':'wrong')+"'></div>";
}
function initSaleGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/goods/saleByPage'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        rowHeight:65,
        minColToggle:6,
        rownumbers:true,
        columns: [
            { display: "&nbsp;<input id='all' type='checkbox' class='chk' onclick='javascript:WST.checkChks(this,\".chk\")'>", name: 'goodsId',width:25,align:'left',heightAlign:'left',isSort: false,render: function (rowdata, rowindex, value){
            	return "<div class='goods-valign-m'><input type='checkbox' class='chk' value='"+rowdata['goodsId']+"'></div>";
            }},
            { display: '&nbsp;', name: 'goodsName',width:60,align:'left',heightAlign:'left',isSort: false,render: function (rowdata, rowindex, value){
            	return "<img style='height:60px;width:60px;' src='"+WST.conf.ROOT+"/"+rowdata['goodsImg']+"'>";
            }},
	        { display: '商品名称', name: 'goodsName',heightAlign:'left',isSort: false,render: function (rowdata, rowindex, value){
	            return rowdata['goodsName'];
	        }},
	        { display: '商品编号', name: 'goodsSn',isSort: false,render: function (rowdata, rowindex, value){
	        	return "<div class='goods-valign-m'>"+rowdata['goodsSn']+"</div>";
	        }},
	        { display: '价格', name: 'shopPrice',isSort: false,width:80,render: function (rowdata, rowindex, value){
	        	var html = [];
	        		html.push('<div class="goods-valign-m" ondblclick="javascript:toEditGoodsBase(2,'+rowdata['goodsId']+',\'\')">');
                    html.push('<input id="ipt_2_'+rowdata['goodsId']+'" onkeyup="javascript:WST.isChinese(this,1)" onkeypress="return WST.isNumberKey(event)" onblur="javascript:editGoodsBase(2,'+rowdata['goodsId']+')" style="display:none;width:98%;border:1px solid red;" maxlength="6"/>');
                    html.push('<span id="span_2_'+rowdata['goodsId']+'" style="display: inline;cursor:pointer;color:green;">'+rowdata['shopPrice']+'</span>');

                html.push('</div>');
                return html.join('');
	        }},
	        { display: '推荐', name: 'isRecom',isSort: false,width:50,render: function (rowdata, rowindex, value){
	        	return goodsStatus(rowdata['goodsId'],'r',rowdata['isRecom']);
	        }},
	        { display: '精品', name: 'isBest',isSort: false,width:50,render: function (rowdata, rowindex, value){
	        	return goodsStatus(rowdata['goodsId'],'b',rowdata['isBest']);
	        }},
	        { display: '新品', name: 'isNew',isSort: false,width:50,render: function (rowdata, rowindex, value){
	        	return goodsStatus(rowdata['goodsId'],'n',rowdata['isNew']);
	        }},
	        { display: '热销', name: 'isHot',isSort: false,width:50,render: function (rowdata, rowindex, value){
	        	return goodsStatus(rowdata['goodsId'],'h',rowdata['isHot']);
	        }},
	        { display: '销量', name: 'saleNum',isSort: false,width:80,render: function (rowdata, rowindex, value){
	        	return "<div class='goods-valign-m'>"+rowdata['saleNum']+"</div>";
	        }},
	        { display: '库存', name: 'goodsStock',isSort: false,width:80,render: function (rowdata, rowindex, value){
	        	var html = [];
	        		html.push('<div class="goods-valign-m" ondblclick="javascript:toEditGoodsBase(3,'+rowdata['goodsId']+',\'\')">');
                    html.push('<input id="ipt_3_'+rowdata['goodsId']+'" onkeyup="javascript:WST.isChinese(this,1)" onkeypress="return WST.isNumberKey(event)" onblur="javascript:editGoodsBase(3,'+rowdata['goodsId']+')" style="display:none;width:98%;border:1px solid red;" maxlength="6"/>');
                    html.push('<span id="span_3_'+rowdata['goodsId']+'" style="display: inline;cursor:pointer;color:green;">'+rowdata['goodsStock']+'</span>');

                html.push('</div>');
                return html.join('');
	        }},
	        { display: '操作', name: 'op',isSort: false,width:110,render: function (rowdata, rowindex, value){
	            var h = "";
	            h += "<div class='goods-valign-m'><a target='_blank' href='"+WST.U("home/goods/detail","id="+rowdata['goodsId'])+"'>查看</a> ";
	            if(WST.GRANT.ZSSP_02)h += "<a href='javascript:edit(" + rowdata['goodsId'] + ",\"index\")'>编辑</a> ";
	            if(WST.GRANT.ZSSP_03)h += "<a href='javascript:del(" + rowdata['goodsId'] + ",\"sale\")'>删除</a></div> "; 
	            return h;
	        }}
        ]
    });
}
function loadSaleGrid(){
	var params = WST.getParams('.j-ipt');
	params.goodsCatIdPath = WST.ITGetAllGoodsCatVals('cat_0','pgoodsCats').join('_');
	grid.set('url',WST.U('admin/goods/saleByPage',params));
}
function initStoreGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/goods/storeByPage'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        rowHeight:65,
        minColToggle:6,
        rownumbers:true,
        columns: [
            { display: "&nbsp;<input id='all' type='checkbox' class='chk' onclick='javascript:WST.checkChks(this,\".chk\")'>", name: 'goodsId',width:25,align:'left',heightAlign:'left',isSort: false,render: function (rowdata, rowindex, value){
            	return "<div class='goods-valign-m'><input type='checkbox' class='chk' value='"+rowdata['goodsId']+"'></div>";
            }},
            { display: '&nbsp;', name: 'goodsName',width:60,align:'left',heightAlign:'left',isSort: false,render: function (rowdata, rowindex, value){
            	if(rowdata['goodsImg']){
            		goodsImg = rowdata['goodsImg'];
            	}else{
            		goodsImg = goodsLogo;
            	}
            	return "<img style='height:60px;width:60px;' src='"+WST.conf.ROOT+"/"+goodsImg+"'>";
            }},
	        { display: '商品名称', name: 'goodsName',heightAlign:'left',isSort: false,render: function (rowdata, rowindex, value){
	            return rowdata['goodsName'];
	        }},
	        { display: '商品编号', name: 'goodsSn',isSort: false,render: function (rowdata, rowindex, value){
	        	return "<div class='goods-valign-m'>"+rowdata['goodsSn']+"</div>";
	        }},
	        { display: '价格', name: 'shopPrice',isSort: false,width:80,render: function (rowdata, rowindex, value){
	        	var html = [];
	        		html.push('<div class="goods-valign-m" ondblclick="javascript:toEditGoodsBase(2,'+rowdata['goodsId']+',\'\')">');
                    html.push('<input id="ipt_2_'+rowdata['goodsId']+'" onkeyup="javascript:WST.isChinese(this,1)" onkeypress="return WST.isNumberKey(event)" onblur="javascript:editGoodsBase(2,'+rowdata['goodsId']+')" style="display:none;width:98%;border:1px solid red;" maxlength="6"/>');
                    html.push('<span id="span_2_'+rowdata['goodsId']+'" style="display: inline;cursor:pointer;color:green;">'+rowdata['shopPrice']+'</span>');

                html.push('</div>');
                return html.join('');
	        }},
	        { display: '推荐', name: 'isRecom',isSort: false,width:50,render: function (rowdata, rowindex, value){
	        	return goodsStatus(rowdata['goodsId'],'r',rowdata['isRecom']);
	        }},
	        { display: '精品', name: 'isBest',isSort: false,width:50,render: function (rowdata, rowindex, value){
	        	return goodsStatus(rowdata['goodsId'],'b',rowdata['isBest']);
	        }},
	        { display: '新品', name: 'isNew',isSort: false,width:50,render: function (rowdata, rowindex, value){
	        	return goodsStatus(rowdata['goodsId'],'n',rowdata['isNew']);
	        }},
	        { display: '热销', name: 'isHot',isSort: false,width:50,render: function (rowdata, rowindex, value){
	        	return goodsStatus(rowdata['goodsId'],'h',rowdata['isHot']);
	        }},
	        { display: '销量', name: 'saleNum',isSort: false,width:80,render: function (rowdata, rowindex, value){
	        	return "<div class='goods-valign-m'>"+rowdata['saleNum']+"</div>";
	        }},
	        { display: '库存', name: 'saleNum',isSort: false,width:80,render: function (rowdata, rowindex, value){
	        	var html = [];
	        		html.push('<div class="goods-valign-m" ondblclick="javascript:toEditGoodsBase(3,'+rowdata['goodsId']+',\'\')">');
                    html.push('<input id="ipt_3_'+rowdata['goodsId']+'" onkeyup="javascript:WST.isChinese(this,1)" onkeypress="return WST.isNumberKey(event)" onblur="javascript:editGoodsBase(3,'+rowdata['goodsId']+')" style="display:none;width:98%;border:1px solid red;" maxlength="6"/>');
                    html.push('<span id="span_3_'+rowdata['goodsId']+'" style="display: inline;cursor:pointer;color:green;">'+rowdata['goodsStock']+'</span>');
                html.push('</div>');
                return html.join('');
	        }},
	        { display: '操作', name: 'op',isSort: false,width:110,render: function (rowdata, rowindex, value){
	            var h = "";
	            h += "<div class='goods-valign-m'><a target='_blank' href='"+WST.U("home/goods/detail","id="+rowdata['goodsId'])+"'>查看</a> ";
	            if(WST.GRANT.ZSSP_02)h += "<a href='javascript:edit(" + rowdata['goodsId'] + ",\"store\")'>编辑</a> ";
	            if(WST.GRANT.ZSSP_03)h += "<a href='javascript:del(" + rowdata['goodsId'] + ",\"store\")'>删除</a></div> "; 
	            return h;
	        }}
        ]
    });
}
function loadStoreGrid(){
	var params = WST.getParams('.j-ipt');
	params.goodsCatIdPath = WST.ITGetAllGoodsCatVals('cat_0','pgoodsCats').join('_');
	grid.set('url',WST.U('admin/goods/storeByPage',params));
}


function loadWarnGrid(){
	var params = WST.getParams('.j-ipt');
	params.goodsCatIdPath = WST.ITGetAllGoodsCatVals('cat_0','pgoodsCats').join('_');
	grid.set('url',WST.U('admin/goods/warnByPage',params));
}
function initWarnGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/goods/warnByPage'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        rowHeight:65,
        minColToggle:6,
        rownumbers:true,
        columns: [
            { display: '&nbsp;', name: 'goodsName',width:66,align:'left',heightAlign:'left',isSort: false,render: function (rowdata, rowindex, value){
            	return "<img style='height:60px;width:60px;' src='"+WST.conf.ROOT+"/"+rowdata['goodsImg']+"'>";
            }},
	        { display: '商品名称', name: 'goodsName',heightAlign:'left',isSort: false,render: function (rowdata, rowindex, value){
	            return rowdata['goodsName'];
	        }},
	        { display: '商品货号', name: 'productNo',isSort: false,render: function (rowdata, rowindex, value){
	        	return "<div class='goods-valign-m'>"+rowdata['productNo']+"</div>";
	        }},
	        { display: '现有库存', name: 'goodsStock',isSort: false,width:200,render: function (rowdata, rowindex, value){
	        	var html = [];
                    html.push('<div class="goods-valign-m" ondblclick="javascript:toEditGoodsBase(3,'+rowdata['goodsId']+',\'\')">');
                    html.push('<input id="ipt_3_'+rowdata['goodsId']+'" onkeyup="javascript:WST.isChinese(this,1)" onkeypress="return WST.isNumberKey(event)" onblur="javascript:editGoodsStock(3,'+rowdata['goodsId']+')" style="display:none;width:100%;border:1px solid red;width:40px;" maxlength="6"/>');
                    html.push('<span id="span_3_'+rowdata['goodsId']+'" style="display: inline;cursor:pointer;color:green;">'+rowdata['goodsStock']+'</span>');
                html.push('</div>');
                return html.join('');
	        }},
	        { display: '预警库存', name: 'warnStock',isSort: false,width:200,render: function (rowdata, rowindex, value){
	        	var html = [];
                    html.push('<div class="goods-valign-m" ondblclick="javascript:toEditGoodsBase(4,'+rowdata['goodsId']+',\'\')">');
                    html.push('<input id="ipt_4_'+rowdata['goodsId']+'" onkeyup="javascript:WST.isChinese(this,1)" onkeypress="return WST.isNumberKey(event)" onblur="javascript:editGoodsStock(4,'+rowdata['goodsId']+')" style="display:none;width:100%;border:1px solid red;width:40px;" maxlength="6"/>');
                    html.push('<span id="span_4_'+rowdata['goodsId']+'" style="display: inline;cursor:pointer;color:green;">'+rowdata['warnStock']+'</span>');
                html.push('</div>');
                return html.join('');
	        }},
	        { display: '操作', name: 'op',isSort: false,width:110,render: function (rowdata, rowindex, value){
	            var h = "";
	            h += "<div class='goods-valign-m'><a target='_blank' href='"+WST.U("home/goods/detail","id="+rowdata['goodsId'])+"'>查看</a> ";
	            return h;
	        }}
        ]
    });
}
function editGoodsStock(fv,goodsId,ids){
	var vtext = $('#ipt_'+fv+'_'+goodsId).val();
	if($.trim(vtext)==''){
		WST.msg('库存不能为空', {icon: 5});	
        return;
	}
	var params = {};
	params.type = fv;
	params.number = vtext;
	params.id = goodsId;
	params.goodsId = ids;
	$.post(WST.U('admin/Goods/editwarnStock'),params,function(data,textStatus){
		var json = WST.toAdminJson(data);
		if(json.status>0){
			$('#img_'+fv+'_'+goodsId).fadeTo("fast",100);
			endEditGoodsBase(fv,goodsId);
			$('#img_'+fv+'_'+goodsId).fadeTo("slow",0);
			WST.msg(json.msg, {icon: 1,time:1000}); 
		}else{
			WST.msg(json.msg, {icon: 5}); 
		}
	});
}

function del(id,src){
	var c = WST.confirm({content:'您确定要删除商品吗?',yes:function(){
		layer.close(c);
		var load = WST.load({msg:'正在删除，请稍后...'});
		$.post(WST.U('admin/goods/del'),{id:id},function(data,textStatus){
			layer.close(load);
		    var json = WST.toAdminJson(data);
		    if(json.status==1){
		    	switch(src){
		    	   case 'sale':loadSaleGrid(0);break;
		    	   case 'store':loadStoreGrid(0);break;
		    	}
		    }else{
		    	WST.msg(json.msg,{icon:2});
		    }
		});
	}});
}

// 批量 上架/下架
function changeSale(i,func){
	var ids = WST.getChks('.chk');
	if(ids==''){
		WST.msg('请先选择商品!', {icon: 5});
		return;
	}
	var params = {};
	params.ids = ids.filter(function(value){
		return value>0;
	});
	params.isSale = i;
	$.post(WST.U('admin/goods/changeSale'), params, function(data,textStatus){
		var json = WST.toAdminJson(data);
		if(json.status=='1'){
			WST.msg('操作成功',{icon:1},(function(){
			   $('#all').prop('checked',false);
			   switch(func){
	    	       case 'store':loadStoreGrid(0);break;
	    	       case 'sale':loadSaleGrid(0);break;
	    	  }
			}));
	    }else if(json.status=='-2'){
	    	WST.msg(json.msg, {icon: 5});
	    }else if(json.status=='2'){
	    	WST.msg(json.msg, {icon: 1},function(){
	    		switch(func){
		    	   case 'store':loadStoreGrid(0);break;
	    	       case 'sale':loadSaleGrid(0);break;
		    	}
	    	});
	    }else{
	    	WST.msg(json.msg, {icon: 5,time:3000});
	    }
	});
}

// 批量设置 精品/新品/推荐/热销
function changeGoodsStatus(isWhat,src){
	var ids = WST.getChks('.chk');
	if(ids==''){
		WST.msg('请先选择商品!', {icon: 5});
		return;
	}
	var params = {};
	params.ids = ids;
	params.is = isWhat;
	$.post(WST.U('admin/goods/changeGoodsStatus'),params,function(data,textStatus){
		var json = WST.toAdminJson(data);
		if(json.status=='1'){
			WST.msg('设置成功',{icon:1},function(){
				   $('#all').prop('checked',false);
				   switch(src){
		    	   case 'store':loadStoreGrid(0);break;
		    	   case 'sale':loadSaleGrid(0);break;
		    	  }
			});
		}else{
			WST.msg('设置失败',{icon:5});
		}
	});
}

// 双击设置 
function changSaleStatus(isWhat, obj, id){
	var params = {};
	status = $(obj).attr('status');
	params.status = status;
	params.id = id;
	switch(isWhat){
	   case 'r':params.is = "isRecom";break;
	   case 'b':params.is = "isBest";break;
	   case 'n':params.is = "isNew";break;
	   case 'h':params.is = "isHot";break;
	}
	var load = WST.load({msg:'请稍后...'});
	$.post(WST.U('admin/goods/changSaleStatus'),params,function(data,textStatus){
		layer.close(load);
		var json = WST.toAdminJson(data);
		if(json.status==1){
			if(status==0){
				$(obj).attr('status',1);
				$(obj).removeClass('wrong').addClass('right');
			}else{
				$(obj).attr('status',0);
				$(obj).removeClass('right').addClass('wrong');
			}
		}else{
			WST.msg('操作失败',{icon:5});
		}
	});
}

//双击修改
function toEditGoodsBase(fv,goodsId,flag){	
	if((fv==2 || fv==3) && flag==1){
		WST.msg('该商品存在商品属性，不能直接修改，请进入编辑页修改', {icon: 5});
		return;
	}else{
		$("#ipt_"+fv+"_"+goodsId).show();
		$("#span_"+fv+"_"+goodsId).hide();
		$("#ipt_"+fv+"_"+goodsId).focus();
		$("#ipt_"+fv+"_"+goodsId).val($("#span_"+fv+"_"+goodsId).html());
	}
	
}
function endEditGoodsBase(fv,goodsId){
	$('#span_'+fv+'_'+goodsId).html($('#ipt_'+fv+'_'+goodsId).val());
	$('#span_'+fv+'_'+goodsId).show();
    $('#ipt_'+fv+'_'+goodsId).hide();
}
function editGoodsBase(fv,goodsId){

	var vtext = $('#ipt_'+fv+'_'+goodsId).val();
	if($.trim(vtext)==''){
		if(fv==2){
			WST.msg('价格不能为空', {icon: 5});
		}else if(fv==3){
			WST.msg('库存不能为空', {icon: 5});
		}		
        return;
	}
	var params = {};
	(fv==2)?params.shopPrice=vtext:params.goodsStock=vtext;
	params.goodsId = goodsId;
	$.post(WST.U('admin/Goods/editGoodsBase'),params,function(data,textStatus){
		var json = WST.toAdminJson(data);
		if(json.status>0){
			$('#img_'+fv+'_'+goodsId).fadeTo("fast",100);
			endEditGoodsBase(fv,goodsId);
			$('#img_'+fv+'_'+goodsId).fadeTo("slow",0);
			WST.msg(json.msg, {icon: 1,time:1000}); 
		}else{
			WST.msg(json.msg, {icon: 5}); 
		}
	});
}

function benchDel(func,flag){
	if(flag==1){
		var ids = WST.getChks('.chk1');
	}else{
		var ids = WST.getChks('.chk');
	}
	
	if(ids==''){
		WST.msg('请先选择商品!', {icon: 5});
		return;
	}
	var params = {};
	params.ids = ids;
	var c = WST.confirm({content:'您确定要删除这些商品吗?',yes:function(){
		layer.close(c);
		var load = WST.load({msg:'请稍后...'});
		$.post(WST.U('admin/goods/batchDel'),params,function(data,textStatus){
			layer.close(load);
			var json = WST.toAdminJson(data);
			if(json.status=='1'){
				WST.msg('操作成功',{icon:1},function(){
					   $('#all').prop('checked',false);
					   switch(func){
			    	   case 'store':loadStoreGrid(0);break;
			    	   case 'sale':loadSaleGrid(0);break;
			    	  }
				});
			}else{
				WST.msg('操作失败',{icon:5});
			}
		});
	}});
}