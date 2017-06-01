<?php
namespace wstshop\common\model;
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
 * 订单业务处理类
 */
class Orders extends Base{
	
	/**
	 * 提交订单
	 */
	public function submit(){
		$addressId = (int)input('post.s_addressId');
		$deliverType = ((int)input('post.deliverType')!=0)?1:0;
		$isInvoice = ((int)input('post.isInvoice')!=0)?1:0;
		$invoiceClient = ($isInvoice==1)?input('post.invoiceClient'):'';
		$payType = ((int)input('post.payType')!=0)?1:0;
		$userId = (int)session('WST_USER.userId');
		if($userId==0)return WSTReturn('下单失败，请先登录');
		//检测购物车
		$carts = model('carts')->getCarts(true);
		if(empty($carts['carts']))return WSTReturn("请选择要购买的商品");
		//检测地址是否有效
		$address = Db::name('user_address')->where(['userId'=>$userId,'addressId'=>$addressId,'dataFlag'=>1])->find();
		if(empty($address)){
			return WSTReturn("无效的用户地址");
		}
	    $areaIds = [];
        $areaMaps = [];
        $tmp = explode('_',$address['areaIdPath']);
        $address['areaId2'] = $tmp[1];//记录配送城市
        foreach ($tmp as $vv){
         	if($vv=='')continue;
         	if(!in_array($vv,$areaIds))$areaIds[] = $vv;
        }
        if(!empty($areaIds)){
	         $areas = Db::name('areas')->where(['dataFlag'=>1,'areaId'=>['in',$areaIds]])->field('areaId,areaName')->select();
	         foreach ($areas as $v){
	         	 $areaMaps[$v['areaId']] = $v['areaName'];
	         }
	         $tmp = explode('_',$address['areaIdPath']);
	         $areaNames = [];
		     foreach ($tmp as $vv){
	         	 if($vv=='')continue;
	         	 $areaNames[] = $areaMaps[$vv];
	         	 $address['areaName'] = implode('',$areaNames);
	         }
         }
		$address['userAddress'] = $address['areaName'].$address['userAddress'];
		WSTUnset($address, 'isDefault,dataFlag,createTime,userId');
		//生成订单
		Db::startTrans();
		try{
			$orderNo = WSTOrderNo(); 
			$orderScore = 0;
			//创建订单
			$order = [];
			// 订单来源 0:PC   1:微信  2:手机版   3:安卓App  4:苹果App
			$module = request()->module();
			$mArr = ['home'=>0,'wechat'=>1,'mobile'=>2];
			// 			默认订单来自pc版
			$order['orderSrc'] = isset($mArr[$module])?$mArr[$module]:0;

			$order = array_merge($order,$address);
			$order['orderNo'] = $orderNo;
			$order['userId'] = $userId;

			$order['payType'] = $payType;
			if($payType==1){
				$order['orderStatus'] = -2;//待付款
				$order['isPay'] = 0;
			}else{
				$order['orderStatus'] = 0;//待发货
			}
			$order['goodsMoney'] = $carts['goodsTotalMoney'];
			$order['deliverType'] = $deliverType;
			// 运费
			$order['deliverMoney'] = ($deliverType==1)?0:WSTOrderFreight($order['areaId2']);

			$order['totalMoney'] = $order['goodsMoney']+$order['deliverMoney'];
			$order['realTotalMoney'] = $order['totalMoney'];
			$order['needPay'] = $order['realTotalMoney'];
			//积分
			$orderScore = 0;
			//如果开启下单获取积分则有积分
			if(WSTConf('CONF.isOrderScore')==1){
				 $orderScore = round($order['goodsMoney'],0);
			}
			$order['orderScore'] = $orderScore;
			$order['isInvoice'] = $isInvoice;
			$order['invoiceClient'] = $invoiceClient;

			$order['orderRemarks'] = input('post.remark');
			$order['dataFlag'] = 1;
			$order['createTime'] = date('Y-m-d H:i:s');
			$result = $this->data($order,true)->isUpdate(false)->allowField(true)->save($order);
			$orderId = 0;
			if(false !== $result){
				$orderId = $this->orderId;
				$orderTotalGoods = [];
				foreach ($carts['carts'] as $gkey =>$goods){
					//创建订单商品记录
					$orderGgoods = [];
					$orderGoods['orderId'] = $orderId;
					$orderGoods['goodsId'] = $goods['goodsId'];
					$orderGoods['goodsNum'] = $goods['cartNum'];
					$orderGoods['goodsPrice'] = $goods['shopPrice'];
					$orderGoods['goodsSpecId'] = $goods['goodsSpecId'];
					if(!empty($goods['specNames'])){
						$specNams = [];
						foreach ($goods['specNames'] as $pkey =>$spec){
							$specNams[] = $spec['catName'].'：'.$spec['itemName'];
						}
						$orderGoods['goodsSpecNames'] = implode('@@_@@',$specNams);
					}else{
						$orderGoods['goodsSpecNames'] = '';
					}
					$orderGoods['goodsName'] = $goods['goodsName'];
					$orderGoods['goodsImg'] = $goods['goodsImg'];
					$orderTotalGoods[] = $orderGoods;
					//修改库存
					Db::name('goods')->where('goodsId',$goods['goodsId'])->update([
						'goodsStock'=>['exp','goodsStock-'.$goods['cartNum']],
						'saleNum'=>['exp','saleNum+'.$goods['cartNum']]
					]);
				}
				Db::name('order_goods')->insertAll($orderTotalGoods);
				//建立订单记录
				$logOrder = [];
				$logOrder['orderId'] = $orderId;
				$logOrder['orderStatus'] = $order['orderStatus'];
				$logOrder['logContent'] = ($payType==1)?"下单成功，等待用户支付":"下单成功";
				$logOrder['logUserId'] = $userId;
				$logOrder['logType'] = 0;
				$logOrder['logTime'] = date('Y-m-d H:i:s');
				Db::name('log_orders')->insert($logOrder);
			}
			//删除已选的购物车商品
			Db::name('carts')->where(['userId'=>$userId,'isCheck'=>1])->delete();
			Db::commit();
			return WSTReturn("提交订单成功", 1,$orderId);
		}catch (\Exception $e) {
            Db::rollback();
            return WSTReturn($this->getError(),-1);
        }
	}
	
