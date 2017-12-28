<?php

date_default_timezone_set('PRC');
session_start();

return array(
	'debug' => 1,

	'rewrite_html' => array(
		'login.html'         => 'login/index',
		'index.html'         => 'main/index',
		'index/<p>.html'         => 'main/index',
		'view/<article_id>.html'    => 'main/view',
		'list/<category_id>.html'    => 'main/list',
		'list/<category_id>/<p>.html'    => 'main/list',
		'admin/<c>-<a>'      => 'admin/<c>/<a>',
	),
    'rewrite' => array(),
    'template_type' => array(
    	'index' => '首页模板',
		'list'  => '列表模板',
		'view'  => '文章页模板'
	),
	'template_dir' => "template",

    'htmlmakeup' => false,

    'manager' => array(
        'jake',
    ),
	'upload' => array(
		'path' => "/upload",
		'ext'  => "jpg,jpeg,gif,png",
		'maxSize' => 2097152, // 2M
	),
	'mysql' => array(
		'MYSQL_HOST' => 'localhost',
		'MYSQL_PORT' => '3306',
		'MYSQL_USER' => 'root',
		'MYSQL_DB'   => 'cms-one',
		'MYSQL_PASS' => '123456',
		'MYSQL_CHARSET' => 'utf8',
	),
);