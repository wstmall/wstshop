<?php
namespace wstshop\home\controller;
use wstshop\common\model\Brands as M;
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
 * 品牌控制器
 */
class Brands extends Base{
	/**
	 * 品牌街
	 */
	public function index(){
		$m = new M();
		$pagesize = 25;
		$brandsList = $m->pageQuery($pagesize);
		$this->assign('list',$brandsList);

		$g = model('goodsCats');
		$goodsCats = $g->listQuery(0);
    	$this->assign('goodscats',$goodsCats);

    	$selectedId = (int)input("id");
    	$this->assign('selectedId',$selectedId);
		return $this->fetch('brands_list');
	}
}
