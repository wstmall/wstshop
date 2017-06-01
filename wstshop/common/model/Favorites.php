<?php
namespace wstshop\common\model;
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
 * 收藏类
 */
class Favorites extends Base{
	/**
	 * 关注的商品列表
	 */
	public function listGoodsQuery(){
		$pagesize = input("param.pagesize/d");
		$userId = (int)session('WST_USER.userId');
		$page = Db::name("favorites")->alias('f')
    	->join('__GOODS__ g','g.goodsId = f.goodsId','left')
    	->field('f.favoriteId,g.goodsId,g.goodsName,g.goodsImg,g.shopPrice,g.marketPrice,g.saleNum,g.appraiseNum')
    	->where(['f.userId'=> $userId])
    	->order('f.favoriteId desc')
    	->paginate($pagesize)->toArray();
		return $page;
	}
	
	/**
	 * 取消关注
	 */
	public function del(){
		$id = input("param.id");
		$type = input("param.type/d");
		$userId = (int)session('WST_USER.userId');
		$ids = explode(',',$id);
		
		if(empty($ids))return WSTReturn("取消失败", -1);
		$rs = $this->where(['favoriteId'=> ['in',$ids],'userId'=>$userId])->delete();
		if(false !== $rs){
			return WSTReturn("取消成功", 1);
		}else{
			return WSTReturn($this->getError(),-1);
		}
	}

	
	/**
	 * 新增关注
	 */
	public function add(){
	    $id = input("param.id/d");
		$type = input("param.type/d");
		$userId = (int)session('WST_USER.userId');
		if($userId==0)return WSTReturn('关注失败，请先登录');
		//判断记录是否存在
		$isFind = false;
		if($type==0){
			$c = Db::name('goods')->where(['dataFlag'=>1,'goodsId'=>$id])->count();
			$isFind = ($c>0);
		}
		if(!$isFind)return WSTReturn("关注失败，无效的关注对象", -1);
		$data = [];
		$data['userId'] = $userId;
		$data['goodsId'] = $id;
		//判断是否已关注
		$rc = $this->where($data)->count();
		if($rc>0)return WSTReturn("关注成功", 1);
		$data['createTime'] = date('Y-m-d H:i:s');
		$rs = $this->save($data);
		if(false !== $rs){
			return WSTReturn("关注成功", 1,['fId'=>$this->favoriteId]);
		}else{
			return WSTReturn($this->getError(),-1);
		}
	}
	/**
	 * 判断是否已关注
	 */
	public function checkFavorite($id){
		$rs = $this->where(['userId'=>(int)session('WST_USER.userId'),'goodsId'=>$id])->find();
		return empty($rs)?0:$rs['favoriteId'];
	}
}
