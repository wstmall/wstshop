<?php
namespace wstshop\admin\controller;
use wstshop\admin\model\Orders as M;
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
	 * 待发货订单列表
	 */
    public function waitDelivery(){
        $express = model('common/Express')->listQuery();
        $this->assign('express',$express);
    	return $this->fetch("list_wait_delivery");
    }
    /**
     * 获取分页
     */
    public function waitDeliveryByPage(){
        $m = new M();
        return $m->pageQuery(0);
    }
    /**
     * 待付款订单列表
     */
    public function waitPay(){
        return $this->fetch("list_wait_pay");
    }
    /**
     * 获取分页
     */
    public function waitPayByPage(){
        $m = new M();
        return $m->pageQuery(-2,1);
    }
    /**
     * 已发货订单列表
     */
    public function delivery(){
        return $this->fetch("list_delivery");
    }
    /**
     * 获取分页
     */
    public function deliveryByPage(){
        $m = new M();
        return $m->pageQuery(1);
    }
    /**
     * 取消/拒收订单列表
     */
    public function abnormal(){
        return $this->fetch("list_abnormal");
    }
    /**
     * 获取分页
     */
    public function abnormalByPage(){
        $orderStatus = (int)input('orderStatus',10000);
        if(!in_array($orderStatus,[10000,-1,-3]) || $orderStatus == 10000)$orderStatus = [-1,-3];
        $m = new M();
        return $m->pageQuery($orderStatus);
    }
    /**
     * 取消/拒收订单列表
     */
    public function received(){
        return $this->fetch("list_received");
    }
    /**
     * 获取分页
     */
    public function receivedByPage(){
        $m = new M();
        return $m->pageQuery(2);
    }
   /**
    * 获取订单详情
    */
    public function view(){
        $m = new M();
        $rs = $m->getByView(Input("id/d",0));
        $this->assign("object",$rs);
        return $this->fetch("view");
    }
    /**
     * 删除订单
     */
    public function del(){
        $m = new M();
        $rs = $m->del();
        return $rs;
    }
    /**
     * 发货
     */
    public function deliver(){
        $m = new M();
        $rs = $m->deliver();
        return $rs;
    }
}