	/**
	 * 根据订单唯一流水获取订单信息
	 */
	public function getByUnique(){
		$orderId = input('id/d',0);
		$userId = (int)session('WST_USER.userId');
		$rs = $this->where(['userId'=>$userId,'orderId'=>$orderId])->field('orderId,orderNo,payType,needPay,deliverMoney')->find();
		if(empty($rs))return [];
		$data = [];
		$data['deliverMoney'] = $rs['deliverMoney'];
		$data['orderNo'] = $rs['orderNo'];
		$data['orderId'] = $orderId;
		$data['totalMoney'] = $rs['needPay'];
		$data['payType'] = $rs['payType'];
		//如果是在线支付的话就要加载商品信息和支付信息
		if($data['payType']==1){
			//获取商品信息
			$goods = Db::name('order_goods')->where(['orderId'=>$orderId])->select();
			foreach ($goods as $key =>$v){
				if($v['goodsSpecNames']!=''){
				    $v['goodsSpecNames'] = explode('@@_@@',$v['goodsSpecNames']);
				}else{
					$v['goodsSpecNames'] = [];
				}
				$data['goods'][] = $v;
			}
			//获取支付信息
			$payments = model('payments')->where(['isOnline'=>1,'enabled'=>1])->order('payOrder asc')->select();
			$data['payments'] = $payments;
		}
		return $data;
	}
	
