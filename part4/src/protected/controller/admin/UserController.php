<?php

class UserController extends BaseController {

	// 用户列表
	function actionIndex(){
		$userObj = new User();
		$roleObj = new Role();
		$this->users = $userObj->findAll(null, "user_id DESC");
		$this->roles = $roleObj->findAll();
		$this->setRandStr();
	}

	// 新增，编辑表单
	function actionForm(){
		if($user_id = arg("user_id")){
			$userObj = new User();
			$this->info = $userObj->find(array("user_id" => $user_id));
			if(!$this->info)$this->tips("参数错误", url("admin/user", "index"));
		}
		$roleObj = new Role();
		$this->roles = $roleObj->findAll();
		$this->display("admin/user_form.html");
	}

	// 提交
	function actionAdd(){
		if($username = trim(arg("username"))){
			if(strlen($username) >= 3 && strlen($username) < 20){
				$userpass = arg("userpass");
				if(strlen($userpass) >= 6 && strlen($userpass) < 20){
					if($userpass == arg("userpass2")){
						$userObj = new User();
						if(! $userObj->checkExists($username)){
							$salt = substr(uniqid(), 0, 10);
							$roleObj = new Role();
							$role_id = ($roleObj->checkRoleIdExists(arg("role_id"))) ? arg("role_id") : 2;
							$newrow = array(
									"username" => $username,
									"userpass" => md5($username . $salt . $userpass),
									"salt" => $salt,
									"role_id" => $role_id,
									"articles" => 0,
									"created" => time(),
									"last_login" => 0
							);
							$userObj->create($newrow);
							$this->tips("注册成功", url("admin/user", "index"));
						}else{
							$msg = "用户名已经存在";
						}
					}else{
						$msg = "两次输入密码不一致";
					}
				}else{
					$msg = "密码不能大于20字符，小于6个字符";
				}
			}else {
				$msg = "用户名不能大于20字符，小于3个字符";
			}
		}else{
			$msg = "请输入用户名";
		}
		$this->tips($msg, url("admin/user", "add"));
	}

	// 编辑用户信息
	function actionEdit(){
		if($user_id = arg("user_id")){
			$userObj = new User();
			$roleObj = new Role();
			$info = $userObj->find(array("user_id" => $user_id));
			$userpass = arg("userpass");
			if(!empty($userpass)){
				if(strlen($userpass) >= 6 && strlen($userpass) < 20){
					// 修改密码
					if($userObj->isManager($info["username"]) && $_SESSION["username"] != $info["username"]){
						$this->tips("你不能修改其他管理员密码", url("admin/user", "index"));
					}
					if($userpass == arg("userpass2")){
						$salt = substr(uniqid(), 0, 10);
						$role_id = ($roleObj->checkRoleIdExists(arg("role_id"))) ? arg("role_id") : 2;
						$newrow = array(
							"userpass" => md5($info["username"] . $salt . $userpass),
							"salt" => $salt,
							"role_id" => $role_id,
						);
						$userObj->update(array("user_id"=>$user_id), $newrow);
						$msg = "修改成功";
					}else{
						$msg = "两次输入密码不一致";
					}
				}else{
					$msg = "密码不能大于20字符，小于6个字符";
				}
				$this->tips($msg, url("admin/user", "form", array("user_id"=>$user_id)));
			}else{
				// 不修改密码，那么只修改角色组
				$role_id = ($roleObj->checkRoleIdExists(arg("role_id"))) ? arg("role_id") : 2;
				if($role_id != $info["role_id"]){
					$userObj->update(array("user_id"=>$user_id), array("role_id"=>$role_id));
				}
				$this->tips("修改成功", url("admin/user", "form", array("user_id"=>$user_id)));
			}
		}else{
			$this->tips("参数错误", url("admin/user", "index"));
		}
	}

	// 删除用户
	function actionDel(){
		if($this->checkRandStr()){
			if($user_id = arg("user_id")){
				$userObj = new User();
				$info = $userObj->find(array("user_id" => $user_id));
				if($info){
					if( !$userObj->isManager($info["username"]) || $_SESSION["username"] == $info["username"]){
						$userObj->delete(array("user_id" => $user_id));
						$this->tips("删除成功", url("admin/user", "index"));
					}else{
						$this->tips("不能删除管理员或者自己", url("admin/user", "index"));
					}
				}
			}
		}
		$this->tips("参数错误", url("admin/user", "index"));
	}

}