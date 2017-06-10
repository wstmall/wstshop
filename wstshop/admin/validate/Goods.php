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
 * 商品验证器
 */
class Goods extends Validate{
	protected $rule = [
        ['goodsName'  ,'require|max:300','请输入商品编号|商品名称不能超过100个字符'],
        ['goodsImg'  ,'require','请上传商品图片'],
        ['goodsSn'  ,'checkGoodsSn:1','请输入商品编号'],
        ['productNo'  ,'checkProductNo:1','请输入商品货号'],
        ['marketPrice'  ,'checkMarketPrice:1','请输入市场价格'],
        ['shopPrice'  ,'checkShopPrice:1','请输入店铺价格'],
        ['goodsUnit'  ,'require','请输入商品单位'],
        ['isSale'  ,'in:,0,1','无效的上架状态'],
        ['isRecom'  ,'in:,0,1','无效的推荐状态'],
        ['isBest'  ,'in:,0,1','无效的精品状态'],
        ['isNew'  ,'in:,0,1','无效的新品状态'],
        ['isHot'  ,'in:,0,1','无效的热销状态'],
        ['goodsCatId'  ,'require','请选择完整商品分类'],
        ['goodsDesc','require','请输入商品描述'],
    ];
    /**
     * 检测商品编号
     */
    protected function checkGoodsSn($value){
    	$goodsId = Input('post.goodsId/d',0);
    	$key = Input('post.goodsSn');
    	if($key=='')return '请输入商品编号';
    	$isChk = model('Goods')->checkExistGoodsKey('goodsSn',$key,$goodsId);
    	if($isChk)return '对不起，该商品编号已存在';
    	return true;
    }
    /**
     * 检测价格
     */
    public function checkMarketPrice(){
        $marketPrice = floatval(input('post.marketPrice'));
        if($marketPrice<0.01)return '市场价格不能小于0.01';
        return true;
    }
    public function checkShopPrice(){
        $shopPrice = floatval(input('post.shopPrice'));
        if($shopPrice<0.01)return '店铺价格不能小于0.01';
        return true;
    }
    /**
     * 检测商品货号
     */
    protected function checkProductNo($value){
    	$goodsId = Input('post.goodsId/d',0);
    	$key = Input('post.productNo');
    	if($key=='')return '请输入商品货号';
    	$isChk = model('Goods')->checkExistGoodsKey('productNo',$key,$goodsId);
    	if($isChk)return '对不起，该商品货号已存在';
    	return true;
    }
    
}