	/**
	 * 获取用户订单列表
	 */
	public function userOrdersByPage($orderStatus,$isAppraise = -1){
		$userId = (int)session('WST_USER.userId');
		$orderNo = input('post.orderNo');
		$userName = input('post.userName');
		$isRefund = (int)input('post.isRefund',-1);
		$where = ['o.userId'=>$userId,'o.dataFlag'=>1];
		if(is_array($orderStatus)){
			$where['orderStatus'] = ['in',$orderStatus];
		}else{
			$where['orderStatus'] = $orderStatus;
		}
		if($isAppraise!=-1)$where['isAppraise'] = $isAppraise;
		if($orderNo!=''){
			$where['o.orderNo'] = ['like',"%$orderNo%"];
		}
		if($userName!=''){
			$where['o.userName'] = ['like',"%$userName%"];
		}
		if(in_array($isRefund,[0,1])){
			$where['isRefund'] = $isRefund;
		}

		$page = $this->alias('o')
		             ->join('__ORDER_REFUNDS__ orf','orf.orderId=o.orderId and orf.refundStatus!=-1','left')
		             ->where($where)
		             ->field('o.userName,o.orderId,o.orderNo,o.goodsMoney,o.totalMoney,o.realTotalMoney,
		              o.orderStatus,o.deliverType,deliverMoney,isPay,payType,payFrom,o.orderStatus,needPay,isAppraise,isRefund,orderSrc,o.createTime,orf.id refundId')
			         ->order('o.createTime', 'desc')
			         ->paginate(input('pagesize/d'))->toArray();
	    if(count($page['Rows'])>0){
	    	 $orderIds = [];
	    	 foreach ($page['Rows'] as $v){
	    	 	 $orderIds[] = $v['orderId'];
	    	 }
	    	 $goods = Db::name('order_goods')->where('orderId','in',$orderIds)->select();
	    	 $goodsMap = [];
	    	 foreach ($goods as $v){
	    	 	 $v['goodsSpecNames'] = str_replace('@@_@@','、',$v['goodsSpecNames']);
	    	 	 $goodsMap[$v['orderId']][] = $v;
	    	 }
	    	 foreach ($page['Rows'] as $key => $v){
	    	 	 $page['Rows'][$key]['list'] = $goodsMap[$v['orderId']];
	    	 	 $page['Rows'][$key]['payTypeName'] = WSTLangPayType($v['payType']);
	    	 	 $page['Rows'][$key]['deliverType'] = WSTLangDeliverType($v['deliverType']==1);
	    	 	 $page['Rows'][$key]['status'] = WSTLangOrderStatus($v['orderStatus']);
	    	 }
	    }
	    return $page;
	}

	/**
	 * 用户收货
	 */
	public function receive(){
		$orderId = (int)input('post.id');
		$userId = (int)session('WST_USER.userId');
		$order = $this->where(['userId'=>$userId,'orderId'=>$orderId,'orderStatus'=>1])
		              ->field('orderId,orderNo,payType,orderScore')->find();
		if(!empty($order)){
			Db::startTrans();
		    try{
				$data = ['orderStatus'=>2,'receiveTime'=>date('Y-m-d H:i:s')];
			    $result = $this->where('orderId',$order['orderId'])->update($data);
				if(false != $result){
					//修改商品成交量
					$goodss = Db::name('order_goods')->where('orderId',$order['orderId'])->field('goodsId,goodsNum')->select();
					foreach($goodss as $key =>$v){
						Db::name('goods')->where('goodsId',$v['goodsId'])->setInc('saleNum', $v['goodsNum']);
					}
					//新增订单日志
					$logOrder = [];
					$logOrder['orderId'] = $orderId;
					$logOrder['orderStatus'] = 2;
					$logOrder['logContent'] = "用户已收货";
					$logOrder['logUserId'] = $userId;
					$logOrder['logType'] = 0;
					$logOrder['logTime'] = date('Y-m-d H:i:s');
					Db::name('log_orders')->insert($logOrder);
					//给用户增加积分
					if(WSTConf("CONF.isOrderScore")==1){
						$score = [];
						$score['userId'] = $userId;
						$score['score'] = $order['orderScore'];
						$score['dataSrc'] = 1;
						$score['dataId'] = $orderId;
						$score['dataRemarks'] = "交易订单【".$order['orderNo']."】获得积分".$order['orderScore']."个";
						$score['scoreType'] = 1;
						$score['createTime'] = date('Y-m-d H:i:s');
						model('UserScores')->save($score);
						// 增加用户积分
						model('Users')->where("userId=$userId")->setInc('userScore',$order['orderScore']);
						// 用户总积分
						model('Users')->where("userId=$userId")->setInc('userTotalScore',$order['orderScore']);
					}
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
	/**
	 * 用户取消订单
	 */
	public function cancel(){
		$orderId = (int)input('post.id');
		$reason = (int)input('post.reason');
		$content = input('post.content');
		$userId = (int)session('WST_USER.userId');
		$order = $this->where(['userId'=>$userId,'orderId'=>$orderId,'orderStatus'=>['in',[-2,0]]])
		              ->field('orderId,orderNo,realTotalMoney,orderStatus,payType,isPay')->find();
		$reasonData = WSTDatas(1,$reason);
		if(empty($reasonData))return WSTReturn("无效的取消原因");
		if(!empty($order)){
			Db::startTrans();
		    try{
				$data = ['orderStatus'=>-1,'cancelReason'=>$reason];
			    $result = $this->where('orderId',$order['orderId'])->update($data);
				if(false != $result){
					//返还商品库存
					$goods = Db::name('order_goods')->alias('og')->join('__GOODS__ g','og.goodsId=g.goodsId','inner')
					           ->where('orderId',$orderId)->field('og.*')->select();
					foreach ($goods as $key => $v){
						//修改库存
						Db::name('goods')->where('goodsId',$v['goodsId'])->setInc('goodsStock',$v['goodsNum']);
					}
					//新增订单日志
					$logOrder = [];
					$logOrder['orderId'] = $orderId;
					$logOrder['orderStatus'] = -1;
					$logOrder['logContent'] = "用户取消订单，取消原因：".$reasonData['dataName'];
					$logOrder['logUserId'] = $userId;
					$logOrder['logType'] = 0;
					$logOrder['logTime'] = date('Y-m-d H:i:s');
					Db::name('log_orders')->insert($logOrder);
					//如果是在线支付并且已经支付了的订单还要生成退款单
					if($order['orderStatus'] == 0 && $order['payType']==1 && $order['isPay']==1){
						$data = [];
					    $data['orderId'] = $orderId;	
		                $data['refundTo'] = 0;
		                $data['refundReson'] = 10000;
		                $data['refundOtherReson'] = '【用户取消订单】：'.$reasonData['dataName'];
		                if($reason==10000)$data['refundOtherReson'] = $content;
		                $data['backMoney'] = $order['realTotalMoney'];
		                $data['createTime'] = date('Y-m-d H:i:s');
		                $data['refundStatus'] = 0;
		                Db::name('order_refunds')->insert($data);
	                }
					Db::commit();
					return WSTReturn('订单取消成功',1);
				}
			}catch (\Exception $e) {
		        Db::rollback();
	            return WSTReturn('操作失败',-1);
	        }
		}
		return WSTReturn('操作失败，请检查订单状态是否已改变');
	}
	/**
	 * 用户拒收订单
	 */
	public function reject(){
		$orderId = (int)input('post.id');
		$reason = (int)input('post.reason');
		$content = input('post.content');
		$userId = (int)session('WST_USER.userId');
		$order = $this->where(['userId'=>$userId,'orderId'=>$orderId,'orderStatus'=>1])
		              ->field('orderId,orderNo,realTotalMoney,payType,isPay')->find();
		$reasonData = WSTDatas(2,$reason);
		if(empty($reasonData))return WSTReturn("无效的拒收原因");
		if($reason==10000 && $content=='')return WSTReturn("请输入拒收原因");
		if(!empty($order)){
			Db::startTrans();
		    try{
				$data = ['orderStatus'=>-3,'rejectReason'=>$reason];
				if($reason==10000)$data['rejectOtherReason'] = $content;
			    $result = $this->where('orderId',$order['orderId'])->update($data);
				if(false != $result){
					//新增订单日志
					$logOrder = [];
					$logOrder['orderId'] = $orderId;
					$logOrder['orderStatus'] = -3;
					$logOrder['logContent'] = "用户拒收订单，拒收原因：".$reasonData['dataName'].(($reason==10000)?"-".$content:"");
					$logOrder['logUserId'] = $userId;
					$logOrder['logType'] = 0;
					$logOrder['logTime'] = date('Y-m-d H:i:s');
					Db::name('log_orders')->insert($logOrder);
					//如果是在线支付并且已经支付了的订单还要生成退款单
					if($order['payType']==1 && $order['isPay']==1){
						$data = [];
					    $data['orderId'] = $orderId;	
		                $data['refundTo'] = 0;
		                $data['refundReson'] = $reason;
		                if($reason==10000)$data['refundOtherReson'] = $content;
		                $data['backMoney'] = $order['realTotalMoney'];
		                $data['createTime'] = date('Y-m-d H:i:s');
		                $data['refundStatus'] = 0;
		                Db::name('order_refunds')->insert($data);
		            }
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
	/**
	 * 获取订单价格
	 */
	public function getMoneyByOrder($orderId = 0){
		$orderId = ($orderId>0)?$orderId:(int)input('post.id');
		return $this->where('orderId',$orderId)->field('orderId,goodsMoney,deliverMoney,totalMoney,realTotalMoney')->find();
	}
	


	
	/**
	 * 获取订单详情
	 */
	public function getByView($orderId){
		$userId = (int)session('WST_USER.userId');
		$shopId = (int)session('WST_USER.shopId');
		$orders = $this->alias('o')->join('__EXPRESS__ e','o.expressId=e.expressId','left')
		               ->join('__ORDER_REFUNDS__ orf ','o.orderId=orf.orderId and orf.refundStatus=1','left')
		               ->where('o.dataFlag=1 and o.orderId='.$orderId.' and ( o.userId='.$userId.')')
		               ->field('o.*,e.expressName,orf.refundRemark,orf.refundTime,orf.backMoney')->find();
		if(empty($orders))return WSTReturn("无效的订单信息");
		
		//获取订单信息
		$orders['log'] =Db::name('log_orders')->where('orderId',$orderId)->order('logId asc')->select();
		//获取订单商品
		$orders['goods'] = Db::name('order_goods')->where('orderId',$orderId)->order('id asc')->select();
		return $orders;
	}



	/**
	* 根据订单id获取 商品信息跟商品评价
	*/
	public function getOrderInfoAndAppr(){
		$orderId = (int)input('oId');
		$userId = (int)session('WST_USER.userId');

		$goodsInfo = Db::name('order_goods')
					->field('id,orderId,goodsName,goodsId,goodsSpecNames,goodsImg,goodsSpecId')
					->where(['orderId'=>$orderId])
					->select();
		//根据商品id 与 订单id 取评价
		$alreadys = 0;// 已评价商品数
		$count = count($goodsInfo);//订单下总商品数
		if($count>0){
			foreach($goodsInfo as $k=>$v){
				$goodsInfo[$k]['goodsSpecNames'] = str_replace('@@_@@', ';', $v['goodsSpecNames']);
				$appraise = Db::name('goods_appraises')
							->field('goodsScore,serviceScore,timeScore,content,images,createTime')
							->where(['goodsId'=>$v['goodsId'],
							         'goodsSpecId'=>$v['goodsSpecId'],
									 'orderId'=>$orderId,
									 'dataFlag'=>1,
									 'isShow'=>1,
									 'userId'=>$userId
									 ])->find();
				if(!empty($appraise)){
					++$alreadys;
					$appraise['images'] = ($appraise['images']!='')?explode(',', $appraise['images']):[];
				}
				$goodsInfo[$k]['appraise'] = $appraise;
			}
		}
		return ['count'=>$count,'Rows'=>$goodsInfo,'alreadys'=>$alreadys];

	}
	
	/**
	 * 检查订单是否已支付
	 */
	public function checkOrderPay (){
		$userId = (int)session('WST_USER.userId');
		$orderId = input("id/d",0);
		$rs = array();
		$where = ["userId"=>$userId,"dataFlag"=>1,"orderStatus"=>-2,"isPay"=>0,"payType"=>1];
		$where['orderId'] = $orderId;
		
		$rs = $this->field('orderId,orderNo')->where($where)->select();
		if(count($rs)>0){
			return WSTReturn('',1);
		}else{
			return WSTReturn('订单已支付',-1);
		}
	}
	/**
	* 获取用户各订单状态数
	*/
	public function getOrderStatusNum(){
		$userId = session('WST_USER.userId');
		$data = [];
		//  -3:用户拒收 -2:未付款的订单  -1：用户取消 0:待发货 1:配送中 2:用户确认收货
		$data['waitPay'] = Db::name('orders')->where(['userId'=>$userId,'orderStatus'=>-2,'dataFlag'=>1])->count();
		$data['waitSend'] = Db::name('orders')->where(['userId'=>$userId,'orderStatus'=>0,'dataFlag'=>1])->count();
		$data['waitReceive'] = Db::name('orders')->where(['userId'=>$userId,'orderStatus'=>1,'dataFlag'=>1])->count();
		$data['waitAppr'] = Db::name('orders')->where(['userId'=>$userId,'orderStatus'=>2,'isAppraise'=>0,'dataFlag'=>1])->count();
		$data['reject'] = Db::name('orders')->where(['userId'=>$userId,'orderStatus'=>-3,'dataFlag'=>1])->count();
		return $data;
	}
	

	/**
	 * 完成支付订单
	 */
	public function complatePay ($obj){
		$trade_no = $obj["trade_no"];
		$orderId = $obj["out_trade_no"];
		$userId = (int)$obj["userId"];
		$payFrom = (int)$obj["payFrom"];
		$payMoney = (float)$obj["total_fee"];
	
		if($payFrom>0){
			$cnt = model('orders')
			->where(['payFrom'=>$payFrom,"userId"=>$userId,"tradeNo"=>$trade_no])
			->count();
			if($cnt>0){
				return WSTReturn('订单已支付',-1);
			}
		}
		$where = ["userId"=>$userId,"dataFlag"=>1,"orderStatus"=>-2,"isPay"=>0,"payType"=>1,"needPay"=>[">",0]];
		$where['orderId'] = $orderId;
		
		$needPay = model('orders')->where($where)->sum('needPay');
	
		if($needPay>$payMoney){
			return WSTReturn('支付金额不正确',-1);
		}
		Db::startTrans();
		try{
			$data = array();
			$data["needPay"] = 0;
			$data["isPay"] = 1;
			$data["orderStatus"] = 0;
			$data["tradeNo"] = $trade_no;
			$data["payFrom"] = $payFrom;
			$rs = model('orders')->where($where)->update($data);
				
			if($needPay>0 && false != $rs){
				$where = ["o.userId"=>$userId];
				$where["orderId"] = $orderId;
			}
			//新增订单日志
			$logOrder = [];
			$logOrder['orderId'] = $orderId;
			$logOrder['orderStatus'] = 0;
			$logOrder['logContent'] = "支付成功，等待商家发货";
			$logOrder['logUserId'] = $userId;
			$logOrder['logType'] = 0;
			$logOrder['logTime'] = date('Y-m-d H:i:s');
			Db::name('log_orders')->insert($logOrder);

			Db::commit();
			return WSTReturn('支付成功',1);
		}catch (\Exception $e) {
			Db::rollback();
			return WSTReturn('操作失败',-1);
		}
	}
	
	/**
	 * 获取支付订单信息
	 */
	public function getPayOrders ($obj){
		$userId = (int)$obj["userId"];
		$orderId = $obj["orderId"];
		$needPay = 0;
		$where = ["userId"=>$userId,"dataFlag"=>1,"orderStatus"=>-2,"isPay"=>0,"payType"=>1,"needPay"=>[">",0]];
		$where['orderId'] = $orderId;
		$data = array();
		$data["needPay"] = model('orders')->where($where)->sum('needPay');
		return $data;
	}
	
}
