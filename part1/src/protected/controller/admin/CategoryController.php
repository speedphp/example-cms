<?php
class CategoryController extends BaseController {
    function actionIndex() {
        $categoryObj = new Category();
        $this->categorys = $categoryObj->findAll();
    }

    function actionAdd() {
        $this->display("admin/category_form.html");
    }

    function actionEdit() {
        if (arg("category_id")) {
            $categoryObj = new Category();
            $cat_info = $categoryObj->find(array("category_id" => arg("category_id")));
            if ($cat_info) {
                $this->info = $cat_info;
                $this->display("admin/category_form.html");
                return;
            }
        }
        $this->tips("没有此分类", url("admin/category", "index"));
    }

    function actionSubmit() {
        $category_name = trim(arg("category_name"));
        $category_id = arg("category_id");
        $categoryObj = new Category();
        if ($category_id && $category_name) {
            $cat_info = $categoryObj->find(array("category_id" => $category_id));
            if ($cat_info) {
                $categoryObj->update(array("category_id" => $category_id), array("category_name" => $category_name));
                $this->tips("已修改分类", url("admin/category", "index"));
            }else{
                $this->tips("无此分类", url("admin/category", "index"));
            }
        }
        if ($category_name) {
            $categoryObj->create(array("category_name" => $category_name));
            $this->tips("成功增加分类", url("admin/category", "index"));
        } else {
            $this->tips("请填写分类", url("admin/category", "add"));
        }
    }
}