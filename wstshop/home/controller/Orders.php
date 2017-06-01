<?php
namespace wstshop\home\controller;
use wstshop\common\model\Orders as M;
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
 * 订单控制器
 */
class Orders extends Base{
    /**
    * 提交订单
    */
	public function submit(){
		$m = new M();
		$rs = $m->submit();
		return $rs;
	}
	/**
	 * 订单提交成功
	 */
	public function succeed(){
		$m = new M();
		$rs = $m->getByUnique();
		$this->assign('object',$rs);
		if(!empty($rs)){
			if($rs['payType']==1){
				$this->assign('id',(int)input("get.id"));
				return $this->fetch('order_pay_step1');
			}else{
			    return $this->fetch('order_success');
			}
		}else{
			$this->assign('message','Sorry~您要找的订单不存在或丢失了。。。');
			return $this->fetch('error_msg');
		}
	}
	/**
	 * 用户-待付款订单
	 */
	public function waitPay(){
		return $this->fetch('users/orders/list_wait_pay');
	}
    /**
	 * 用户-获取待付款列表
	 */
    public function waitPayByPage(){
		$m = new M();
		$rs = $m->userOrdersByPage(-2);
		return WSTReturn("", 1,$rs);
	}
	/**
	 * 待发货列表
	 */
    public function waitSendByPage(){
		$m = new M();
		$rs = $m->userOrdersByPage(0);
		return WSTReturn("", 1,$rs);
	}
	/**
	 * 等待发货
	 */
	public function waitSend(){
		return $this->fetch('users/orders/list_wait_send');
	}
    /**
	 * 等待收货
	 */
	public function waitReceive(){
		return $this->fetch('users/orders/list_wait_receive');
	}
    /**
	 * 待收货列表
	 */
    public function waitReceiveByPage(){
		$m = new M();
		$rs = $m->userOrdersByPage(1);
		return WSTReturn("", 1,$rs);
	}
	/**
	 * 用户-待评价
	 */
    public function waitAppraise(){
		return $this->fetch('users/orders/list_appraise');
	}
	/**
	 * 用户-待评价
	 */
	public function waitAppraiseByPage(){
		$m = new M();
		$rs = $m->userOrdersByPage(2,0);
		return WSTReturn("", 1,$rs);
	}
	/**
	 * 用户-已完成订单
	 */
    public function finish(){
		return $this->fetch('users/orders/list_finish');
	}
	/**
	 * 用户-已完成订单
	 */
	public function finishByPage(){
		$m = new M();
		$rs = $m->userOrdersByPage(2,-1);
		return WSTReturn("", 1,$rs);
	}
   /**
	 * 用户-加载取消订单页面
	 */
	public function toCancel(){
		return $this->fetch('users/orders/box_cancel');
	}

	/**
	 * 用户取消订单
	 */
	public function cancellation(){
		$m = new M();
		$rs = $m->cancel();
		return $rs;
	}
    /**
	 * 用户-取消订单列表
	 */
	public function cancel(){
		return $this->fetch('users/orders/list_cancel');
	}
	/**
	 * 用户-获取已取消订单
	 */
    public function cancelByPage(){
		$m = new M();
		$rs = $m->userOrdersByPage(-1);
		return WSTReturn("", 1,$rs);
	}
    /**
	 * 用户-拒收订单
	 */
	public function toReject(){
		return $this->fetch('users/orders/box_reject');
	}
	/**
	 * 用户拒收订单
	 */
	public function reject(){
		$m = new M();
		$rs = $m->reject();
		return $rs;
	}
	/**
	 * 用户-申请退款
	 */
	public function toRefund(){
		$m = new M();
		$rs = $m->getMoneyByOrder((int)input('id'));
		$this->assign('object',$rs);
		return $this->fetch('users/orders/box_refund');
	}

	
	/**
	 * 用户-拒收/退款列表
	 */
	public function abnormal(){
		return $this->fetch('users/orders/list_abnormal');
	}
	/**
	 * 获取用户拒收/退款列表
	 */
    public function abnormalByPage(){
		$m = new M();
		$rs = $m->userOrdersByPage(-3);
		return WSTReturn("", 1,$rs);
	}

	/**
	 * 待处理订单
	 */
	public function deliveredByPage(){
		$m = new M();
		$rs = $m->shopOrdersByPage([1,2]);
		return WSTReturn("", 1,$rs);
	}
	/**
	 * 用户收货
	 */
	public function receive(){
		$m = new M();
		$rs = $m->receive();
		return $rs;
	}
	/**
	 * 获取订单信息方便修改价格
	 */
	public function getMoneyByOrder(){
		$m = new M();
		$rs = $m->getMoneyByOrder();
		return WSTReturn("", 1,$rs);
	}
	

    /**
	 * 用户-订单详情
	 */
	public function detail(){
		$m = new M();
		$rs = $m->getByView((int)input('id'));
		$this->assign('object',$rs);
		return $this->fetch('users/orders/view');
	}
	
   /**
	* 用户-评价页
	*/
	public function orderAppraise(){
		$m = new M();
		//根据订单id获取 商品信息跟商品评价
		$data = $m->getOrderInfoAndAppr();
		$this->assign(['data'=>$data['Rows'],
					   'count'=>$data['count'],
					   'alreadys'=>$data['alreadys']
						]);
		return $this->fetch('users/orders/list_order_appraise');
	}
	/**
	* 设置完成评价
	*/
	public function complateAppraise($orderId){
		$m = new M();
		return $m->complateAppraise($orderId);
	}
}
