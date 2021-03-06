<?php 
namespace wstshop\admin\validate;
use think\Validate;
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
 * 菜单验证器
 */
class HomeMenus extends Validate{
	protected $rule = [
        ['menuName'  ,'require|max:30','请输入菜单名称|菜单名称不能超过10个字符'],
		['parentId'  ,'number','无效的父级菜单'],
		['menuUrl'   ,'require','请输入菜单Url'],
		['isShow'    ,'require','请选择是否显示']
    ];

    protected $scene = [
        'add'   =>  ['menuName','parentId','menuType','menuUrl','isShow'],
        'edit'  =>  ['menuName','menuType','menuUrl','isShow']
    ]; 
}