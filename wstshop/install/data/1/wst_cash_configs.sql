SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_cash_configs`;
CREATE TABLE `wst_cash_configs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `targetType` tinyint(4) NOT NULL DEFAULT '0',
  `targetId` int(11) NOT NULL,
  `accType` tinyint(4) NOT NULL DEFAULT '0',
  `accTargetId` int(11) NOT NULL DEFAULT '0',
  `accAreaId` int(11) DEFAULT NULL,
  `accNo` varchar(100) NOT NULL,
  `accUser` varchar(100) NOT NULL,
  `dataFlag` tinyint(4) NOT NULL DEFAULT '1',
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `targetType` (`targetType`,`targetId`,`dataFlag`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


INSERT INTO `wst_cash_configs` VALUES ('1', '0', '35', '3', '1', '440118', '2234324322312', '234', '1', '2016-12-27 11:31:01');
