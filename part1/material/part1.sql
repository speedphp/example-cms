
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