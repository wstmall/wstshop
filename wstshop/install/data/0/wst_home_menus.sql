SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_home_menus`;
CREATE TABLE `wst_home_menus` (
  `menuId` int(11) NOT NULL AUTO_INCREMENT,
  `parentId` int(11) NOT NULL DEFAULT '0',
  `menuName` varchar(100) NOT NULL,
  `menuUrl` varchar(100) NOT NULL,
  `menuOtherUrl` text,
  `isShow` tinyint(4) DEFAULT '1',
  `menuSort` int(11) NOT NULL DEFAULT '0',
  `dataFlag` tinyint(4) NOT NULL DEFAULT '1',
  `createTime` datetime DEFAULT NULL,
  PRIMARY KEY (`menuId`),
  KEY `parentId` (`parentId`,`isShow`,`dataFlag`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8;

INSERT INTO `wst_home_menus` VALUES ('1', '0', '买家中心', 'home/users/index', null, '1', '0', '1', '2016-08-14 18:37:18'),
('2', '1', '我的订单', '#', null, '1', '0', '1', '2016-08-14 18:37:18'),
('3', '2', '待付款订单', 'home/orders/waitPay', 'home/orders/waitPayByPage,home/orders/cancellation,home/orders/detail', '1', '0', '1', '2016-08-14 18:37:18'),
('5', '2', '待收货订单', 'home/orders/waitReceive', 'home/orders/waitReceiveByPage,home/orders/cancellation,home/orders/detail,home/orders/receive,home/orders/toReject,home/ordercomplains/complain', '1', '1', '1', '2016-08-14 18:37:18'),
('6', '2', '待评价订单', 'home/orders/waitAppraise', 'home/orders/waitAppraiseByPage,home/orders/detail,home/orders/orderAppraise,home/ordercomplains/complain', '1', '2', '1', '2016-08-14 18:37:18'),
('7', '2', '已取消订单', 'home/orders/cancel', 'home/orders/cancelByPage,home/orders/detail', '1', '4', '1', '2016-08-14 18:37:18'),
('8', '2', '拒收/退款', 'home/orders/abnormal', 'home/orders/abnormalByPage,home/ordercomplains/complain', '1', '5', '1', '2016-08-14 18:37:18'),
('9', '2', '我的评价', 'home/goodsappraises/myAppraise', 'home/goodsappraises/userAppraise', '1', '6', '1', '2016-08-14 18:37:18'),
('10', '1', '收藏管理', '#', null, '1', '0', '1', '2016-08-14 18:37:18'),
('11', '43', '资金管理', '#', null, '1', '0', '1', '2016-08-14 18:37:18'),
('13', '11', '积分管理', 'home/userscores/index', 'home/userscores/pageQuery', '1', '0', '1', '2016-08-14 18:37:18'),
('14', '1', '帐户设置', '#', null, '1', '0', '1', '2016-08-14 18:37:18'),
('15', '14', '用户资料', 'home/users/edit', 'home/users/toEdit,home/users/editUserPhoto', '1', '0', '1', '2016-08-14 18:37:18'),
('16', '14', '安全设置', 'home/users/security', 'home/users/editPass,home/users/editEmail,home/users/editPhone', '1', '0', '1', '2016-08-14 18:37:18'),
('17', '14', '用户地址', 'home/useraddress/index', 'home/useraddress/listQuery,home/useraddress/edit,home/useraddress/setDefault,home/useraddress/del', '1', '0', '1', '2016-08-14 18:37:18'),
('19', '1', '客户管理', '#', null, '1', '0', '1', '2016-08-14 18:37:18'),
('20', '19', '投诉管理', 'home/ordercomplains/index', 'home/ordercomplains/queryUserComplainByPage,home/ordercomplains/getUserComplainDetail', '1', '0', '-1', '2016-08-14 18:37:18'),
('41', '10', '我关注的商品', 'home/favorites/goods', 'home/favorites/listGoodsQuery,home/favorites/cancel', '1', '0', '1', '2016-08-14 18:37:18'),
('43', '0', '资金管理', 'home/users/index', '', '1', '0', '1', '2016-09-18 10:24:47'),
('46', '10', '我关注的商家', 'home/favorites/shops', 'home/favorites/listShopQuery,home/favorites/cancel', '1', '2', '-1', '2016-09-24 00:09:34'),
('48', '2', '已完成订单', 'home/orders/finish', 'home/orders/finishByPage,home/orders/detail,home/orders/orderAppraise,home/ordercomplains/complain', '1', '3', '1', '2016-09-22 10:18:16'),
('49', '19', '用户信息', 'home/messages/index', 'home/messages/queryByList,home/messages/showMsg,home/messages/batchRead,home/messages/del,home/messages/batchDel', '1', '3', '1', '2016-09-22 10:54:49'),
('60', '11', '资金流水', 'home/logmoneys/usermoneys', 'home/logmoneys/pageUserQuery', '1', '1', '1', '2016-11-09 23:53:50'),
('62', '11', '提现管理', 'home/cashdraws/index', 'home/cashdraws/pageQuery,home/cashdraws/toEdit,home/cashdraws/drawMoney,home/cashconfigs/pageQuery,home/cashconfigs/toEdit,home/cashconfigs/add,home/cashconfigs/edit,home/cashconfigs/del', '1', '5', '1', '2016-11-13 15:38:46'),
('63', '2', '待发货订单', 'home/orders/waitSend', 'home/orders/waitSendByPage', '1', '1', '1', '2016-12-16 14:37:58');
