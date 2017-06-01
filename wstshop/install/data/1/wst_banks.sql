SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_banks`;
CREATE TABLE `wst_banks` (
  `bankId` int(11) NOT NULL AUTO_INCREMENT,
  `bankName` varchar(50) NOT NULL,
  `dataFlag` tinyint(4) NOT NULL DEFAULT '1',
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`bankId`),
  KEY `bankFlag` (`dataFlag`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;


INSERT INTO `wst_banks` VALUES ('1', '工商银行', '1', '2016-12-27 11:25:41'),
('2', '农业银行', '1', '2016-12-27 11:25:55'),
('3', '中国银行', '1', '2016-12-27 11:26:05'),
('4', '建设银行', '1', '2016-12-27 11:26:15'),
('5', '招商银行', '1', '2016-12-27 11:26:22'),
('6', '交通银行', '1', '2016-12-27 11:26:29'),
('7', '兴业银行', '1', '2016-12-27 11:27:23'),
('8', '农商银行', '1', '2016-12-27 11:27:30');
