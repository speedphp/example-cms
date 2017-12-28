<?php

class TemplateController extends BaseController{
	private $template_dir = APP_DIR.DS.'protected'.DS.'view'.DS;

	function actionIndex(){
		$templateObj = new Template();
		$this->template_type = $GLOBALS["template_type"];
		$this->templates = $templateObj->findAll(null, "template_id DESC");
		$this->setRandStr();
	}

	function actionForm(){
		$this->template_type = $GLOBALS["template_type"];
		if($template_id = arg("template_id")){
			$templateObj = new Template();
			$this->info = $templateObj->find(array("template_id" => $template_id));
			if(!$this->info)$this->tips("参数错误", url("admin/template", "index"));
			$filepath = $this->template_dir . DS . $GLOBALS["template_dir"] . DS .$this->info["filename"];
			if(file_exists($filepath)){
				$this->code = file_get_contents($filepath);
			}else{
				$this->code = "";
			}
		}
	}

	function actionDel(){
		if($this->checkRandStr()){
			if($template_id = arg("template_id")){
				$templateObj = new Template();
				$info = $templateObj->find(array("template_id" => $template_id));
				if($info){
					if($info["template_type"] != "index"){
						if($info["template_type"] == "list"){
							$relateObj = new Category();
						}else{
							$relateObj = new Article();
						}
						$relateObj->update(array("template_id" => $template_id), array("template_id"=>0));
					}
					$templateObj->delete(array("template_id" => $template_id));
					$filepath = $this->template_dir . DS . $GLOBALS["template_dir"] . DS .$info["filename"];
					@unlink($filepath);
					$this->tips("删除成功", url("admin/template", "index"));
				}
			}
		}
		$this->tips("参数错误", url("admin/template", "index"));
	}

	function actionEdit(){
		if($template_id = arg("template_id")){
			$templateObj = new Template();
			$info = $templateObj->find(array("template_id" => $template_id));
			if(!$info)$this->tips("参数错误", url("admin/template", "index"));
			$newrow = array(
				"template_name" => arg("template_name"),
				"updated" => time(),
				"update_username" => $_SESSION["username"]
			);
			file_put_contents($this->template_dir . DS . $GLOBALS["template_dir"] . DS .$info["filename"], arg("code"));
			$templateObj->update(array("template_id"=>$template_id), $newrow);
			$this->tips("修改模板成功", url("admin/template", "form", array("template_id"=>$template_id)));
		}
		$this->tips("参数错误", url("admin/template", "index"));
	}

	function actionAdd(){
		$template_name = arg("template_name");
		$filename = arg("filename");
		if($template_name && $filename){
			if(preg_match("/^[\w-\.]+$/", $filename)) {
				$templateObj = new Template();
				if (!$templateObj->find(array("filename" => $filename))){
					$newrow = array(
						"template_type" => arg("template_type", "view"),
						"template_name" => $template_name,
						"filename" => $filename,
						"created" => time(),
						"updated" => time(),
						"create_username" => $_SESSION["username"],
						"update_username" => $_SESSION["username"]
					);
					$templateObj->create($newrow);
					file_put_contents($this->template_dir . DS . $GLOBALS["template_dir"] . DS .$filename, arg("code"));
					$this->tips("新建模板成功", url("admin/template", "index"));
				}else {
					$msg = "文件名已存在";
				}
			}else{
				$msg = "文件名只能由英文数字点号组成";
			}
		}else{
			$msg = "请填写完整信息";
		}
		$this->tips($msg, url("admin/template", "form"));
	}
}