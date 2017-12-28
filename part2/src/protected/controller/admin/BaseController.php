<?php
class BaseController extends Controller {

    public $layout = "admin/layout.html";

    function init() {
        header("Content-type: text/html; charset=utf-8");
        if (empty($_SESSION["username"])) {
            $this->tips("没有权限或登录过期，请重新登录！", url("login", "index"));
        }
        $this->username = $_SESSION["username"];
        $this->title = "CMS后台管理";
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
} 