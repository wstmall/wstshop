<?php
namespace wstshop\admin\model;
use think\Db;
use think\Loader;
/**
 * ============================================================================
 * WSTShop网上商店
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstshop.net
 * 交流社区:http://bbs.shangtaosoft.com
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 订单业务处理类
 */
class Orders extends Base{
	/**
	 * 获取订单
	 */
	public function pageQuery($orderStatus,$payType = 0){
		$orderNo = input('orderNo');
		$payType = $payType>0?$payType:(int)input('payType',-1);
		$deliverType = (int)input('deliverType',-1);
		$where = ['dataFlag'=>1];
		if(is_array($orderStatus)){
			$where['orderStatus'] = ['in',$orderStatus];
		}else{
			$where['orderStatus'] = $orderStatus;
		}
		if($orderNo!=''){
			$where['orderNo'] = ['like',"%$orderNo%"];
		}
		if($payType > -1){
			$where['payType'] =  $payType;
		}
		if($deliverType > -1){
			$where['deliverType'] =  $deliverType;
		}
		$page = $this->alias('o')->where($where)
		      ->join('__ORDER_REFUNDS__ orf','orf.orderId=o.orderId and refundStatus=0','left')
		      ->field('o.orderId,orderNo,goodsMoney,totalMoney,realTotalMoney,orderStatus,deliverType,deliverMoney,isAppraise
		              ,payType,payFrom,userAddress,orderStatus,isPay,isAppraise,userName,orderSrc,o.createTime,orf.id refundId')
			  ->order('o.createTime', 'desc')
			  ->paginate()->toArray();
	    if(count($page['Rows'])>0){ 
	    	 foreach ($page['Rows'] as $key => $v){
	    	 	 $page['Rows'][$key]['payTypeName'] = WSTLangPayType($v['payType']);
	    	 	 $page['Rows'][$key]['deliverTypeName'] = WSTLangDeliverType($v['deliverType']==1);
	    	 	 $page['Rows'][$key]['status'] = WSTLangOrderStatus($v['orderStatus']);
	    	 }
	    }
	    return $page;
	}	
    /**
	 * 获取用户退款订单列表
	 */
	public function refundPageQuery(){
		$where = ['o.dataFlag'=>1];
		$where['orderStatus'] = ['in',[-1,-4]];
		$where['o.payType'] = 1;
		$orderNo = input('orderNo');
		$shopName = input('shopName');
		$deliverType = (int)input('deliverType',-1);
		$areaId1 = (int)input('areaId1');
		$areaId2 = (int)input('areaId2');
		$areaId3 = (int)input('areaId3');
		$isRefund = (int)input('isRefund',-1);
		if($orderNo!='')$where['orderNo'] = ['like','%'.$orderNo.'%'];
		if($shopName!='')$where['shopName|shopSn'] = ['like','%'.$shopName.'%'];
		if($areaId1>0)$where['s.areaId1'] = $areaId1;
		if($areaId2>0)$where['s.areaId2'] = $areaId2;
		if($areaId3>0)$where['s.areaId3'] = $areaId3;
		if($deliverType!=-1)$where['o.deliverType'] = $deliverType;
		if($isRefund!=-1)$where['o.isRefund'] = $isRefund;
		$page = $this->alias('o')->join('__SHOPS__ s','o.shopId=s.shopId','left')
		     ->join('__ORDER_REFUNDS__ orf ','o.orderId=orf.orderId','left') 
		     ->where($where)
		     ->field('o.orderId,o.orderNo,s.shopName,s.shopId,s.shopQQ,s.shopWangWang,o.goodsMoney,o.totalMoney,o.realTotalMoney,
		              o.orderStatus,o.userName,o.deliverType,payType,payFrom,o.orderStatus,orderSrc,orf.refundRemark,isRefund,o.createTime')
			 ->order('o.createTime', 'desc')
			 ->paginate(input('pagesize/d'))->toArray();
	    if(count($page['Rows'])>0){
	    	 foreach ($page['Rows'] as $key => $v){
	    	 	 $page['Rows'][$key]['payType'] = WSTLangPayType($v['payType']);
	    	 	 $page['Rows'][$key]['deliverType'] = WSTLangDeliverType($v['deliverType']==1);
	    	 	 $page['Rows'][$key]['status'] = WSTLangOrderStatus($v['orderStatus']);
	    	 }
	    }
	    return $page;
	}
	/**
	 * 获取退款资料
	 */
	public function getInfoByRefund(){
		return $this->where(['orderId'=>(int)input('get.id'),'isRefund'=>0,'orderStatus'=>['in',[-1,-4]]])
		         ->field('orderNo,orderId,goodsMoney,totalMoney,realTotalMoney,deliverMoney,payType,payFrom,tradeNo')
		         ->find();
	}
	/**
	 * 退款
	 */
	public function orderRefund(){
		$id = (int)input('post.id');
		$content = input('post.content');
		if($id==0)return WSTReturn("操作失败!");
		$order = $this->where(['orderId'=>(int)input('post.id'),'payType'=>1,'isRefund'=>0,'orderStatus'=>['in',[-1,-4]]])
		         ->field('userId,orderNo,orderId,goodsMoney,totalMoney,realTotalMoney,deliverMoney,payType,payFrom,tradeNo')
		         ->find();
		if(empty($order))return WSTReturn("该订单不存在或已退款!");
		Db::startTrans();
        try{
			$order->isRefund = 1;
			$order->save();
			//修改用户账户金额
			Db::name('users')->where('userId',$order->userId)->setInc('userMoney',$order->realTotalMoney);
			//创建资金流水记录
			$lm = [];
			$lm['targetType'] = 0;
			$lm['targetId'] = $order->userId;
			$lm['dataId'] = $order->orderId;
			$lm['dataSrc'] = 1;
			$lm['remark'] = '订单【'.$order->orderNo.'】退款¥'.$order->realTotalMoney."。".(($content!='')?"【退款备注】：".$content:'');
			$lm['moneyType'] = 1;
			$lm['money'] = $order->realTotalMoney;
			$lm['payType'] = 0;
			$lm['createTime'] = date('Y-m-d H:i:s');
			model('LogMoneys')->save($lm);
			//创建退款记录
			$data = [];
			$data['orderId'] = $id;
			$data['refundRemark'] = $content;
			$data['refundTime'] = date('Y-m-d H:i:s');
			$rs = Db::name('order_refunds')->insert($data);
			if(false !== $rs){
				//发送一条用户信息
				WSTSendMsg($order['userId'],"您的退款订单【".$order['orderNo']."】已处理，请留意账户到账情况。".(($content!='')?"【退款备注：".$content."】":""),['from'=>1,'dataId'=>$id]);
				Db::commit();
				return WSTReturn("操作成功",1); 
			}
        }catch (\Exception $e) {

            Db::rollback();
        }
		return WSTReturn("操作失败，请刷新后再重试"); 
	}
	
	
	/**
	 * 获取订单详情
	 */
	public function getByView($orderId){
		$orders = $this->alias('o')->join('__EXPRESS__ e','o.expressId=e.expressId','left')
		               ->join('__ORDER_REFUNDS__ orf ','o.orderId=orf.orderId','left')
		               ->where('o.dataFlag=1 and o.orderId='.$orderId)
		               ->field('o.*,e.expressName,orf.refundRemark,orf.refundTime')->find();
		if(empty($orders))return WSTReturn("无效的订单信息");
		//获取订单信息
		$orders['log'] = Db::name('log_orders')->where('orderId',$orderId)->order('logId asc')->select();
		//获取订单商品
		$orders['goods'] = Db::name('order_goods')->where('orderId',$orderId)->order('id asc')->select();
		return $orders;
	}

	/**
	 * 删除订单
	 */
	public function del(){
		$id = (int)input('id');
		$orders = $this->get($id);
		if(!empty($orders)){
			$orders->dataFlag = -1;
			$orders->save();
			return WSTReturn('操作成功',1);
		}
		return WSTReturn('操作失败，无效的订单ID');
	}

    /**
	 * 发货
	 */
	public function deliver(){
		$orderId = (int)input('post.id');
		$expressId = (int)input('post.expressId');
		$expressNo = input('post.expressNo');
		if($expressId>0)$expressName = Db::name('express')->where(['expressId'=>$expressId,'dataFlag'=>1])->field('expressName')->find();
		$order = $this->where(['orderId'=>$orderId,'orderStatus'=>0])->field('orderId,orderNo,userId')->find();
		if(!empty($order)){
			Db::startTrans();
		    try{
				$data = ['orderStatus'=>1,'expressId'=>$expressId,'expressNo'=>$expressNo,'deliveryTime'=>date('Y-m-d H:i:s')];
			    $result = $this->where('orderId',$order['orderId'])->update($data);
				if(false != $result){
					//新增订单日志
					$logOrder = [];
					$logOrder['orderId'] = $orderId;
					$logOrder['orderStatus'] = 1;
					$logOrder['logContent'] = "商家已发货".(($expressNo!='')?"，快递号为：".$expressNo:"");
					$logOrder['logUserId'] = $order['userId'];
					$logOrder['logType'] = 0;
					$logOrder['logTime'] = date('Y-m-d H:i:s');
					Db::name('log_orders')->insert($logOrder);
					//发送一条用户信息
					$msgContent = "您的订单【".$order['orderNo']."】已发货啦".(($expressId>0)?"，快递为：".$expressName['expressName']:"")."".(($expressNo!='')?"，单号为：".$expressNo:"")."，请做好收货准备哦~";
					WSTSendMsg($order['userId'],$msgContent,['from'=>1,'dataId'=>$orderId]);
					Db::commit();
					return WSTReturn('操作成功',1);
				}
			}catch (\Exception $e) {
	            Db::rollback();
	            return WSTReturn('操作失败',-1);
	        }
		}
		return WSTReturn('操作失败，请检查订单状态是否已改变');
	}
}
