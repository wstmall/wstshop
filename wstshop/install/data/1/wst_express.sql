SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_express`;
CREATE TABLE `wst_express` (
  `expressId` int(11) NOT NULL AUTO_INCREMENT,
  `expressName` varchar(50) NOT NULL,
  `dataFlag` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`expressId`),
  KEY `dataFlag` (`dataFlag`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

INSERT INTO `wst_express` VALUES ('1', '顺丰快递', '1'),
('2', '圆通快递', '1'),
('3', '申通快递', '1'),
('4', '韵达快递', '1'),
('5', 'EMS快递', '1'),
('6', '天天快递', '1');
