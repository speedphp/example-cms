<?php
class HtmlController extends BaseController {
	function actionIndex() {

	}

	function actionStart(){
		$htmlObj = new HtmlMaker($GLOBALS["htmlmakeup"]);
		$_SESSION["job"] = $htmlObj->start($GLOBALS["rewrite_html"]);
		echo "true";
	}

	function actionDo(){
		$htmlObj = new HtmlMaker($GLOBALS["htmlmakeup"]);
		$result = $htmlObj->makeAll($_SESSION["job"], 10);
		if(false === $result){
			echo "true";
		}else{
			$this->result = $result;
			$this->layout = null;
			$this->display("admin/html_do_innner.html");
		}
	}

	function actionClear(){
		$htmlObj = new HtmlMaker($GLOBALS["htmlmakeup"]);
		$htmlObj->clear();
		$this->tips("已清理全部HTML", url("admin/html", "index"));
	}
}