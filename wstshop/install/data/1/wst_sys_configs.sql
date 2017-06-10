SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_sys_configs`;
CREATE TABLE `wst_sys_configs` (
  `configId` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `fieldName` varchar(50) DEFAULT NULL COMMENT '字段名称',
  `fieldCode` varchar(20) DEFAULT NULL COMMENT '字段代码',
  `fieldValue` text,
  `fieldType` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`configId`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8;


INSERT INTO `wst_sys_configs` VALUES ('1', '店铺名称', 'shopName', 'WSTShop网上商店', '0'),
('2', '店铺标题', 'seoShopTitle', 'WSTShop网上商店', '0'),
('3', '店铺描述', 'seoShopDesc', 'WSTShop是一款基于php语言开发的B2C开源网店系统,系统功能强大，扩展性好，产品涵盖手机、微信、安卓、苹果等访问端的接入，适合企业及个人快速构建个性化网上商店！', '0'),
('4', '店铺关键字', 'seoShopKeywords', 'B2C开源系统,thinkphp开源网上商店,单商户系统,WSTShop', '0'),
('5', '联系邮箱', 'serviceEmail', 'wstshop@qq.com', '0'),
('6', '当前系统版本号', 'wstVersion', '1.3.0_170609', '0'),
('7', '系统版本MD5', 'wstMd5', '6dd1ea8ed02f113fb572025715c1b2b1', '0'),
('8', '移动端图片后缀', 'wstMobileImgSuffix', null, '0'),
('14', '访问统计', 'visitStatistics', '&lt;script language=&quot;javascript&quot; type=&quot;text/javascript&quot; src=&quot;http://js.users.51.la/17819468.js&quot;&gt;&lt;/script&gt;', '0'),
('15', 'SMTP服务器', 'mailSmtp', 'smtp.163.com', '0'),
('16', 'SMTP端口', 'mailPort', '25', '0'),
('17', '是否验证SMTP', 'mailAuth', '1', '0'),
('18', 'SMTP发件人邮箱', 'mailAddress', '', '0'),
('19', 'SMTP登录账号', 'mailUserName', '', '0'),
('20', 'SMTP登录密码', 'mailPassword', '', '0'),
('21', '发件人名称', 'mailSendTitle', '', '0'),
('22', '短信账号', 'smsKey', '', '0'),
('23', '短信密码', 'smsPass', '', '0'),
('24', '号码每日发送数', 'smsLimit', '20', '0'),
('26', '授权码', 'shopLicense', null, '0'),
('27', '商城Logo', 'shopLogo', 'upload/sysconfigs/2016-12/585d05bceb629.png', '0'),
('28', '商品默认图片', 'goodsLogo', 'upload/sysconfigs/2016-12/585d06c60bbfb.png', '0'),
('29', '底部设置', 'shopFooter', 'COPYRIGHT 2015-2016 广州商淘信息科技有限公司 版权所有', '0'),
('30', '联系电话', 'serviceTel', '020-85289921', '0'),
('31', 'QQ', 'serviceQQ', '153289970', '0'),
('33', '热搜关键词', 'hotWordsSearch', 'WSTShop,网上商店,华为荣耀手机,酒仙网剑南春', '0'),
('34', '开启短信发送验证码', 'smsVerfy', '0', '0'),
('35', '开启手机验证', 'smsOpen', '0', '0'),
('37', '商城禁用关键字', 'registerLimitWords', 'admin,system,fuck', '0'),
('38', '结算金额设置', 'settlementStartMoney', '', '0'),
('39', '开启积分支付', 'isOpenScorePay', '1', '0'),
('40', '开启下单获取积分', 'isOrderScore', '1', '0'),
('41', '开启评价获取积分', 'isAppraisesScore', '1', '0'),
('42', '积分与金钱兑换比例', 'scoreCashRatio', '', '0'),
('43', '自动收货期限', 'autoReceiveDays', '10', '0'),
('44', '自动评价期限', 'autoAppraiseDays', '10', '0'),
('53', '会员默认头像', 'userLogo', 'upload/sysconfigs/2016-12/585cf2d46c3d8.png', '0'),
('54', '默认省份', 'defaultProvince', null, '0'),
('55', '水印文字', 'watermarkWord', '', '0'),
('56', '水印文字大小', 'watermarkSize', '', '0'),
('57', '水印文字颜色', 'watermarkColor', '', '0'),
('58', '水印文件', 'watermarkFile', '', '0'),
('59', '水印位置', 'watermarkPosition', '0', '0'),
('60', '水印透明度', 'watermarkOpacity', '50', '0'),
('61', '水印字体', 'watermarkTtf', '', '0'),
('62', '商城口号', 'shopSlogan', '易用的B2C网上商店系统！', '0'),
('63', '热搜广告词', 'adsWordsSearch', 'WSTShop网上商店', '0'),
('65', '未付款订单有效期', 'autoCancelNoPayDays', '24', '0'),
('66', '默认运费', 'defaultFreight', '0', '0');
