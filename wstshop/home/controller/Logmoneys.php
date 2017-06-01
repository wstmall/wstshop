<?php
namespace wstshop\home\controller;
use wstshop\common\model\LogMoneys as M;
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
 * 资金流水控制器
 */
class Logmoneys extends Base{
    /**
     * 查看用户资金流水
     */
	public function usermoneys(){
		$rs = model('Users')->getFieldsById((int)session('WST_USER.userId'),['lockMoney','userMoney']);
		$this->assign('object',$rs);
		return $this->fetch('users/logmoneys/list');
	}
    /**
     * 获取用户数据
     */
    public function pageUserQuery(){
        $userId = (int)session('WST_USER.userId');
        $data = model('logMoneys')->pageQuery(0,$userId);
        return WSTReturn("", 1,$data);
    }
}
