<?php
namespace wstshop\home\controller;
use wstshop\common\model\Favorites as M;
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
 * 收藏控制器
 */
class Favorites extends Base{
	/**
	 * 关注的商品
	 */
	public function goods(){
		return $this->fetch('users/favorites/list_goods');
	}
	/**
	 * 关注的商品列表
	 */
	public function listGoodsQuery(){
		$m = new M();
		$data = $m->listGoodsQuery();
		return WSTReturn("", 1,$data);
	}
	/**
	 * 取消关注
	 */
	public function cancel(){
		$m = new M();
		$rs = $m->del();
		return $rs;
	}
	/**
	 * 增加关注
	 */
	public function add(){
		$m = new M();
		$rs = $m->add();
		return $rs;
	}
}
