SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_log_moneys`;
CREATE TABLE `wst_log_moneys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `targetType` tinyint(4) NOT NULL DEFAULT '0',
  `targetId` int(11) NOT NULL DEFAULT '0',
  `dataId` int(11) NOT NULL DEFAULT '0',
  `dataSrc` int(11) NOT NULL DEFAULT '0',
  `remark` text NOT NULL,
  `moneyType` tinyint(4) NOT NULL DEFAULT '1',
  `money` decimal(11,2) NOT NULL DEFAULT '0.00',
  `tradeNo` varchar(100) DEFAULT NULL,
  `payType` tinyint(4) NOT NULL DEFAULT '0',
  `dataFlag` tinyint(4) NOT NULL DEFAULT '1',
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;