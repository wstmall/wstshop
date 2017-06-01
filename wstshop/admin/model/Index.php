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
 * 系统业务处理
 */
class Index extends Base{
    /**
	 * 清除缓存
	 */
	public function clearCache(){
		$dirpath = WSTRootPath()."/runtime/cache";
		$isEmpty = WSTDelDir($dirpath);
		return $isEmpty;
	}
	/**
	 * 获取基础统计信息
	 */
	public function summary(){
		$data = [];
		//今日统计
		//会员
		$data['tody']['newUser'] = Db::name('users')->where(['dataFlag'=>1,'createTime'=>['like',date('Y-m-d').'%']])->count();
		$data['tody']['loginUser'] = Db::name('log_user_logins')->where(['loginTime'=>['like',date('Y-m-d').'%']])->distinct(true)->field('userId')->count();
		$data['tody']['order'] = Db::name('orders')->where(['dataFlag'=>1,'createTime'=>['like',date('Y-m-d').'%']])->count();
		// 待发货”、“待付款”、   “取消/拒收”、“待退款”、   "待提现"以及“新增评价”统计展示。取消/拒收订单”直接查订单日志
		// 订单相关
		$data['tody']['waitDelivery'] = Db::name('orders')->where(['orderStatus'=>0,'dataFlag'=>1])->count();
		$data['tody']['waitPay'] = Db::name('orders')->where(['orderStatus'=>-2,'dataFlag'=>1])->count();
		$data['tody']['cancel'] = Db::name('log_orders')->where(['orderStatus'=>['in',[-2,-3]],'logTime'=>['like',date('Y-m-d').'%']])->count();
		$data['tody']['refund'] = Db::name('orders')->alias('o')->join('order_refunds orf','orf.orderId=o.orderId')->where(['refundStatus'=>0,'o.dataFlag'=>1,'orf.createTime'=>['like',date('Y-m-d').'%']])->count();
		$data['tody']['appraise'] = Db::name('goods_appraises')->where(['dataFlag'=>1,'createTime'=>['like',date('Y-m-d').'%']])->count();
		$data['tody']['drawscash'] = Db::name('cash_draws')->where(['cashSatus'=>0,'createTime'=>['like',date('Y-m-d').'%']])->count();
		//商城统计
		$data['totalGoods'] = Db::name('goods')->where(['dataFlag'=>1])->count();
		$data['saleGoods'] = Db::name('goods')->where(['dataFlag'=>1,'isSale'=>1])->count();
		$data['storeGoods'] = Db::name('goods')->where(['dataFlag'=>1,'isSale'=>0])->count();
		$data['order'] = Db::name('orders')->where(['dataFlag'=>1])->count();
		$data['brands'] = Db::name('brands')->where(['dataFlag'=>1])->count();
		$data['appraise'] = Db::name('goods_appraises')->where(['dataFlag'=>1])->count();
		$rs = Db::query('select VERSION() as sqlversion');
		$data['MySQL_Version'] = $rs[0]['sqlversion'];
		return $data;
	}
	
    /**
	 * 保存授权码
	 */
	public function saveLicense(){
		$data = [];
		$data['fieldValue'] = input('license');
	    $result = model('SysConfigs')->where('fieldCode','shopLicense')->update($data);
		if(false !== $result){
			cache('WST_CONF',null);
			return WSTReturn("操作成功",1);
		}
		return WSTReturn("操作失败");
	}

	/**
	* 获取各订单状态数
	*/
	public function getSysMsg(){
		$task = input('task');
		$data = [];
		if($task=='order'){
			// 待发货
			$data['order']['waitDelivery'] = Db::name('orders')->where(['orderStatus'=>0,'dataFlag'=>1])->count();
			// 待支付
			$data['order']['waitPay'] = Db::name('orders')->where(['orderStatus'=>-2,'dataFlag'=>1])->count();
			// 用户拒收并且未退款
			$data['order']['refund'] = Db::name('orders')->alias('o')->join('order_refunds orf','orf.orderId=o.orderId')->where(['refundStatus'=>0,'o.dataFlag'=>1])->count();
		}elseif($task=='warnStock'){
			//获取库存预警数量
			$data['warnStock'] = Db::name('goods')->where('dataFlag = 1 and goodsStock <= warnStock and warnStock>0')->count();
		}
		return $data;
	}
}