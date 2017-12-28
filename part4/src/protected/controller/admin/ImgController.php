<?php

class ImgController extends BaseController {

	function actionIndex(){
		$imgObj = new Img();
		$articleObj = new Article();
		$result = $imgObj->findAll(null, "article_id DESC");
		if($result){
			foreach ($result as $k => $v){
				$result[$k]["article"] = $articleObj->find(array("article_id"=>$v["article_id"]));
			}
		}
		$this->imgs = $result;
		$this->setRandStr();
	}

	function actionDel(){
		if($this->checkRandStr()){
			$imgObj = new Img();
			$imgObj->delete(array("img_id" => arg("img_id")));
			$this->tips("删除成功", url("admin/img", "index"));
		}
	}
}