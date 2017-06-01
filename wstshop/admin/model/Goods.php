<?php
namespace wstshop\admin\model;
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
class Goods extends Base{
	/**
	 * 新增商品
	 */
	public function add(){
		$data = input('post.');
		WSTUnset($data,'goodsId,statusRemarks,goodsStatus,dataFlag');
		$data['saleTime'] = date('Y-m-d H:i:s');
		$data['createTime'] = date('Y-m-d H:i:s');
		$goodsCats = model('GoodsCats')->getParentIs($data['goodsCatId']);		
		$data['goodsCatIdPath'] = implode('_',$goodsCats)."_";
		Db::startTrans();
        try{
			$result = $this->validate(true)->allowField(true)->save($data);
			if(false !== $result){
				$goodsId = $this->goodsId;
				//商品图片
				WSTUseImages(0, $goodsId, $data['goodsImg']);
				//商品相册
				WSTUseImages(0, $goodsId, $data['gallery']);
				//商品描述图片
				WSTEditorImageRocord(0, $goodsId, '',$data['goodsDesc']);

				//建立商品评分记录
				$gs = [];
				$gs['goodsId'] = $goodsId;
				Db::name('goods_scores')->insert($gs);

    	        //保存商品属性
		    	$attrsArray = [];
		    	$attrRs = Db::name('attributes')->where(['goodsCatId'=>['in',$goodsCats],'isShow'=>1,'dataFlag'=>1])
		    		            ->field('attrId')->select();
		    	foreach ($attrRs as $key =>$v){
		    		$attrs = [];
		    		$attrs['attrVal'] = input('attr_'.$v['attrId']);
		    		if($attrs['attrVal']=='')continue;
		    		$attrs['goodsId'] = $goodsId;
		    		$attrs['attrId'] = $v['attrId'];
		    		$attrs['createTime'] = date('Y-m-d H:i:s');
		    		$attrsArray[] = $attrs;
		    	}
		    	if(count($attrsArray)>0)Db::name('goods_attributes')->insertAll($attrsArray);
    	        Db::commit();				
    	        return WSTReturn("新增成功", 1);
			}else{
				return WSTReturn($this->getError(),-1);
			}
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('新增失败',-1);
        }
	}
	
	/**
	 * 编辑商品资料
	 */
	public function edit(){
		//dump(input('param.'));die;
	    $goodsId = input('post.goodsId/d');
		$data = input('post.');
		WSTUnset($data,'goodsId,dataFlag,statusRemarks,goodsStatus,createTime');
		$data['saleTime'] = date('Y-m-d H:i:s');
		$goodsCats = model('GoodsCats')->getParentIs($data['goodsCatId']);
		$data['goodsCatIdPath'] = implode('_',$goodsCats)."_";
		Db::startTrans();
        try{
        	//商品图片
			WSTUseImages(0, $goodsId, $data['goodsImg'],'goods','goodsImg');
			//商品相册
			WSTUseImages(0, $goodsId, $data['gallery'],'goods','gallery');
			// 商品描述图片
	        $desc = $this->where('goodsId',$goodsId)->value('goodsDesc');
			WSTEditorImageRocord(0, $goodsId, $desc, $data['goodsDesc']);


			$result = $this->validate(true)->allowField(true)->save($data,['goodsId'=>$goodsId]);
			if(false !== $result){
    	        //保存商品属性
    	        //删除之前的商品属性
    	        Db::name('goods_attributes')->where(['goodsId'=>$goodsId])->delete();
    	        //新增商品属性
		    	$attrsArray = [];
		    	$attrRs = Db::name('attributes')->where(['goodsCatId'=>['in',$goodsCats],'isShow'=>1,'dataFlag'=>1])
		    		            ->field('attrId')->select();
		    	foreach ($attrRs as $key =>$v){
		    		$attrs = [];
		    		$attrs['attrVal'] = input('attr_'.$v['attrId']);
		    		if($attrs['attrVal']=='')continue;
		    		$attrs['goodsId'] = $goodsId;
		    		$attrs['attrId'] = $v['attrId'];
		    		$attrs['createTime'] = date('Y-m-d H:i:s');
		    		$attrsArray[] = $attrs;
		    	}
		    	if(count($attrsArray)>0)Db::name('goods_attributes')->insertAll($attrsArray);
				Db::commit();
				return WSTReturn("编辑成功", 1);
			}else{
				return WSTReturn($this->getError(),-1);
			}
	    }catch (\Exception $e) {
        	Db::rollback();
            return WSTReturn('编辑失败',-1);
        }
	}
	/**
	 * 获取商品规格属性
	 */
	public function getSpecAttrs(){
		$goodsCatId = Input('post.goodsCatId/d');
		$goodsCatIds = model('GoodsCats')->getParentIs($goodsCatId);
		$data = [];
		$data['attrs'] = Db::name('attributes')->where(['dataFlag'=>1,'isShow'=>1,'goodsCatId'=>['in',$goodsCatIds]])->field('attrId,attrName,attrType,attrVal')->order('attrSort asc,attrId asc')->select();
	    return WSTReturn("", 1,$data);
	}
	
