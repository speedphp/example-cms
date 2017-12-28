
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

CREATE TABLE `img` (
  `img_id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `upload_path` varchar(50) NOT NULL COMMENT '上传路径',
  `temp_article_id` char(13) DEFAULT NULL COMMENT '临时文章标识',
  `created_date` int(8) DEFAULT NULL COMMENT '创建日期，方便按日期删除',
  PRIMARY KEY (`img_id`,`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `rolename` varchar(20) DEFAULT NULL COMMENT '角色名',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `role2acl` (
  `role2acl_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) DEFAULT NULL,
  `acl_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`role2acl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `template` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_type` char(10) NOT NULL COMMENT '模板类型',
  `template_name` varchar(20) NOT NULL COMMENT '模板名',
  `filename` varchar(100) NOT NULL COMMENT '模板文件名',
  `created` int(11) NOT NULL,
  `updated` int(11) NOT NULL,
  `create_username` varchar(30) NOT NULL COMMENT '创建者',
  `update_username` varchar(30) NOT NULL COMMENT '修改者',
  PRIMARY KEY (`template_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
