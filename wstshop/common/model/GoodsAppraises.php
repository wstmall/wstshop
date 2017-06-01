<?php
namespace wstshop\common\model;
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
 * 评价类
 */
use think\Db;
class GoodsAppraises extends Base{
	/**
	* 用户评价
	*/
	public function userAppraise(){
		$userId = (int)session('WST_USER.userId');

		$where = [];
		$where['g.dataFlag'] = 1;
		$where['g.isSale'] = 1;


		$where['ga.userId'] = $userId;


		$model = model('goods');
		$data = $model->alias('g')
					  ->field('g.goodsId,g.goodsImg,g.goodsName,ga.goodsScore,ga.serviceScore,ga.timeScore,ga.content,ga.images,ga.shopReply,ga.replyTime,u.userName,o.orderNo')
					  ->join('__GOODS_APPRAISES__ ga','g.goodsId=ga.goodsId','inner')
					  ->join('__ORDERS__ o','o.orderId=ga.orderId','inner')
					  ->join('__USERS__ u','u.userId=ga.userId','inner')
					  ->where($where)
					  ->paginate()->toArray();
		if($data !== false){
			return WSTReturn('',1,$data);
		}else{
			return WSTReturn($this->getError(),-1);
		}
	}
 	/**
	* 添加评价
	*/
	public function add(){
		//检测订单是否有效
		$orderId = (int)input('orderId');
		$goodsId = (int)input('goodsId');
		$goodsSpecId = (int)input('goodsSpecId');
		$userId = (int)session('WST_USER.userId');
		$goodsScore = (int)input('goodsScore');
		$timeScore = (int)input('timeScore');
		$serviceScore = (int)input('serviceScore');
		$orders = model('orders')->where(['orderId'=>$orderId,'dataFlag'=>1])->field('orderStatus,orderNo,isAppraise,orderScore')->find();
		if(empty($orders))return WSTReturn("无效的订单");
		if($orders['orderStatus']!=2)return WSTReturn("订单状态已改变，请刷新订单后再尝试!");
		//检测商品是否已评价
		$apCount = $this->where(['orderId'=>$orderId,'goodsId'=>$goodsId,'goodsSpecId'=>$goodsSpecId])->count();
		if($apCount>0)return WSTReturn("该商品已评价!");
		Db::startTrans();
		try{	
			//增加订单评价
			$data = [];
			$data['userId'] = $userId;
			$data['goodsSpecId'] = $goodsSpecId;
			$data['goodsId'] = $goodsId;
			$data['orderId'] = $orderId;
			$data['goodsScore'] = $goodsScore;
			$data['serviceScore'] = $serviceScore;
			$data['timeScore']= $timeScore;
			$data['content'] = input('content');
			$data['images'] = input('images');
			$data['createTime'] = date('Y-m-d H:i:s');
			$rs = $this->validate('GoodsAppraises.add')->allowField(true)->save($data);
			if($rs !==false){
				WSTUseImages(0, $this->id, $data['images']);
				//增加商品评分
				$prefix = config('database.prefix');
				$updateSql = "update ".$prefix."goods_scores set 
				             totalScore=totalScore+".(int)($goodsScore+$serviceScore+$timeScore).",
				             goodsScore=goodsScore+".(int)$goodsScore.",
				             serviceScore=serviceScore+".(int)$serviceScore.",
				             timeScore=timeScore+".(int)$timeScore.",
				             totalUsers=totalUsers+1,goodsUsers=goodsUsers+1,serviceUsers=serviceUsers+1,timeUsers=timeUsers+1
				             where goodsId=".$goodsId;
				Db::execute($updateSql);
				//增加商品评价数
				Db::name('goods')->where('goodsId',$goodsId)->setInc('appraiseNum');
				// 查询该订单是否已经完成评价,修改orders表中的isAppraise
				$ogRs = Db::name('order_goods')->alias('og')
				   ->join('__GOODS_APPRAISES__ ga','og.orderId=ga.orderId and og.goodsId=ga.goodsId and og.goodsSpecId=ga.goodsSpecId','left')
				   ->where('og.orderId',$orderId)->field('og.id,ga.id gid')->select();
				$isFinish = true;
				foreach ($ogRs as $key => $v){
					if($v['id']>0 && $v['gid']==''){
						$isFinish = false;
						break;
					}
				}
				//订单商品全部评价完则修改订单状态
				if($isFinish){
					if(WSTConf("CONF.isAppraisesScore")==1){
						//给用户增加积分
						$score = [];
						$score['userId'] = $userId;
						$score['score'] = 5;
						$score['dataSrc'] = 1;
						$score['dataId'] = $orderId;
						$score['dataRemarks'] = "评价订单【".$orders['orderNo']."】获得积分5个";
						$score['scoreType'] = 1;
						$score['createTime'] = date('Y-m-d H:i:s');
						model('UserScores')->save($score);
						// 增加用户积分
						model('Users')->where("userId=$userId")->setInc('userScore',5);
						// 用户总积分
						model('Users')->where("userId=$userId")->setInc('userTotalScore',5);
					}
					//修改订单评价状态
					model('orders')->where('orderId',$orderId)->update(['isAppraise'=>1,'isClosed'=>1]);
				}
				Db::commit();
				return WSTReturn('评价成功',1);
			}else{
				return WSTReturn($this->getError(),-1);
			}
		}catch (\Exception $e) {
		    Db::rollback();
	        return WSTReturn('评价失败',-1);
	    }

	}
	/**
	* 根据商品id取评论
	*/
	public function getById(){
		// 处理匿名
		$anonymous = (int)input('anonymous');

		$goodsId = (int)input('goodsId');
		$rs  = 	$this->alias('ga')
					 ->field('ga.content,ga.images,ga.shopReply,ga.replyTime,ga.createTime,ga.goodsScore,ga.serviceScore,ga.timeScore,ga.orderId,u.loginName,u.userTotalScore,goodsSpecNames')
					 ->join('__USERS__ u','ga.userId=u.userId','left')
					 ->join('__ORDER_GOODS__ og','og.orderId=ga.orderId and og.goodsId=ga.goodsId','inner')
					 ->where(['ga.goodsId'=>$goodsId,
							  'ga.dataFlag'=>1,
							  'ga.isShow'=>1])->paginate()->toArray();
		foreach($rs['Rows'] as $k=>$v){
			$rs['Rows'][$k]['goodsSpecNames'] = str_replace('@@_@@','，',$v['goodsSpecNames']);
			if($anonymous){
				$start = floor((strlen($v['loginName'])/2))-1;
				$rs['Rows'][$k]['loginName'] = substr_replace($v['loginName'],'**',$start,2);
			}
			//获取用户等级
			$rrs = Db::name('user_ranks')->where('startScore','<=',$v['userTotalScore'])->where('endScore','>=',$v['userTotalScore'])->field('rankId,rankName,rebate,userrankImg')->find();
			$rs['Rows'][$k]['userTotalScore'] = $rrs['userrankImg'];
		}
		if($rs!==false){
			return WSTReturn('',1,$rs);
		}else{
			return WSTReturn($this->getError(),-1);
		}
	}
	/*获取最新的5条高评分评价,用于首页显示*/
	public function getNewAppr($num){
		$model = model('goods');
		$where = [];
		$where['serviceScore'] = ['>=',4];
		$where['g.dataFlag'] = 1;
		$where['ga.dataFlag'] = 1;
		$where['goodsScore'] = ['>=',4];
		$where['timeScore'] = ['>=',4];
		$data = $model->alias('g')
					  ->field('g.goodsId,g.goodsImg,g.goodsName,ga.content')
					  ->join('__GOODS_APPRAISES__ ga','g.goodsId=ga.goodsId','inner')
					  ->join('__USERS__ u','u.userId=ga.userId','inner')
					  ->where($where)
					  ->order('ga.createTime desc')
					  ->limit($num)
					  ->select();
		return $data;
	}

}