	/**
	 * 检测商品主表的货号或者商品编号
	 */
	public function checkExistGoodsKey($key,$val,$id = 0){
		if(!in_array($key,array('goodsSn','productNo')))return WSTReturn("非法的查询字段");
		$conditon = [$key=>$val];
		if($id>0)$conditon['goodsId'] = ['<>',$id];
		$rs = $dbo = $this->where($conditon)->count();
		return ($rs==0)?false:true;
	}
     /**
      *  上架商品列表
      */
	public function saleByPage(){
		$where = [];
		$where['dataFlag'] = 1;
		$where['isSale'] = 1;
		$goodsName = input('goodsName');
		$goodsCatIdPath = input('goodsCatIdPath');
		if($goodsCatIdPath !='')$where['goodsCatIdPath'] = ['like',$goodsCatIdPath."%"];
		if($goodsName != '')$where['goodsName|goodsSn'] = ['like',"%$goodsName%"];
		$rs = $this->where($where)
			->field('goodsId,goodsName,goodsSn,saleNum,shopPrice,goodsImg,isRecom,isHot,isBest,isNew,goodsStock')
			->order('saleTime', 'desc')
			->paginate(input('pagesize/d'))->toArray();
		foreach ($rs['Rows'] as $key => $v){
			$rs['Rows'][$key]['verfiycode'] = WSTShopEncrypt($v['goodsId']);
		}
		return $rs;
	}
	/**
	 * 仓库中的商品
	 */
    public function storeByPage(){
		$where['dataFlag'] = 1;
		$where['isSale'] = 0;
		$goodsName = input('goodsName');
		$goodsCatIdPath = input('goodsCatIdPath');
		if($goodsName != '')$where['goodsName|goodsSn'] = ['like',"%$goodsName%"];
		if($goodsCatIdPath !='')$where['goodsCatIdPath'] = ['like',$goodsCatIdPath."%"];	
		$rs = $this->where($where)
			->field('goodsId,goodsName,goodsImg,goodsSn,isSale,isBest,isHot,isNew,isRecom,goodsStock,saleNum,shopPrice')
			->order('saleTime', 'desc')
			->paginate(input('pagesize/d'))->toArray();
        foreach ($rs['Rows'] as $key => $v){
			$rs['Rows'][$key]['verfiycode'] =  WSTShopEncrypt($v['goodsId']);
		}
		return $rs;
	}
	
