<?php
class MainController extends BaseController {
    function actionIndex() {
        // 这里是后台首页，但目前没有信息，所以直接跳到文章页
        $this->jump(url("admin/article", "index"));
    }
}