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
 * 支付管理业务处理
 */
class Payments extends Base{
	/**
	 * 获取支付方式种类
	 */
	public function getByGroup($fielterCode = ''){
		$payments = ['0'=>[],'1'=>[]];
		$rs = $this->where(['enabled'=>1])->order('payOrder asc')->select();
		foreach ($rs as $key =>$v){
			if($fielterCode==$v['payCode'])continue;
			if($v['payConfig']!='')$v['payConfig'] = json_decode($v['payConfig'], true);
			$payments[$v['isOnline']][] = $v;
		}
		return $payments;
	}

	
	/**
	 * 获取支付信息
	 */
	public function getPayment($payCode){
		$payment = $this->where("enabled=1 AND payCode='$payCode' AND isOnline=1")->find();
		$payConfig = json_decode($payment["payConfig"]) ;
		foreach ($payConfig as $key => $value) {
			$payment[$key] = $value;
		}
		return $payment;
	}

}
