/*
MySQL Database Backup Tools
Server:127.0.0.1:3306
Database:cs
Data:2019-11-13 15:30:18
*/
SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for cmf_demo
-- ----------------------------
DROP TABLE IF EXISTS `cmf_demo`;
CREATE TABLE `cmf_demo` (
  `id` smallint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` char(40) NOT NULL COMMENT '名称',
  `create_time` char(11) NOT NULL COMMENT '添加时间',
  `islock` tinyint(1) unsigned NOT NULL COMMENT '状态',
  `nickname` varchar(100) NOT NULL COMMENT '昵称',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='例子表';
-- ----------------------------
-- Records of cmf_demo
-- ----------------------------
