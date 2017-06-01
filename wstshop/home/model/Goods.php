<?php
namespace wstshop\home\model;
use wstshop\common\model\Goods as CGoods;
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
 * 商品类
 */
class Goods extends CGoods{
	/**
	 * 获取商品资料在前台展示
	 */
     public function getBySale($goodsId){
     	$key = input('key');
     	// 浏览量
     	$this->where('goodsId',$goodsId)->setInc('visitNum',1);
		$rs = Db::name('goods')->where(['goodsId'=>$goodsId,'dataFlag'=>1])->find();

		

		if(!empty($rs)){
			$rs['read'] = false;
			//判断是否可以公开查看
			$viKey = WSTShopEncrypt($rs['goodsId']);
			if(($rs['isSale']==0 ) && $viKey != $key)return [];
			if($key!='')$rs['read'] = true;

			$gallery = [];
			$gallery[] = $rs['goodsImg'];
			if($rs['gallery']!=''){
				$tmp = explode(',',$rs['gallery']);
				$gallery = array_merge($gallery,$tmp);
			}
			$rs['gallery'] = $gallery;
			//获取商品属性
			$rs['attrs'] = Db::name('attributes')->alias('a')->join('goods_attributes ga','a.attrId=ga.attrId','inner')
			                   ->where(['a.isShow'=>1,'dataFlag'=>1,'goodsId'=>$goodsId])->field('a.attrName,ga.attrVal')
			                   ->order('attrSort asc')->select();
			//获取商品评分
			$rs['scores'] = Db::name('goods_scores')->where('goodsId',$goodsId)->field('totalScore,totalUsers')->find();
			$rs['scores']['totalScores'] = ($rs['scores']['totalScore']==0)?5:WSTScore($rs['scores']['totalScore'],$rs['scores']['totalUsers'],5,0,3);
			WSTUnset($rs, 'totalUsers');
			//关注
			$rs['favGood'] = model('Favorites')->checkFavorite($goodsId);
		}
		return $rs;
	}
	
	
	

	
    /**
     * 获取符合筛选条件的商品ID
     */
    public function filterByAttributes(){
    	$vs = input('vs');
    	if($vs=='')return [];
    	$vs = explode(',',$vs);
    	$goodsIds = [];
    	$prefix = config('database.prefix');
		//循环遍历每个属性相关的商品ID
	    foreach ($vs as $v){
	    	$goodsIds2 = [];
	    	$attrVal = input('v_'.(int)$v);
	    	if($attrVal=='')continue;
	    	$sql = "select goodsId goodsId from ".$prefix."goods_attributes 
	    	where attrId=".(int)$v." and find_in_set('".$attrVal."',attrVal) ";
			$rs = Db::query($sql);
			if(!empty($rs)){
				foreach ($rs as $vg){
					$goodsIds2[] = $vg['goodsId'];
				}
			}
			//如果有一个属性是没有商品的话就不需要查了
			if(empty($goodsIds2))return [-1];
			//第一次比较就先过滤，第二次以后的就找集合
			$goodsIds2[] = -1;
			if(empty($goodsIds)){
				$goodsIds = $goodsIds2;
			}else{

				$goodsIds = array_intersect($goodsIds,$goodsIds2);
			}
		}
		return $goodsIds;
    }
	
