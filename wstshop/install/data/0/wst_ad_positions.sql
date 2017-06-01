SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `wst_ad_positions`;
CREATE TABLE `wst_ad_positions` (
  `positionId` int(11) NOT NULL AUTO_INCREMENT,
  `positionType` tinyint(4) NOT NULL DEFAULT '0',
  `positionName` varchar(100) NOT NULL,
  `positionWidth` int(11) NOT NULL DEFAULT '0',
  `positionHeight` int(11) NOT NULL DEFAULT '0',
  `dataFlag` tinyint(4) NOT NULL DEFAULT '1',
  `positionCode` varchar(20) DEFAULT NULL,
  `apSort` int(11) NOT NULL,
  PRIMARY KEY (`positionId`),
  KEY `dataFlag` (`positionType`) USING BTREE,
  KEY `positionCode` (`positionCode`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;

INSERT INTO `wst_ad_positions` VALUES ('4', '1', '首页分层1F顶部广告', '1200', '120', '1', 'ads-1-1', '0'),
('5', '1', '首页分层1F左侧广告', '240', '338', '1', 'ads-1-2', '0'),
('7', '1', '首页分层2F顶部广告', '1200', '120', '1', 'ads-2-1', '0'),
('8', '1', '首页分层2F左侧广告', '240', '338', '1', 'ads-2-2', '0'),
('10', '1', '首页分层3F顶部广告', '1200', '120', '1', 'ads-3-1', '0'),
('11', '1', '首页分层3F左侧广告', '240', '338', '1', 'ads-3-2', '0'),
('13', '1', '首页分层4F顶部广告', '1200', '120', '1', 'ads-4-1', '0'),
('14', '1', '首页分层4F左侧广告', '240', '338', '1', 'ads-4-2', '0'),
('16', '1', '首页分层5F顶部广告', '1200', '120', '1', 'ads-5-1', '0'),
('17', '1', '首页分层5F左侧广告', '240', '338', '1', 'ads-5-2', '0'),
('19', '1', '首页分层6F顶部广告', '1200', '120', '1', 'ads-6-1', '0'),
('20', '1', '首页分层6F左侧广告', '240', '338', '1', 'ads-6-2', '0'),
('22', '1', '首页分层7F顶部广告', '1200', '120', '1', 'ads-7-1', '0'),
('23', '1', '首页分层7F左侧广告', '240', '338', '1', 'ads-7-2', '0'),
('25', '1', '首页分层8F顶部广告', '1200', '120', '1', 'ads-8-1', '0'),
('26', '1', '首页分层8F左侧广告', '240', '338', '1', 'ads-8-2', '0'),
('28', '1', '首页分层9F顶部广告', '1200', '120', '1', 'ads-9-1', '0'),
('29', '1', '首页分层9F左侧广告', '240', '338', '1', 'ads-9-2', '0'),
('31', '1', '首页分层10F顶部广告', '1200', '120', '1', 'ads-10-1', '0'),
('32', '1', '首页分层10F左侧广告', '240', '338', '1', 'ads-10-2', '0'),
('34', '1', '首页轮播广告', '985', '337', '1', 'ads-index', '99'),
('35', '1', '首页顶部广告', '1200', '100', '-1', 'index-top-ads', '100'),
('36', '1', '首页资讯下方广告', '210', '68', '-1', 'index-art-bottom', '1'),
('38', '1', '首页分类下方4个广告', '290', '202', '1', 'index-four-ads', '0'),
('39', '1', '首页热销右侧广告', '222', '352', '1', 'index-hot', '0');
