<?php
class Template extends Model {
	public $table_name = "template";

	// 删除相关模板设置
	function deleteRelateTemplateId($type, $template_id) {
		if($type == "list"){
			$categoryObj = new Category();
			$categoryObj->update(array("template_id" => $template_id), array("$template_id"=>""));
		}else if($type == "view") {
			$articleObj = new Article();
			$articleObj->update(array("template_id" => $template_id), array("$template_id"=>""));
		}
	}
}