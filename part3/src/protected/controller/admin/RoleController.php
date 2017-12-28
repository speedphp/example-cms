<?php

class RoleController extends BaseController {

	// 角色列表
	function actionIndex() {
		$roleObj = new Role();
		$aclObj = new Acl();
		$roles = $roleObj->findAll();
		$acls = $aclObj->findAll(null, "acl_module ASC, acl_controller ASC");
		if($roles){
			foreach($roles as $k => $role){
				$role_acls = $roleObj->getRole($role["role_id"]);
				$roles[$k]["acls"] = array();
				foreach($acls as $acl){
					foreach($role_acls as $role2acl){
						if($acl["acl_id"] == $role2acl["acl_id"]){
							$roles[$k]["acls"][] = $acl["module_name"] . "-" . $acl["controller_name"] . "（" . $acl["action_name"] . "）";
						}
					}
				}
			}
		}
		$this->roles = $roles;
	}

	//  编辑角色表单
	function actionForm() {
		$roleObj = new Role();
		if($rolename = arg("rolename")){
			if($role_id = arg("role_id")){
				$info = $roleObj->find(array("role_id" => $role_id));
				if(! $info){
					$this->tips("参数错误", url("admin/role", "index"));
				}
				if($info["rolename"] != $rolename){
					$roleObj->update(array("role_id" => $role_id), array("rolename" => $rolename));
				}
				$roleObj->setRole($role_id, arg("acl_ids"));
				$this->tips("编辑角色成功", url("admin/role", "form", array("role_id"=>$role_id)));
			}else{
				if(! $roleObj->checkRoleNameExists($rolename)){
					$role_id = $roleObj->create(array("rolename" => $rolename));
					$roleObj->setRole($role_id, arg("acl_ids"));
					$this->tips("新增角色成功", url("admin/role", "index"));
				}else{
					$this->tips("角色名已经存在", url("admin/role", "form"));
				}
			}
		}

		$aclObj = new Acl();
		$this->acls = $aclObj->findAll(null, "acl_module ASC, acl_controller ASC");

		if($role_id = arg("role_id")){
			$this->info = $roleObj->find(array("role_id" => $role_id));
			if(! $this->info){
				$this->tips("参数错误", url("admin/role", "index"));
			}
			$this->role_acls = $roleObj->getRole($role_id);
		}
	}

	// 权限列表
	function actionAcl(){
		$aclObj = new Acl();
		$this->acls = $aclObj->findAll(null, "acl_module ASC, acl_controller ASC");
		$this->setRandStr();
	}

	// 编辑权限
	function actionAclform(){
		$aclObj = new Acl();
		if(arg("controller_name") && arg("acl_controller")){
			$newrow = array(
				"module_name" => arg("module_name") ? arg("module_name") : "默认module",
				"controller_name" => arg("controller_name"),
				"action_name" => arg("action_name", "*"),
				"acl_module" => arg("acl_module") ? arg("acl_module") : "default",
				"acl_controller" =>arg("acl_controller"),
				"acl_action" => arg("acl_action", "*"),
				"is_menu" => (arg("is_menu") == "on") ? 1 : 0,
				"menu_sort" => arg("menu_sort", 0),
			);
			if($acl_id = arg("acl_id")){
				$aclObj->update(array("acl_id" => $acl_id), $newrow);
				$this->tips("修改成功", url("admin/role", "aclform", array("acl_id"=>$acl_id)));
			}else{
				$aclObj->create($newrow);
				$this->tips("增加成功", url("admin/role", "acl"));
			}
		}

		if($acl_id = arg("acl_id")){
			$this->info = $aclObj->find(array("acl_id"=>$acl_id));
			if(! $this->info){
				$this->tips("参数错误", url("admin/role", "acl"));
			}
		}
	}

	// 删除权限
	function actionAcldel(){
		if($this->checkRandStr()){
			$aclObj = new Acl();
			$info = $aclObj->find(array("acl_id"=>arg("acl_id")));
			if(! $info){
				$this->tips("参数错误", url("admin/role", "acl"));
			}
			$aclObj->delete(array("acl_id"=>arg("acl_id")));
			// 清理role2acl
			$aclObj->table_name = "role2acl";
			$aclObj->delete(array("acl_id"=>arg("acl_id")));
			$this->tips("删除成功", url("admin/role", "acl"));
		}
		$this->tips("参数错误", url("admin/role", "acl"));
	}
}