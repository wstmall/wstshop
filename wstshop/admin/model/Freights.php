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
 * 运费管理业务处理
 */
class Freights extends Base{
	/**
	 * 获取树形分类
	 */
	public function pageQuery(){
		$parentId = input('areaId/d',0);
		$data = Db::name('areas')->alias('a')->join('__FREIGHTS__ f','a.areaId= f.areaId2','left')
		->where(['a.isShow'=>1,'a.dataFlag'=>1,'a.parentId'=>$parentId])->where('areaType<=1')
		->field('a.areaId,a.areaName,a.areaType,f.freightId,f.freight')->order('a.areaKey desc')->paginate(input('post.pagesize/d',100))->toArray();
		return $data;
	}
	
	/**
	 * 编辑
	 */
	public function edit(){
		$info = input("post.list/a");
		$areaId = (int)input("post.areaId");
		$where = [];
		$where = ['isShow'=>1,'dataFlag'=>1,'areaType'=>1];
		if($areaId>0)$where['parentId'] = $areaId;
		$areas = Db::name('areas')->where($where)->field('areaId')->select();
		Db::startTrans();
		if(count($areas)==0)return WSTReturn('无效的城市');
		try{
		   $dataList = [];
		   if($areaId>0 || $areaId==-1)$info = $areas;
           foreach ($info as $key => $v) {
           	$areaIds = $key;
           	if($areaId>0 || $areaId==-1)$areaIds = $v['areaId'];
           	$areass = Db::name('areas')->where('isShow=1 AND dataFlag = 1 AND areaType=1 AND  areaId='.$areaIds)->field('areaId')->find();
           	if($areass){
           		$m = model('Freights')->where(['areaId2'=>$areaIds])->find();
           		if($areaId>0 || $areaId==-1){
           			$freight = input("post.vtext",0);
           		}else{
           			$freight = $v?$v:0;
           		}
           		if($m){
           			$m->freight = $freight;
           			$m->save();
           		}else{
           			$data = [];
           			$data['areaId2'] = $areaIds;
           			$data['freight'] = $freight;
           			$data['createTime'] = date('Y-m-d H:i:s');
           			$dataList[] = $data;
           		}
           	}
           }
           if(count($dataList)>0)model('Freights')->saveAll($dataList);
		   Db::commit();
		   return WSTReturn("操作成功", 1);
		}catch (\Exception $e) {
			Db::rollback();
			return WSTReturn('操作失败',-1);
		}
	}
}
