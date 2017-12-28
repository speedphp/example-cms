<?php
class LoginController extends BaseController {

    // 登录表单
    function actionIndex() {
        $this->title = "登录管理平台";
    }

    // 登录检查
    function actionSubmit() {
        if (arg("username") && arg("password")) {
            $userObj = new User();
            $is_user = $userObj->check(arg("username"), arg("password"));
            if ($is_user == true) {
                $_SESSION["username"] = arg("username");
                $this->jump(url("admin/main", "index"));
            }
        }
        $this->tips("用户名或密码错误！", url("login", "index"));
    }

    // 退出登录
    function actionLogout(){
        $_SESSION["username"] = null;
        unset($_SESSION["username"]);
        $this->jump(url("main", "index"));
    }
}