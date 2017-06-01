<?php 
namespace wstshop\install\model;
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
 * 系统业务处理
 */
use think\Model;
class Index extends Model{
	
	public function envCheck(&$env_items) {
		foreach($env_items as $key => $item) {
			$env_items[$key]['status'] = 1;
			if($key == 'os') {
				$env_items[$key]['current'] = PHP_OS;
			} elseif($key == 'php'){
				$env_items[$key]['current'] = PHP_VERSION;
			} elseif($key == 'attachmentupload') {
				if(@ini_get('file_uploads')){
					$env_items[$key]['current'] =  ini_get('upload_max_filesize');
				}else{
					$env_items[$key]['status'] = -1;
					$env_items[$key]['current'] = '没有开启文件上传';
				}
			} elseif($key == 'gdversion') {
				if(extension_loaded('gd')){
					$tmp = gd_info();
					$env_items[$key]['current'] = empty($tmp['GD Version']) ? '' : $tmp['GD Version'];
					unset($tmp);
				}else{
					$env_items[$key]['current'] = "没有开启GD扩展";
					$env_items[$key]['status'] = -1;
				}
			} elseif($key == 'diskspace') {
				if(function_exists('disk_free_space')) {
					$env_items[$key]['current'] = floor(disk_free_space(WSTRootPath()) / (1024*1024)).'M';
				} else {
					$env_items[$key]['current'] = '未知的磁盘空间';
					$env_items[$key]['status'] = 0;
				}
			}
		}
		return $env_items;
	}
	
	function checkFunc($func_items){
		foreach($func_items as $key => $item) {
			if(function_exists($key)){
				$func_items[$key]['current'] = '支持';
				$func_items[$key]['status'] = 1;
			}else{
				$func_items[$key]['current'] = '不支持';
				$func_items[$key]['status'] = -1;
			}
		}
		return $func_items;
	}
	
	public function dirCheck(&$dir_items) {
		foreach($dir_items as $key => $item) {
			$item_path = $item['path'];
			if(!$this->dirWriteable(WSTRootPath().$item_path)) {
				if(!is_dir(WSTRootPath().$item_path)) {
					$dir_items[$key]['status'] = 1;
				} else {
					$dir_items[$key]['status'] = -1;
				}
			} else {
				$dir_items[$key]['status'] = 1;
			}
		}
		return $dir_items;
	}
	
	public function dirWriteable($dir) {
		$writeable = 0;
		if(!is_dir($dir)) {
			@mkdir($dir, 0777);
		}
		if(is_dir($dir)) {
			if($fp = @fopen("$dir/test.txt", 'w')) {
				@fclose($fp);
					
			}
			if(file_exists("$dir/test.txt")){
				$writeable = 1;
				@unlink("$dir/test.txt");
			}
		}
		return $writeable;
	}
	
	public function install(){
		$data = input('post.');
		$admin_name = $data['admin_name'];
		$act = $data['act'];
		$isFinish = $data['isFinish'];
		$db_demo = $data['db_demo'];
		
		if($act=='list'){
			$db_name = $data['db_name'];
		    $db_host = $data['db_host'];
		    $db_port = $data['db_port'];
		    $db_user = $data['db_user'];
		    $db_pass = $data['db_pass'];
			$list = array();
			$dh = opendir(WSTRootPath().'/wstshop/install/data/'.$db_demo);
			while (($file=readdir($dh))!== false) {
				if($file!="." && $file!="..") {
					$list[] = $file;
				}
			}
			try{
				$pdo = new \PDO("mysql:host=$db_host;port=$db_host", $db_user, $db_pass);
			    $sql="CREATE DATABASE $db_name DEFAULT CHARACTER SET utf8;";
			    $pdo->exec($sql);
		    }catch (\Exception $e) {
                return WSTReturn('无法创建数据库，请检查数据库配置是否正确!',-1);
            }
			return array('status'=>1,'list'=>$list);
		}else if($act=='insert'){
			$table = $data['table'];
			$db_prefix = $data['db_prefix'];
			$sql = WSTRootPath()."/wstshop/install/data/".$db_demo."/wst_".$table.".sql";
			$sql = file_get_contents($sql);
			try{
				$this->excute($sql,$db_prefix);
				if($isFinish==1){
					$staffs = model('admin/staffs')->get(1);
					$staffs->loginPwd = md5($data['admin_password']."9365");
					$staffs->save();
					$counter_file = WSTRootPath().'/wstshop/install/install.ok';
					$fopen = fopen($counter_file,'wb');
					fputs($fopen,   @date('Y-m-d H:i:s'));
					fclose($fopen);
					if(file_exists(WSTRootPath().'/wstshop/install/install.ok')){
						return WSTReturn('',1);
					}else{
						return WSTReturn('无法创建配置文件，请检查install目录是否有写入权限!');
					}
				}
			}catch (\Exception $e) {
                Db::rollback();
                return WSTReturn('初始化表【'.$table.'】失败!');
            }
			return WSTReturn('',1);
		}
	}
	
