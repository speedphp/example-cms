<?php
class BaseController extends Controller {

    public $layout = "admin/layout.html";

    function init() {
        header("Content-type: text/html; charset=utf-8");
        if ( false == $this->checkAccess()) {
            //$this->tips("没有权限或登录过期，请重新登录！", url("login", "index"));
        }
		$this->layout_menus = $_SESSION["menus"];
        $this->layout_username = $_SESSION["username"];
        $this->layout_title = "CMS后台管理";
    }

    function tips($msg, $url) {
        $url = "location.href=\"{$url}\";";
        echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><script>function sptips(){alert(\"{$msg}\");{$url}}</script></head><body onload=\"sptips()\"></body></html>";
        exit;
    }

    function jump($url, $delay = 0) {
        echo "<html><head><meta http-equiv='refresh' content='{$delay};url={$url}'></head><body></body></html>";
        exit;
    }

    public static function err404($module, $controller, $action, $msg) {
        header("HTTP/1.0 404 Not Found");
        exit;
    }

    public function setRandStr(){
        $this->randstr = $_SESSION["randstr"] = sprintf("%s", uniqid());
    }
    public function checkRandStr(){
        return arg("randstr") && arg("randstr") == $_SESSION["randstr"];
    }
    private function checkAccess() {
		if (empty($_SESSION["username"]) || empty($_SESSION["role_id"]) || empty($_SESSION["acls"])) return false;
		global $__module, $__controller, $__action;
		foreach ($_SESSION["acls"] as $acl){
			if(strtolower($acl["acl_module"]) == strtolower($__module)
				&& strtolower($acl["acl_controller"]) == strtolower($__controller) ){
				if($acl["acl_action"] == "*" || strtolower($acl["acl_action"]) == strtolower($__action)){
					return true;
				}
			}
		}
		return false;
	}
} 