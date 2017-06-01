SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_favorites`;
CREATE TABLE `wst_favorites` (
  `favoriteId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL DEFAULT '0',
  `goodsId` int(11) NOT NULL,
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`favoriteId`),
  UNIQUE KEY `favoriteId` (`userId`,`goodsId`) USING BTREE,
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
