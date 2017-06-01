<?php
namespace wstshop\home\controller;
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
 * 默认控制器
 */
class Index extends Base{
	
    public function index(){
        $categorys = model('GoodsCats')->getFloors();
        $this->assign('floors',$categorys);
        // 获取分类
        $cat = WSTSideCategorys();
        $catNames = [];
        foreach($cat as $k=>$v){
            $catNames[$k]['catId'] = $v['catId'];
            $catNames[$k]['catName'] = $v['catName'];
        }
        $this->assign('catNames',$catNames);
        // 获取最新高评分评价
        $appraise = model('GoodsAppraises')->getNewAppr(5);
        $this->assign('appraise',$appraise);



    	$this->assign('hideCategory',1);
    	return $this->fetch('index');
    }
    /**
     * 保存目录ID
     */
    public function getMenuSession(){
    	$menuId = input("post.menuId");
    	$menuType = session('WST_USER.loginTarget');
    	session('WST_MENUID3'.$menuType,$menuId);
    } 
    /**
     * 获取用户信息
     */
    public function getSysMessages(){
    	$rs = model('Systems')->getSysMessages();
    	return $rs;
    }
    /**
     * 定位菜单以及跳转页面
     */
    public function position(){
    	$menuId = (int)input("post.menuId");
    	$menuType = ((int)input("post.menuType")==1)?1:0;
    	session('WST_MENUID3'.$menuType,$menuId);
    }
}
