<?php
namespace wstshop\common\model;
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
 * 某些较杂业务处理类
 */
use think\db;
class Systems extends Base{
	/**
	 * 获取定时任务
	 */
	public function getSysMessages(){
		$tasks = strtolower(input('post.tasks'));
		$tasks = explode(',',$tasks);
		$userId = (int)session('WST_USER.userId');
		$shopId = (int)session('WST_USER.shopId');
		$data = [];
		if(in_array('message',$tasks)){
		    //获取用户未读消息
		    $data['message']['num'] = Db::name('messages')->where(['receiveUserId'=>$userId,'msgStatus'=>0,'dataFlag'=>1])->count();
		    $data['message']['id'] = 49;
		}
		//获取用户订单状态
	    if(in_array('userorder',$tasks)){
		    $data['userorder']['3'] = Db::name('orders')->where(['userId'=>$userId,'orderStatus'=>-2,'dataFlag'=>1])->count();
		    $data['userorder']['5'] = Db::name('orders')->where(['userId'=>$userId,'orderStatus'=>['in',[1]],'dataFlag'=>1])->count();
		    $data['userorder']['6'] = Db::name('orders')->where(['userId'=>$userId,'orderStatus'=>2,'isAppraise'=>0,'dataFlag'=>1])->count();
		    $data['userorder']['63'] = Db::name('orders')->where(['userId'=>$userId,'orderStatus'=>0,'dataFlag'=>1])->count();
		}
		//获取用户购物车数量
		if(in_array('cart',$tasks)){
			$cartNum = 0;
			$cartGoodsNum = 0;
			$rs = Db::name('carts')->field('cartNum')->where(['userId'=>$userId])->select();
			foreach($rs as $key => $v){
				$cartGoodsNum++;
				$cartNum = $cartNum + $v['cartNum'];
			}
			$data['cart']['goods'] = $cartGoodsNum;
			$data['cart']['num'] = $cartNum;
		}
		return $data;
	}
}
