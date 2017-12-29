
CREATE TABLE `acl` (
  `acl_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(20) NOT NULL DEFAULT '' COMMENT '模块名',
  `controller_name` varchar(20) NOT NULL COMMENT '控制器名',
  `action_name` varchar(20) NOT NULL COMMENT '方法名',
  `acl_module` varchar(20) NOT NULL DEFAULT 'admin' COMMENT '模块英文名',
  `acl_controller` varchar(20) NOT NULL COMMENT '控制器英文名',
  `acl_action` varchar(20) NOT NULL COMMENT '方法英文名',
  `is_menu` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否是菜单项',
  `menu_sort` int(11) DEFAULT NULL COMMENT '菜单的排序，越小越在前面',
  PRIMARY KEY (`acl_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

INSERT INTO `acl` VALUES ('4', '管理', '文章管理', '*', 'admin', 'article', '*', '1', '0');
INSERT INTO `acl` VALUES ('5', '管理', 'HTML生成', '*', 'admin', 'html', '*', '1', '0');
INSERT INTO `acl` VALUES ('6', '管理', '分类', '*', 'admin', 'category', '*', '1', '0');
INSERT INTO `acl` VALUES ('7', '管理', '后台首页', '*', 'admin', 'main', '*', '0', '0');
INSERT INTO `acl` VALUES ('8', '管理', '权限管理', '*', 'admin', 'role', '*', '1', '0');
INSERT INTO `acl` VALUES ('9', '管理', '用户', '*', 'admin', 'user', '*', '1', '0');
INSERT INTO `acl` VALUES ('10', '管理', '图片管理', '*', 'admin', 'img', '*', '0', '0');
INSERT INTO `acl` VALUES ('11', '管理', '模板', '*', 'admin', 'template', '*', '0', '0');

CREATE TABLE `article` (
  `article_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL COMMENT '作者名',
  `category_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL COMMENT '标题',
  `contents` text COMMENT '文章内容',
  `created` int(11) NOT NULL COMMENT '发布时间',
  `updated` int(11) NOT NULL COMMENT '更新时间',
  `page_view` bigint(20) NOT NULL DEFAULT '0' COMMENT '访问量',
  `template_id` int(11) DEFAULT NULL COMMENT '模板ID',
  PRIMARY KEY (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(50) NOT NULL COMMENT '分类名',
  `articles` int(11) NOT NULL DEFAULT '0' COMMENT '分类下文章数量',
  `template_id` int(11) DEFAULT NULL COMMENT '模板ID',
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `htmlmaker` (
  `source_url` varchar(100) NOT NULL,
  `destination_url` varchar(100) NOT NULL,
  `update_job` varchar(20) NOT NULL,
  `is_made` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `rolename` varchar(20) DEFAULT NULL COMMENT '角色名',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `role` VALUES ('1', '管理员');
INSERT INTO `role` VALUES ('2', '用户');

CREATE TABLE `role2acl` (
  `role2acl_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) DEFAULT NULL,
  `acl_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`role2acl_id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;

INSERT INTO `role2acl` VALUES ('29', '1', '4');
INSERT INTO `role2acl` VALUES ('30', '1', '6');
INSERT INTO `role2acl` VALUES ('31', '1', '5');
INSERT INTO `role2acl` VALUES ('32', '1', '10');
INSERT INTO `role2acl` VALUES ('33', '1', '7');
INSERT INTO `role2acl` VALUES ('34', '1', '8');
INSERT INTO `role2acl` VALUES ('35', '1', '11');
INSERT INTO `role2acl` VALUES ('36', '1', '9');

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `userpass` varchar(50) NOT NULL,
  `salt` char(10) NOT NULL COMMENT '密码混淆码',
  `role_id` int(11) NOT NULL COMMENT '角色ID',
  `articles` int(11) DEFAULT NULL COMMENT '文章数',
  `created` int(11) DEFAULT NULL COMMENT '创建时间',
  `last_login` int(11) DEFAULT NULL COMMENT '上次登录时间',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `user` VALUES ('1', 'admin', '3ad3eb6695d1443bdd674db109b5866f', '5a459cd16c', '1', '0', '1514511569', '0');
