var grid;
function loadWaitDeliveryGrid(){
	var p = WST.arrayParams('.j-ipt');
	grid.set('url',WST.U('admin/orders/waitDeliveryByPage',p.join('&')));
}
function initWaitDeliveryGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/orders/waitDeliveryByPage'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
            { display: '订单编号', name: 'orderNo',isSort: false},
	        { display: '收货人', name: 'userName',isSort: false},
	        { display: '订单总金额', name: 'totalMoney',isSort: false},
	        { display: '实收金额', name: 'realTotalMoney',isSort: false},
	        { display: '支付方式', name: 'payTypeName',isSort: false},
	        { display: '配送方式', name: 'deliverTypeName',isSort: false},
	        { display: '下单时间', name: 'createTime',isSort: false},
	        { display: '订单状态', name: 'status'},
	        { display: '操作', name: 'op',isSort: false,width:120,render: function (rowdata, rowindex, value){
	            var h = "";
	            h += "<a href='javascript:view(" + rowdata['orderId'] + ")'>详情</a> ";
	            h += "<a href='javascript:deliver(" + rowdata['orderId'] + ","+rowdata['deliverType']+")'>发货</a> ";
	            h += "<a href='javascript:del(" + rowdata['orderId'] + ",\"loadWaitDeliveryGrid\")'>删除</a> ";
	            return h;
	        }}
        ]
    });
}
function loadDeliveryGrid(){
	var p = WST.arrayParams('.j-ipt');
	grid.set('url',WST.U('admin/orders/deliveryByPage',p.join('&')));
}
function initDeliveryGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/orders/deliveryByPage'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
            { display: '订单编号', name: 'orderNo',isSort: false},
	        { display: '收货人', name: 'userName',isSort: false},
	        { display: '订单总金额', name: 'totalMoney',isSort: false},
	        { display: '实收金额', name: 'realTotalMoney',isSort: false},
	        { display: '支付方式', name: 'payTypeName',isSort: false},
	        { display: '配送方式', name: 'deliverTypeName',isSort: false},
	        { display: '下单时间', name: 'createTime',isSort: false},
	        { display: '订单状态', name: 'status'},
	        { display: '操作', name: 'op',isSort: false,width:120,render: function (rowdata, rowindex, value){
	            var h = "";
	            h += "<a href='javascript:view(" + rowdata['orderId'] + ")'>详情</a> ";
	            h += "<a href='javascript:del(" + rowdata['orderId'] + ",\"loadDeliveryGrid\")'>删除</a> ";
	            return h;
	        }}
        ]
    });
}
function loadWaitPayGrid(){
	var p = WST.arrayParams('.j-ipt');
	grid.set('url',WST.U('admin/orders/waitPayByPage',p.join('&')));
}
function initWaitPayGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/orders/waitPayByPage'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
            { display: '订单编号', name: 'orderNo',isSort: false},
	        { display: '收货人', name: 'userName',isSort: false},
	        { display: '订单总金额', name: 'totalMoney',isSort: false},
	        { display: '实收金额', name: 'realTotalMoney',isSort: false},
	        { display: '配送方式', name: 'deliverTypeName',isSort: false},
	        { display: '下单时间', name: 'createTime',isSort: false},
	        { display: '订单状态', name: 'status'},
	        { display: '操作', name: 'op',isSort: false,width:140,render: function (rowdata, rowindex, value){
	            var h = "";
	            h += "<a href='javascript:view(" + rowdata['orderId'] + ")'>详情</a> ";
	            h += "<a href='javascript:del(" + rowdata['orderId'] + ",\"loadWaitPayGrid\")'>删除</a> ";
	            return h;
	        }}
        ]
    });
}
function loadAbnormalGrid(){
	var p = WST.arrayParams('.j-ipt');
	grid.set('url',WST.U('admin/orders/abnormalByPage',p.join('&')));
}
function initAbnormalGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/orders/abnormalByPage'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
            { display: '订单编号', name: 'orderNo',isSort: false},
	        { display: '收货人', name: 'userName',isSort: false},
	        { display: '订单总金额', name: 'totalMoney',isSort: false},
	        { display: '实收金额', name: 'realTotalMoney',isSort: false},
	        { display: '支付方式', name: 'payTypeName',isSort: false},
	        { display: '配送方式', name: 'deliverTypeName',isSort: false},
	        { display: '下单时间', name: 'createTime',isSort: false},
	        { display: '订单状态', name: 'status'},
	        { display: '操作', name: 'op',isSort: false,width:140,render: function (rowdata, rowindex, value){
	            var h = "";
	            h += "<a href='javascript:view(" + rowdata['orderId'] + ")'>详情</a> ";
	            h += "<a href='javascript:del(" + rowdata['orderId'] + ",\"loadAbnormalGrid\")'>删除</a> ";
	            return h;
	        }}
        ]
    });
}
function loadReceivedGrid(){
	var p = WST.arrayParams('.j-ipt');
	grid.set('url',WST.U('admin/orders/receivedByPage',p.join('&')));
}
function initReceivedGrid(){
	grid = $("#maingrid").ligerGrid({
		url:WST.U('admin/orders/receivedByPage'),
		pageSize:WST.pageSize,
		pageSizeOptions:WST.pageSizeOptions,
		height:'99%',
        width:'100%',
        minColToggle:6,
        rownumbers:true,
        columns: [
            { display: '订单编号', name: 'orderNo',isSort: false},
	        { display: '收货人', name: 'userName',isSort: false},
	        { display: '订单总金额', name: 'totalMoney',isSort: false},
	        { display: '实收金额', name: 'realTotalMoney',isSort: false},
	        { display: '支付方式', name: 'payTypeName',isSort: false},
	        { display: '配送方式', name: 'deliverTypeName',isSort: false},
	        { display: '下单时间', name: 'createTime',isSort: false},
	        { display: '订单状态', name: 'status'},
	        { display: '操作', name: 'op',isSort: false,width:140,render: function (rowdata, rowindex, value){
	            var h = "";
	            h += "<a href='javascript:view(" + rowdata['orderId'] + ")'>详情</a> ";
	            h += "<a href='javascript:del(" + rowdata['orderId'] + ",\"loadReceivedGrid\")'>删除</a> ";
	            return h;
	        }}
        ]
    });
}
function view(id){
	location.href=WST.U('admin/orders/view','id='+id);
}
function loadGrid(){
	var p = WST.arrayParams('.j-ipt');
	grid.set('url',WST.U('admin/orders/pageQuery',p.join('&')));
}
function del(id,src){
    var c = WST.confirm({content:'您确定要删除订单吗?',yes:function(){
		layer.close(c);
		var load = WST.load({msg:'正在删除，请稍后...'});
		$.post(WST.U('admin/orders/del'),{id:id},function(data,textStatus){
			layer.close(load);
		    var json = WST.toAdminJson(data);
		    if(json.status==1){
		    	WST.msg(json.msg,{icon:1});
		    	var fn = window[src];
		    	fn();
		    }else{
		    	WST.msg(json.msg,{icon:2});
		    }
		});
	}});
}
function deliver(id,type){
    if(type==1){
    	var c = WST.confirm({content:'您确定用户已提货吗?',yes:function(){
		    layer.close(c);
		    var ll = WST.load({msg:'正在提交信息，请稍候...'});
			$.post(WST.U('admin/orders/deliver'),{id:id},function(data){
				var json = WST.toAdminJson(data);
				if(json.status>0){
					WST.msg(json.msg,{icon:1});
					initWaitDeliveryGrid(0);
			    	layer.close(ll);
				}else{
					WST.msg(json.msg,{icon:2});
				}
			});
		}});
    }else{
        WST.open({type: 1,title:"请输入发货快递信息",shade: [0.6, '#000'], border: [0],
			content: $('#deliverBox'),area: ['350px', '190px'],btn: ['确定发货','取消'],
			yes:function(index, layero){
				var ll = WST.load({msg:'正在提交信息，请稍候...'});
				$.post(WST.U('admin/orders/deliver'),{id:id,expressId:$('#expressId').val(),expressNo:$('#expressNo').val()},function(data){
					var json = WST.toAdminJson(data);
					if(json.status>0){
						$('#deliverForm')[0].reset();
						WST.msg(json.msg,{icon:1});
						initWaitDeliveryGrid(0);
						layer.close(index);
				    	layer.close(ll);
					}else{
						WST.msg(json.msg,{icon:2});
					}
				});
			}
	    });
    }
}