	/**
	 * 获取商品资料方便编辑
	 */
	public function getById($goodsId){
		$rs = $this->where(['goodsId'=>$goodsId])->find();
		if(!empty($rs)){
			if($rs['gallery']!='')$rs['gallery'] = explode(',',$rs['gallery']);
			//获取属性值
			$rs['attrs'] = Db::name('goods_attributes')->alias('ga')->join('attributes a','ga.attrId=a.attrId','inner')
			                 ->where('goodsId',$goodsId)->field('ga.attrId,a.attrType,ga.attrVal')->select();
		}
		return $rs;
	}
	
	/**
	 * 删除商品
	 */
	public function del(){
	    $id = input('post.id/d');
		$data = [];
		$data['dataFlag'] = -1;
		Db::startTrans();
		try{
		    $result = $this->update($data,['goodsId'=>$id]);
	        if(false !== $result){
	        	Db::name('carts')->where('goodsId',$id)->delete();
	        	WSTUnuseImage('goods','goodsImg',$id);
		        WSTUnuseImage('goods','gallery',$id);
		        Db::commit();
		        //标记删除购物车
	        	return WSTReturn("删除成功", 1);
	        }
		}catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('删除失败',-1);
	}
	/**
	  * 批量删除商品
	  */
	 public function batchDel(){
	 	$shopId = (int)session('WST_USER.shopId');
	   	$ids = input('post.ids/a');
	   	Db::startTrans();
		try{
		   	$rs = $this->where(['goodsId'=>['in',$ids]])->setField('dataFlag',-1);
			if(false !== $rs){
				Db::name('carts')->where(['goodsId'=>['in',$ids]])->delete();
				//标记删除购物车
			    foreach ($ids as $v){
					WSTUnuseImage('goods','goodsImg',(int)$v);
			        WSTUnuseImage('goods','gallery',(int)$v);
				}
				Db::commit();
	        	return WSTReturn("删除成功", 1);
	        }
		}catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('删除失败',-1);
	 }

	/**
	 * 查询商品
	 */
	public function searchQuery(){
		$goodsCatatId = (int)input('post.goodsCatId');
		if($goodsCatatId<=0)return [];
		$goodsCatIds = WSTGoodsCatPath($goodsCatatId);
		$key = input('post.key');
		$where = [];
		$where['dataFlag'] = 1;
		$where['isSale'] = 1;
		$where['goodsCatIdPath'] = ['like',implode('_',$goodsCatIds).'_%'];
		if($key!='')$where['goodsName|goodsSn'] = ['like','%'.$key.'%'];
		return $this->where($where)->field('goodsName,goodsSn,goodsId')->limit(50)->select();
	}

	/**
	 * 批量上架商品
	 */
	public function changeSale(){
		$ids = input('post.ids/a');
		$isSale = (int)input('post.isSale',1);
		//判断商品是否满足上架要求
		if($isSale==1){
	 		//直接设置上架 返回受影响条数
	 		$where = [];
	 		$where['g.goodsId'] = ['in',$ids];
	 		$where['gc.dataFlag'] = 1;
	 		$where['gc.isShow'] = 1;
	 		$where['g.goodsImg'] = ['<>',""];
			$rs = $this->alias('g')
				  ->join('__GOODS_CATS__ gc','g.goodsCatId=gc.CatId','inner')
				  ->where($where)->setField('isSale',1);
			if($rs!==false){
				$status = ($rs==count($ids))?1:2;
				if($status==1){
					return WSTReturn('商品上架成功', 1,['num'=>$rs]);
				}else{
					return WSTReturn('已成功上架商品'.$rs.'件，请核对未能上架的商品信息是否完整。', 2,['num'=>$rs]);
				}
			}else{
	 			return WSTReturn('上架失败，请核对商品信息是否完整!', -2);
	 		}

		}else{
			$rs = $this->where(['goodsId'=>['in',$ids]])->setField('isSale',$isSale);
			if($rs !== false){
				return WSTReturn('商品上架成功', 1);
			}else{
				return WSTReturn($this->getError(), -1);
			}
		}
	}
	/**
	* 修改商品状态
	*/
	public function changSaleStatus(){
		$is = input('post.is');
		$status = (input('post.status',1)==1)?0:1;
		$id = (int)input('post.id');
		$rs = $this->setField([$is=>$status,'goodsId'=>$id]);
		if($rs!==false){
			return WSTReturn('设置成功',1);
		}else{
			return WSTReturn($this->getError(),-1);
		}
	}
	/**
	 * 批量修改商品状态
	 */
	public function changeGoodsStatus(){
		//设置为什么 hot new best rec
		$allowArr = ['isHot','isNew','isBest','isRecom'];
		$is = input('post.is');
		if(!in_array($is,$allowArr))return WSTReturn('非法操作',-1);
		//设置哪一个状态
		$status = input('post.status',1);
		$ids = input('post.ids/a');
		$rs = $this->where(['goodsId'=>['in',$ids]])->setField($is, $status);
		if($rs!==false){
			return WSTReturn('设置成功',1);
		}else{
			return WSTReturn($this->getError(),-1);
		}
	}

