<?php
namespace wstshop\admin\controller;
use wstshop\admin\model\Freights as M;
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
 * 运费控制器
 */
class Freights extends Base{
    /**
    * 查看运费设置
    */
	public function index(){
		$deFreightc = WSTConf('CONF.defaultFreight');
		$deFreight = empty($deFreightc)?0:$deFreightc;
		$this->assign('deFreight',$deFreight);//默认运费
		return $this->fetch('freights/list');
	}
	
	/**
	 * 获取分页
	 */
	public function pageQuery(){
		$m = new M();
		$rs = $m->pageQuery();
		return $rs;
	}

    /**
     * 编辑
     */
    public function edit(){
    	$m = new M();
    	$rs = $m->edit();
    	return $rs;
    }
}
