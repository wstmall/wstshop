SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_user_address`;
CREATE TABLE `wst_user_address` (
  `addressId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `userName` varchar(50) NOT NULL,
  `userPhone` varchar(20) DEFAULT NULL,
  `areaIdPath` varchar(255) NOT NULL DEFAULT '0',
  `areaId` int(11) NOT NULL DEFAULT '0',
  `userAddress` varchar(255) NOT NULL,
  `isDefault` tinyint(4) NOT NULL DEFAULT '0',
  `dataFlag` tinyint(4) NOT NULL DEFAULT '1',
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`addressId`),
  KEY `userId` (`userId`,`dataFlag`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT INTO `wst_user_address` VALUES ('1', '35', 'asfas', '1234453231', '440000_440100_440118_', '440118', 'fasfdae', '0', '1', '2016-12-26 10:45:53'),
('2', '35', 'asdfas', '24312323', '360000_360100_360102_', '360102', 'fasdfa', '0', '1', '2016-12-26 10:46:35'),
('3', '34', '张无忌', '13873313009', '440000_440100_440118_', '440118', '朱村碧桂园城市花园', '0', '1', '2016-12-26 16:18:47'),
('4', '32', '曹火昆', '13151516516', '440000_440100_440111_', '440111', '圣诞节来发掘了', '1', '1', '2016-12-27 10:30:46');
