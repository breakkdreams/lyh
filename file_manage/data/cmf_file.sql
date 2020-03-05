/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50714
Source Host           : localhost:3306
Source Database       : thinkcmf

Target Server Type    : MYSQL
Target Server Version : 50714
File Encoding         : 65001

Date: 2019-11-14 17:00:48
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for cmf_file
-- ----------------------------
DROP TABLE IF EXISTS `cmf_file`;
CREATE TABLE `cmf_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) DEFAULT NULL,
  `filetype` int(11) DEFAULT '0' COMMENT '1 图片类型 2 视频类型 3 音频类型 9 其他',
  `filepath` varchar(255) DEFAULT NULL,
  `fileurl` varchar(255) DEFAULT NULL,
  `filedes` varchar(255) DEFAULT NULL,
  `status` smallint(6) DEFAULT '1',
  `addtime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
