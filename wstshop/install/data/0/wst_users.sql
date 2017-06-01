SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_users`;
CREATE TABLE `wst_users` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `loginName` varchar(20) NOT NULL,
  `loginSecret` int(11) NOT NULL,
  `loginPwd` varchar(50) NOT NULL,
  `userSex` tinyint(4) DEFAULT '0',
  `userName` varchar(20) DEFAULT NULL,
  `trueName` varchar(100) DEFAULT NULL,
  `brithday` date DEFAULT NULL,
  `userPhoto` varchar(150) DEFAULT '',
  `userQQ` varchar(20) DEFAULT NULL,
  `userPhone` char(11) DEFAULT '',
  `userEmail` varchar(50) DEFAULT '',
  `userScore` int(11) DEFAULT '0',
  `userTotalScore` int(11) DEFAULT '0',
  `lastIP` varchar(16) DEFAULT NULL,
  `lastTime` datetime DEFAULT NULL,
  `userFrom` tinyint(4) DEFAULT '0',
  `userMoney` decimal(11,2) DEFAULT '0.00',
  `lockMoney` decimal(11,2) DEFAULT '0.00',
  `userStatus` tinyint(4) NOT NULL DEFAULT '1',
  `dataFlag` tinyint(4) NOT NULL DEFAULT '1',
  `createTime` datetime NOT NULL,
  `payPwd` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`userId`),
  KEY `userStatus` (`userStatus`,`dataFlag`),
  KEY `loginName` (`loginName`),
  KEY `userPhone` (`userPhone`),
  KEY `userEmail` (`userEmail`),
  KEY `userType` (`dataFlag`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;