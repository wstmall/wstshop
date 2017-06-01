SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_freights`;
CREATE TABLE `wst_freights` (
  `freightId` int(11) NOT NULL AUTO_INCREMENT,
  `areaId2` int(11) NOT NULL,
  `freight` int(11) NOT NULL DEFAULT '0',
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`freightId`),
  KEY `shopId` (`areaId2`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;