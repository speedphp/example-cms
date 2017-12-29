<?php

class User extends Model {
	public $table_name = "user";

	// 检查用户名是否存在
	public function checkExists($user) {
		$result = $this->find(array("username" => $user));
		return !empty($result);
	}

	// 检查用户是否管理员
	public function isManager($user) {
		foreach ($GLOBALS['manager'] as $config_user) {
			if ($config_user == $user) {
				return true;
			}
		}
		return false;
	}

	// 检查用户登录信息
	public function check($user, $pass) {
		$info = $this->find(array("username" => $user));
		if ($info) {
			$inputpass = md5($info["username"] . $info["salt"] . $pass);
			if($inputpass == $info["userpass"]){
				return true;
			}
		}
		return false;
	}
}