<?php
namespace wstshop\admin\controller;
use wstshop\admin\model\Goods as M;
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
 * 商品控制器
 */
class Goods extends Base{
   /**
	* 查看上架商品列表
	*/
	public function index(){
    	$this->assign("areaList",model('areas')->listQuery(0));
		return $this->fetch('list_sale');
	}
   /**
    * 批量删除商品
    */
    public function batchDel(){
        $m = new M();
        return $m->batchDel();
    }

	/**
	 * 获取上架商品列表
	 */
	public function saleByPage(){
		$m = new M();
		$rs = $m->saleByPage();
		$rs['status'] = 1;
		return $rs;
	}

    /**
     * 仓库中商品
     */
    public function store(){
        return $this->fetch('goods/list_store');
    }
    /**
     * 获取仓库中的商品列表
     */
    public function storeByPage(){
        $m = new M();
        $rs = $m->storeByPage();
        $rs['status'] = 1;
        return $rs;
    }
    /**
     * 库存预警的商品
     */
    public function warn(){
        return $this->fetch('goods/list_warn');
    }
    /**
     * 获取库存预警的商品列表
     */
    public function warnByPage(){
        $m = new M();
        $rs = $m->warnByPage();
        $rs['status'] = 1;
        return $rs;
    }
    /**
     * 修改预警库存
     */
    public function editwarnStock(){
    	$m = new M();
    	$rs = $m->editwarnStock();
    	return $rs;
    }
    
    /**
     * 跳去编辑页面
     */
    public function toView(){
    	$m = new M();
    	$object = $m->getById(input('get.id'));
    	if($object['goodsImg']=='')$object['goodsImg'] = WSTConf('CONF.goodsLogo');
    	$data = ['object'=>$object];
    	return $this->fetch('default/shops/goods/edit',$data);
    }
    /**
     * 跳去新增商品
     */
    public function toAdd(){
        $m = new M();
        $object = $m->getEModel('goods');
        $object['goodsSn'] = WSTGoodsNo();
        $object['productNo'] = WSTGoodsNo();
        $object['goodsImg'] = WSTConf('CONF.goodsLogo');
        $data = ['object'=>$object,'src'=>input('src','toAdd')];
        return $this->fetch('goods/edit',$data);
    }
    /**
     * 新增商品
     */
    public function add(){
        $m = new M();
        return $m->add();
    }
    /**
     * 编辑商品
     */
    public function toEdit(){
    	$m = new M();
        $object = $m->getById(input('get.id'));
        if($object['goodsImg']=='')$object['goodsImg'] = WSTConf('CONF.goodsLogo');
        $data = ['object'=>$object,'src'=>input('src','toAdd')];
        return $this->fetch('goods/edit',$data);
    }

    /**
     * 编辑商品
     */
    public function edit(){
        $m = new M();
        return $m->edit();
    }
    /**
     * 删除商品
     */
    public function del(){
    	$m = new M();
    	return $m->del();
    }
    /**
     * 获取商品规格属性
     */
    public function getSpecAttrs(){
        $m = new M();
        return $m->getSpecAttrs();
    }

    /**
     * 修改商品库存/价格
     */
    public function editGoodsBase(){
        $m = new M();
        return $m->editGoodsBase();
    }

    /**
    * 修改商品状态
    */
    public function changSaleStatus(){
        $m = new M();
        return $m->changSaleStatus();
    }
    /**
    * 批量修改商品状态 新品/精品/热销/推荐
    */
    public function changeGoodsStatus(){
         $m = new M();
        return $m->changeGoodsStatus();
    }
    /**
    *   批量上(下)架
    */
    public function changeSale(){
        $m = new M();
        return $m->changeSale();
    }
}
