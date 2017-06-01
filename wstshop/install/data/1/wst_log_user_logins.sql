SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_log_user_logins`;
CREATE TABLE `wst_log_user_logins` (
  `loginId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `loginTime` datetime NOT NULL,
  `loginIp` varchar(16) NOT NULL,
  `loginSrc` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0:商城  1:webapp  2:App',
  `loginRemark` varchar(30) DEFAULT NULL COMMENT '登录备注信息',
  PRIMARY KEY (`loginId`),
  KEY `loginTime` (`loginTime`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

INSERT INTO `wst_log_user_logins` VALUES ('1', '1', '2016-12-23 18:37:35', '58.62.29.231', '0', null),
('2', '1', '2016-12-23 18:45:21', '58.62.29.231', '0', null),
('3', '34', '2016-12-26 10:00:13', '58.62.30.105', '0', null),
('4', '35', '2016-12-26 10:40:38', '58.62.30.105', '0', null),
('5', '35', '2016-12-26 11:29:40', '58.62.30.105', '0', null),
('6', '34', '2016-12-26 16:15:54', '58.62.30.105', '0', null),
('7', '34', '2016-12-26 17:34:08', '58.62.30.105', '0', null),
('8', '32', '2016-12-27 10:27:04', '58.62.29.190', '0', null),
('9', '32', '2016-12-27 10:48:32', '58.62.29.190', '0', null),
('10', '35', '2016-12-27 11:21:29', '58.62.29.190', '0', null),
('11', '34', '2016-12-27 11:38:28', '58.62.29.102', '0', null);
