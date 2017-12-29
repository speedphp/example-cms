<?php
class Role extends Model {
	public $table_name = "role";

	// 检查角色ID是否存在
	public function checkRoleIdExists($role_id){
		$result = $this->find(array("role_id" => $role_id));
		return !empty($result);
	}

	// 检查角色名是否存在
	public function checkRoleNameExists($rolename){
		$result = $this->find(array("rolename" => $rolename));
		return !empty($result);
	}

	// 取角色对应的菜单
	public function getMenus($role_id, $manager_module_name) {
		$result = array();
		$aclObj = new Acl();
		$menus = $aclObj->findAll(array("is_menu" => 1), "menu_sort ASC");
		$role_acls = $this->getRole($role_id);
		if($menus){
			foreach($menus as $menu){
				if($role_acls){
					foreach($role_acls as $role_acl){
						if($role_acl["acl_id"] == $menu["acl_id"] && $menu["acl_module"] == $manager_module_name){
							$result[] = $menu;
						}
					}
				}
			}
		}
		return $result;
	}

	// 检查并增加和减少权限关系
	function setRole($role_id, $acl_ids) {
		$role2aclObj = new Role2acl();
		$role2aclObj->delete(array("role_id" => $role_id));
		if(!empty($acl_ids)){
			foreach($acl_ids as $acl_id){
				$newrow = array(
					"role_id" => $role_id,
					"acl_id" => $acl_id,
				);
				$role2aclObj->create($newrow);
			}
		}
	}

	// 取得角色全部权限详情
	function getAcls($role_id) {
		$role2aclObj = new Role2acl();
		$aclObj = new Acl();
		$result = $role2aclObj->findAll(array("role_id" => $role_id));
		foreach ($result as $k => $v){
			$result[$k] = $aclObj->find(array("acl_id"=>$v["acl_id"]));
		}
		return $result;
	}

	// 取得角色全部权限ID
	function getRole($role_id) {
		$role2aclObj = new Role2acl();
		return $role2aclObj->findAll(array("role_id" => $role_id));
	}
}