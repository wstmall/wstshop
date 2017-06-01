<?php
namespace wstshop\admin\model;
use think\Db;
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
 * 退款订单业务处理类
 */
class OrderRefunds extends Base{
	
    /**
	 * 获取用户退款订单列表
	 */
	public function refundPageQuery(){
		$where = ['o.dataFlag'=>1];
		$where['orderStatus'] = ['in',[-1,-3]];
		$where['o.payType'] = 1;
		$orderNo = input('orderNo');
		$deliverType = (int)input('deliverType',-1);
		$isRefund = (int)input('isRefund',-1);
		if($orderNo!='')$where['orderNo'] = ['like','%'.$orderNo.'%'];
		if($deliverType!=-1)$where['o.deliverType'] = $deliverType;
		if($isRefund!=-1)$where['o.isRefund'] = $isRefund;
		$page = Db::name('orders')->alias('o')
		     ->join('__USERS__ u','o.userId=u.userId','left')
		     ->join('__ORDER_REFUNDS__ orf ','o.orderId=orf.orderId') 
		     ->where($where)
		     ->field('orf.id refundId,o.orderId,o.orderNo,o.goodsMoney,o.totalMoney,o.realTotalMoney,
		              o.orderStatus,u.loginName,o.deliverType,payType,payFrom,o.orderStatus,orderSrc,orf.backMoney,orf.refundRemark,isRefund,orf.createTime')
			 ->order('orf.createTime', 'desc')
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
		return $this->alias('orf')->join('__ORDERS__ o','orf.orderId=o.orderId')->where(['orf.id'=>(int)input('get.id'),'payType'=>1,'isRefund'=>0,'orderStatus'=>['in',[-1,-3]],'refundStatus'=>0])
		         ->field('orf.id refundId,orderNo,o.orderId,goodsMoney,refundReson,refundOtherReson,totalMoney,realTotalMoney,deliverMoney,payType,payFrom,backMoney,tradeNo')
		         ->find();
	}
	/**
	 * 退款
	 */
	public function orderRefund(){
		$id = (int)input('post.id');
		$content = input('post.content');
		$backMoney = (float)input('post.backMoney');
		if($id==0)return WSTReturn("操作失败!");
		$refund = $this->get($id);
		if(empty($refund) || $refund->refundStatus!=0)return WSTReturn("该退款订单不存在或已退款!");
		if($backMoney>$refund->backMoney)return WSTReturn("订单退款金额不能大于付款金额!");
		if($backMoney<0)return WSTReturn("订单退款金额不能为负数!");
		Db::startTrans();
        try{
        	$order = model('orders')->get($refund->orderId);
        	if(!in_array($order->orderStatus,[-1,-3]) || $order->payType !=1)return WSTReturn("无效的退款订单!");
			//修改退款单信息
			$refund->refundRemark = $content;
			$refund->backMoney = $backMoney;
			$refund->refundTime = date('Y-m-d H:i:s');
			$refund->refundStatus = 1;
			$refund->save();
			//修改订单状态
			$order->isRefund = 1;
			$order->save();
			//修改用户账户金额
			Db::name('users')->where('userId',$order->userId)->setInc('userMoney',$refund->backMoney);		
			//创建用户资金流水记录
			$lm = [];
			$lm['targetType'] = 0;
			$lm['targetId'] = $order->userId;
			$lm['dataId'] = $order->orderId;
			$lm['dataSrc'] = 1;
			$lm['remark'] = '订单【'.$order->orderNo.'】退款¥'.$refund->backMoney."。".(($content!='')?"【退款备注】：".$content:'');
			$lm['moneyType'] = 1;
			$lm['money'] = $refund->backMoney;
			$lm['payType'] = 0;
			$lm['createTime'] = date('Y-m-d H:i:s');
			Db::name('log_moneys')->insert($lm);
			//发送一条用户信息
			WSTSendMsg($order->userId,"您的退款订单【".$order->orderNo."】已处理，请留意账户到账情况。".(($content!='')?"【退款备注：".$content."】":""),['from'=>1,'dataId'=>$order->orderId]);
			Db::commit();
			return WSTReturn("操作成功",1); 
        }catch (\Exception $e) {
            Db::rollback();
        }
		return WSTReturn("操作失败，请刷新后再重试"); 
	}
}
