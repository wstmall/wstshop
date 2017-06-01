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
 * 购物车业务处理类
 */

class Carts extends Base{
	
	/**
	 * 加入购物车
	 */
	public function addCart(){
		$userId = (int)session('WST_USER.userId');
		$goodsId = (int)input('post.goodsId');
		$goodsSpecId = (int)input('post.goodsSpecId');
		$cartNum = (int)input('post.buyNum',1);
		$cartNum = ($cartNum>0)?$cartNum:1;
		if($userId==0)return WSTReturn('加入购物车，请先登录');
		//验证传过来的商品是否合法
		$chk = $this->checkGoodsSaleSpec($goodsId,$goodsSpecId);
		if($chk['status']==-1)return $chk;
		//检测库存是否足够
		if($chk['data']['stock']<$cartNum)return WSTReturn("加入购物车失败，商品库存不足", -1);
		$goodsSpecId = $chk['data']['goodsSpecId'];
		$goods = $this->where(['userId'=>$userId,'goodsId'=>$goodsId,'goodsSpecId'=>$goodsSpecId])->select();
		if(empty($goods)){
			$data = array();
			$data['userId'] = $userId;
			$data['goodsId'] = $goodsId;
			$data['goodsSpecId'] = $goodsSpecId;
			$data['isCheck'] = 1;
			$data['cartNum'] = $cartNum;
			$rs = $this->save($data);
			if(false !==$rs){
				return WSTReturn("添加成功", 1);
			}
		}else{
			$rs = $this->where(['userId'=>$userId,'goodsId'=>$goodsId,'goodsSpecId'=>$goodsSpecId])->setInc('cartNum',$cartNum);
		    if(false !==$rs){
				return WSTReturn("添加成功", 1);
			}
		}
		return WSTReturn("加入购物车失败", -1);
	}
	/**
	 * 验证商品是否合法
	 */
	public function checkGoodsSaleSpec($goodsId,$goodsSpecId){
		$goods = model('Goods')->where(['dataFlag'=>1,'isSale'=>1,'goodsId'=>$goodsId])->field('goodsId,goodsStock')->find();
		if(empty($goods))return WSTReturn("添加失败，无效的商品信息", -1);
		$goodsStock = (int)$goods['goodsStock'];
		return WSTReturn("", 1,['goodsSpecId'=>0,'stock'=>$goodsStock]);
	}
	/**
	 * 删除购物车里的商品
	 */
	public function delCart(){
		$userId = (int)session('WST_USER.userId');
		$id = input('post.id');
		$id = explode(',',WSTFormatIn(",",$id));
		$id = array_filter($id);
		$this->where("userId = ".$userId." and cartId in(".implode(',', $id).")")->delete();
		return WSTReturn("删除成功", 1);
	}
	/**
	 * 取消购物车商品选中状态
	 */
	public function disChkGoods($goodsId,$goodsSpecId,$userId){
		$this->save(['isCheck'=>0],['userId'=>$userId,'goodsId'=>$goodsId,'goodsSpecId'=>$goodsSpecId]);
	}
	
	/**
	 * 获取购物车列表
	 */
	public function getCarts($isSettlement = false){
		$userId = (int)session('WST_USER.userId');
		$where = [];
		$where['c.userId'] = $userId;
		if($isSettlement)$where['c.isCheck'] = 1;
		$rs = $this->alias('c')->join('__GOODS__ g','c.goodsId=g.goodsId','inner')
		           ->where($where)
		           ->field('c.goodsSpecId,c.cartId,g.goodsId,g.goodsName,g.shopPrice,g.goodsStock,g.goodsImg,c.isCheck,c.cartNum,g.goodsCatId')
		           ->select();
		$carts = [];
		$goodsIds = [];
		$goodsTotalNum = 0;
		$goodsTotalMoney = 0;
		foreach ($rs as $key =>$v){
			//判断能否购买，预设allowBuy值为10，为将来的各种情况预留10个情况值，从0到9
			$v['allowBuy'] = 10;
			if($v['goodsStock']<0){
				$v['allowBuy'] = 0;//库存不足
			}else if($v['goodsStock']<$v['cartNum']){
				//$v['allowBuy'] = 1;//库存比购买数小
				$v['cartNum'] = $v['goodsStock'];
			}
			//如果是结算的话，则要过滤了不符合条件的商品
			if($isSettlement && $v['allowBuy']!=10){
				$this->disChkGoods($v['goodsId'],(int)$v['goodsSpecId'],(int)session('WST_USER.userId'));
				continue;
			}
			if($v['isCheck']==1){
				$goodsTotalMoney = $goodsTotalMoney + $v['shopPrice'] * $v['cartNum'];
				$goodsTotalNum++;
			}
			$v['specNames'] = [];
			$carts[] = $v;
			if(!in_array($v['goodsId'],$goodsIds))$goodsIds[] = $v['goodsId'];
		}
		return ['carts'=>$carts,'goodsTotalMoney'=>$goodsTotalMoney,'goodsTotalNum'=>$goodsTotalNum];     
	}
	
	/**
	 * 获取购物车商品列表
	 */
	public function getCartInfo($isSettlement = false){
		$userId = (int)session('WST_USER.userId');
		$where = [];
		$where['c.userId'] = $userId;
		if($isSettlement)$where['c.isCheck'] = 1;
		$rs = $this->alias('c')->join('__GOODS__ g','c.goodsId=g.goodsId','inner')
		           ->where($where)
		           ->field('c.goodsSpecId,c.cartId,g.goodsId,g.goodsName,g.shopPrice,g.goodsStock,g.goodsImg,c.isCheck,c.cartNum')
		           ->select();
		$goodsIds = []; 
		$goodsTotalMoney = 0;
		$goodsTotalNum = 0;
		foreach ($rs as $key =>$v){
			if(!in_array($v['goodsId'],$goodsIds))$goodsIds[] = $v['goodsId'];
			if($v['goodsStock']<$v['cartNum']){
				$v['cartNum'] = $v['goodsStock'];
			}
			$goodsTotalMoney = $goodsTotalMoney + $v['shopPrice'] * $v['cartNum'];
			$rs[$key]['goodsImg'] = WSTImg($v['goodsImg']);
		}
		$goodsTotalNum = count($rs);
		return ['list'=>$rs,'goodsTotalMoney'=>sprintf("%.2f", $goodsTotalMoney),'goodsTotalNum'=>$goodsTotalNum];
	}
	
	/**
	 * 修改购物车商品状态
	 */
	public function changeCartGoods(){
		$isCheck = Input('post.isCheck/d',-1);
		$buyNum = Input('post.buyNum/d',1);
		if($buyNum<1)$buyNum = 1;
		$id = Input('post.id/d');
		$userId = (int)session('WST_USER.userId');
		$data = [];
		if($isCheck!=-1)$data['isCheck'] = $isCheck;
		$data['cartNum'] = $buyNum;
		$this->where(['userId'=>$userId,'cartId'=>$id])->update($data);
		return WSTReturn("操作成功", 1);
	}
}
