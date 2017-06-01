<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 检测PHP环境
if(version_compare(PHP_VERSION,'5.5.0','<'))  die('require PHP > 5.5.0 !');
// [ 应用入口文件 ]
// 定义应用目录
define('APP_PATH', __DIR__ . '/wstshop/');
define('CONF_PATH', __DIR__.'/wstshop/common/conf/');
define('WST_COMM', __DIR__.'/wstshop/common/common/');
define('WST_HOME_COMM', __DIR__.'/wstshop/home/common/');
define('WST_ADMIN_COMM', __DIR__.'/wstshop/admin/common/');
// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';