	/**
	 * 获取分页商品记录
	 */
	public function pageQuery($goodsCatIds = []){
		//查询条件
		$isStock = input('isStock/d');
		$isNew = input('isNew/d');
		$keyword = input('keyword');
		$brandId = input('brand/d');
		if($brandId>0)$where['g.brandId'] = $brandId;
		$where['g.dataFlag'] = 1;
		$where['isSale'] = 1;

		// 筛选条件左侧的搜索
		$goodsName = input('goodsName');
		if($keyword!='')$where['goodsName'] = ['like','%'.$keyword.'%'];
		if($goodsName!='')$where['goodsName'] = ['like','%'.$goodsName.'%'];

		//属性筛选
		$goodsIds = $this->filterByAttributes();
		if(!empty($goodsIds))$where['goodsId'] = ['in',$goodsIds];
		//排序条件
		$orderBy = input('orderBy/d',0);
		$orderBy = ($orderBy>=0 && $orderBy<=4)?$orderBy:0;
		$order = (input('order/d',0)==1)?1:0;
		$pageBy = ['saleNum','shopPrice','appraiseNum','visitNum','saleTime'];
		$pageOrder = ['asc','desc'];
		if($isStock==1)$where['goodsStock'] = ['>',0];
		if($isNew==1)$where['isNew'] = ['=',1];
		if(!empty($goodsCatIds))$where['goodsCatIdPath'] = ['like',implode('_',$goodsCatIds).'_%'];
		$sprice = input("param.sprice");//开始价格
	    $eprice = input("param.eprice");//结束价格
	    $where2 = $where3 =[];
		if($sprice!="")$where2 = "g.shopPrice >= ".(float)$sprice;
		if($eprice!="")$where3 = "g.shopPrice <= ".(float)$eprice;
		$list = Db::name("goods")->alias('g')
			->where($where)->where($where2)->where($where3)
			->field('goodsId,goodsName,goodsSn,goodsStock,saleNum,shopPrice,marketPrice,goodsImg,appraiseNum,visitNum')
			->order($pageBy[$orderBy]." ".$pageOrder[$order].",goodsId asc")
			->paginate(input('pagesize/d'))->toArray();

		return $list;
	}
	/**
	 * 获取价格范围
	 */
	public function getPriceGrade($goodsCatIds = []){
		$isStock = input('isStock/d');
		$isNew = input('isNew/d');
		$keyword = input('keyword');
		$where = $where2 = $where3 = [];
		$where['g.dataFlag'] = 1;
		$where['isSale'] = 1;
		if($keyword!='')$where['goodsName'] = ['like','%'.$keyword.'%'];
        //属性筛选
		$goodsIds = $this->filterByAttributes();
		if(!empty($goodsIds))$where['goodsId'] = ['in',$goodsIds];
		//排序条件
		$orderBy = input('orderBy/d',0);
		$orderBy = ($orderBy>=0 && $orderBy<=4)?$orderBy:0;
		$order = (input('order/d',0)==1)?1:0;
		$pageBy = ['saleNum','shopPrice','appraiseNum','visitNum','saleTime'];
		$pageOrder = ['asc','desc'];
		if($isStock==1)$where['goodsStock'] = ['>',0];
		if($isNew==1)$where['isNew'] = ['=',1];
		if(!empty($goodsCatIds))$where['goodsCatIdPath'] = ['like',implode('_',$goodsCatIds).'_%'];
		$sprice = input("param.sprice");//开始价格
	    $eprice = input("param.eprice");//结束价格
	    $where2 = $where3 =[];
		if($sprice!="")$where2 = "g.shopPrice >= ".(float)$sprice;
		if($eprice!="")$where3 = "g.shopPrice <= ".(float)$eprice;
		$rs = Db::name("goods")->alias('g')
			->where($where)->where($where2)->where($where3)
			->field('min(shopPrice) minPrice,max(shopPrice) maxPrice')->find();
		
		if($rs['maxPrice']=='')return;
		$minPrice = 0;
		$maxPrice = $rs['maxPrice'];
		$pavg5 = ($maxPrice/5);
		$prices = array();
    	$price_grade = 0.0001;
        for($i=-2; $i<= log10($maxPrice); $i++){
            $price_grade *= 10;
        }
    	//区间跨度
        $span = ceil(($maxPrice - $minPrice) / 8 / $price_grade) * $price_grade;
        if($span == 0){
            $span = $price_grade;
        }
		for($i=1;$i<=8;$i++){
			$prices[($i-1)*$span."_".($span * $i)] = ($i-1)*$span."-".($span * $i);
			if(($span * $i)>$maxPrice) break;
		}

		return $prices;
	}

	
}
