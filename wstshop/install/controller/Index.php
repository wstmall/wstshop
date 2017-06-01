<?php
namespace wstshop\install\controller;
use wstshop\install\model\Index as M;
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
 * 首页控制器
 */
use think\Controller;
class Index extends Controller{
	
    public function index(){
    	$m = new M();
    	$step = input("step",0);
    	if($step<3){
    		if(file_exists(WSTRootPath().'/wstshop/install/install.ok'))$step = 3;
    	}
    	if(version_compare(PHP_VERSION, '5.5', '<')){
    		header("Content-type:text/html;charset=utf-8" );
    		die("系统需运行在PHP5.5以上版本（注意：PHP6不支持）!!!");
    	}
    	if($step == 1){
    		$env_items = [
    				'os' => [],
    				'php' => [],
    				'attachmentupload' => [],
    				'gdversion' => [],
    				'diskspace' => [],
    		];
    		$dir_items = [
    				'install' => ['path' => '/wstshop/install'],
    				'runtime' => ['path' => '/runtime'],
    				'upload' => ['path' => '/upload'],
    				'conf' => ['path' => '/wstshop/common/conf']
    		];
    		$func_items = [
    				'file_get_contents'=> [],
    				'curl_init'=> [],
    				'mb_strlen'=> [],
    				'finfo_open'=> []
    		];
    		$env_items = $m->envCheck($env_items);
    		$dir_items = $m->dirCheck($dir_items);
    		$func_items = $m->checkFunc($func_items);
    		$this->assign('env_items',$env_items);
    		$this->assign('dir_items',$dir_items);
    		$this->assign('func_items',$func_items);
    	}
    	$this->assign('step',$step);
    	return $this->fetch("default/index");
    }

    public function initConfig(){
        $m = new M();
        return $m->initConfig();
    }
    
    public function install(){
    	$m = new M();
    	return $m->install();
    }
}