	/**
	 * 修改商品库存/价格
	 */
	public function editGoodsBase(){
		$goodsId = (int)Input("goodsId");
		$post = input('post.');
		$data = [];
		if(isset($post['goodsStock'])){
			$data['goodsStock'] = (int)input('post.goodsStock',0);
		}elseif(isset($post['shopPrice'])){
			$data['shopPrice'] = (int)input('post.shopPrice',0);
		}else{
			return WSTReturn('操作失败',-1);
		}
		$rs = $this->update($data,['goodsId'=>$goodsId]);
		if($rs!==false){
			return WSTReturn('操作成功',1);
		}else{
			return WSTReturn('操作失败',-1);
		}
	}
	
	/**
	 * 获取库存预警的商品列表
	 */
	public function warnByPage(){
		$where = [];
		$goodsName = input('goodsName');
		$goodsCatIdPath = input('goodsCatIdPath');
		if($goodsCatIdPath !='')$where[] = " goodsCatIdPath like '".$goodsCatIdPath."%' ";
		if($goodsName != '')$where[] = " goodsName  like '%".$goodsName."%'";
		$where[] = " g.dataFlag = 1";
		$prefix = config('database.prefix');
		
		$sql2 = 'SELECT g.productNo,g.goodsId,g.goodsName,g.goodsImg,g.goodsStock,g.warnStock,g.isSale FROM '.$prefix.'goods g 
                    WHERE g.goodsStock<=g.warnStock 
                    and g.warnStock>0 and '.implode(' and ',$where);
		$page = (int)input('post.'.config('paginate.var_page'));
		$page = ($page<=0)?1:$page;
		$pageSize = input('post.pagesize/d');
		$start = ($page-1)*$pageSize;
		$sql = $sql2;
		$sqlNum = 'select count(*) wstNum from ('.$sql.") as c";
		$sql = 'select * from ('.$sql.') as c order by isSale desc limit '.$start.','.$pageSize;

		$rsNum = Db::query($sqlNum);
		$rsRows = Db::query($sql);
		$rs = WSTPager((int)$rsNum[0]['wstNum'],$rsRows,$page,$pageSize);
		return $rs;
	}
	/**
	 *  预警修改预警库存
	 */
	public function editwarnStock(){
		$id = input('post.id/d');
		$type = input('post.type/d');
		$number = (int)input('post.number');
		$data = $data2 = [];
		$datat=array('3'=>'goodsStock','4'=>'warnStock');
		if(!empty($type)){
			$data[$datat[$type]] = $number;
			if($type==3 || $type==4){
				$rs = $this->update($data,['goodsId'=>$id]);
			}
			if($rs!==false){
				return WSTReturn('操作成功',1);
			}else{
				return WSTReturn('操作失败',-1);
			}
		}
		return WSTReturn('操作失败',-1);
	}
}
