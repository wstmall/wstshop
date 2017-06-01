SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_messages`;
CREATE TABLE `wst_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msgType` tinyint(4) NOT NULL DEFAULT '0',
  `sendUserId` int(11) NOT NULL DEFAULT '0',
  `receiveUserId` int(11) NOT NULL DEFAULT '0',
  `msgContent` text NOT NULL,
  `msgStatus` tinyint(4) NOT NULL DEFAULT '0',
  `msgJson` varchar(255) DEFAULT NULL,
  `dataFlag` tinyint(4) NOT NULL DEFAULT '1',
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `receiveUserId` (`receiveUserId`,`dataFlag`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

INSERT INTO `wst_messages` VALUES ('1', '1', '1', '35', '您的订单【100000003】已发货啦，快递号为：123456782，请做好收货准备哦~', '1', '{\"from\":1,\"dataId\":1}', '1', '2016-12-26 10:52:33'),
('2', '1', '1', '34', '您的订单【100000040】已发货啦，快递为：圆通快递，单号为：201602352345533，请做好收货准备哦~', '0', '{\"from\":1,\"dataId\":5}', '1', '2016-12-26 16:32:48'),
('3', '1', '1', '34', '您的订单【100000051】已发货啦，请做好收货准备哦~', '0', '{\"from\":1,\"dataId\":6}', '1', '2016-12-26 16:39:45'),
('4', '1', '1', '34', '您的订单【100000084】已发货啦，快递为：EMS快递，单号为：201601223432334，请做好收货准备哦~', '0', '{\"from\":1,\"dataId\":9}', '1', '2016-12-26 16:42:00'),
('5', '1', '1', '34', '您的订单【100000073】已发货啦，快递为：EMS快递，单号为：20160203423343，请做好收货准备哦~', '0', '{\"from\":1,\"dataId\":8}', '1', '2016-12-26 16:42:09'),
('6', '1', '1', '34', '您的订单【100000062】已发货啦，快递为：天天快递，单号为：2016023423434，请做好收货准备哦~', '1', '{\"from\":1,\"dataId\":7}', '1', '2016-12-26 16:42:19'),
('7', '1', '1', '32', '您的订单【100000095】已发货啦，快递为：顺丰快递，请做好收货准备哦~', '1', '{\"from\":1,\"dataId\":10}', '1', '2016-12-27 10:31:11'),
('8', '1', '1', '32', '您的订单【100000106】已发货啦，请做好收货准备哦~', '0', '{\"from\":1,\"dataId\":11}', '1', '2016-12-27 10:54:15'),
('9', '1', '1', '34', '您的订单【100000110】已发货啦，快递为：申通快递，单号为：20160323124443334，请做好收货准备哦~', '0', '{\"from\":1,\"dataId\":12}', '1', '2016-12-27 11:39:49'),
('10', '1', '1', '34', '您的订单【100000121】已发货啦，请做好收货准备哦~', '0', '{\"from\":1,\"dataId\":13}', '1', '2016-12-27 11:47:11');