	public function initConfig(){
		$data = input('post.');
		$db_host = $data['db_host'];
		$db_port = $data['db_port'];
		$db_user = $data['db_user'];
		$db_pass = $data['db_pass'];
		$db_name = $data['db_name'];
		$db_prefix = $data['db_prefix'];
		$code = "return [
			// 数据库类型
			'type'           => 'mysql',
			// 服务器地址
			'hostname'       => '".$db_host."',
			// 数据库名
			'database'       => '".$db_name."',
			// 用户名
			'username'       => '".$db_user."',
			// 密码
			'password'       => '".$db_pass."',
			// 端口
			'hostport'       => '".$db_port."',
			// 连接dsn
			'dsn'            => '',
			// 数据库连接参数
			'params'         => [],
			// 数据库编码默认采用utf8
			'charset'        => 'utf8',
			// 数据库表前缀
			'prefix'         => '".$db_prefix."',
			// 数据库调试模式
			'debug'          => false,
			// 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
			'deploy'         => 0,
			// 数据库读写是否分离 主从式有效
			'rw_separate'    => false,
			// 读写分离后 主服务器数量
			'master_num'     => 1,
			// 指定从服务器序号
			'slave_no'       => '',
			// 是否严格检查字段是否存在
			'fields_strict'  => true,
			// 数据集返回类型 array 数组 collection Collection对象
			'resultset_type' => 'array',
			// 是否自动写入时间戳字段
			'auto_timestamp' => false,
			// 是否需要进行SQL性能分析
			'sql_explain'    => false,
	]";
		$code = "<?php\n ".$code.";\n?>";
		file_put_contents(WSTRootPath()."/wstshop/common/conf/database.php", $code);
		clearstatcache();
		if(!file_exists(WSTRootPath()."/wstshop/common/conf/database.php")){
				return WSTReturn('无法创建配置文件，请检查wstshop/common/conf目录是否有写入权限!');
		}
		return WSTReturn('',1);
	}
	
	public function excute($sql,$db_prefix=''){
		if(!isset($sql) || empty($sql)) return;
		$sql = str_replace("\r", "\n", str_replace(' `'.$db_prefix, ' `'.$db_prefix, $sql));
		$ret = array();
		$num = 0;
		foreach(explode(";\n", trim($sql)) as $query) {
			$ret[$num] = '';
			$queries = explode("\n", trim($query));
			foreach($queries as $query) {
				$ret[$num] .= (isset($query[0]) && $query[0] == '#') || (isset($query[1]) && isset($query[1]) && $query[0].$query[1] == '--') ? '' : $query;
			}
			$num++;
		}
		unset($sql);
		foreach($ret as $query){
			$query = trim($query);
			if($query) {
				if(strtoupper(substr($query, 0, 12)) == 'CREATE TABLE'){
					$type = strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $query));
					$query = preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU", "\\1", $query)." ENGINE=InnoDB DEFAULT CHARSET=utf8";
				}
				Db::execute($query);
			}
		}
	}
}