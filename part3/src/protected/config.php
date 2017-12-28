<?php

date_default_timezone_set('PRC');
session_start();

$config = array(
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

    'htmlmakeup' => 'test',

    'manager' => array(
        'jake',
    ),
);

$domain = array(
	"localhost" => array( // 调试配置
		'debug' => 1,
		'mysql' => array(
			'MYSQL_HOST' => 'localhost',
			'MYSQL_PORT' => '3306',
			'MYSQL_USER' => 'root',
			'MYSQL_DB'   => 'cms-one',
			'MYSQL_PASS' => '123456',
			'MYSQL_CHARSET' => 'utf8',
		),
	),
	"speedphp.com" => array( //线上配置
		'debug' => 0,
		'mysql' => array(),
	),
);
// 为了避免开始使用时会不正确配置域名导致程序错误，加入判断
if(empty($domain[$_SERVER["HTTP_HOST"]])) die("配置域名不正确，请确认".$_SERVER["HTTP_HOST"]."的配置是否存在！");

return $domain[$_SERVER["HTTP_HOST"]] + $config;
