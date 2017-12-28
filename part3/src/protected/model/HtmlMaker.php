<?php
class HtmlMaker extends Model{
	public $table_name = "htmlmaker";
	private $htmlmakeup;

	public function __construct($htmlmakeup) {
		$this->htmlmakeup = $htmlmakeup;
	}

	public function beforeDisplay($rewrite_rules){
		if(isset($_GET["htmlmakeup"]) && count($rewrite_rules) > 0){
			$GLOBALS['rewrite'] = $rewrite_rules;
		}
	}

	public function afterDisplay(){
		if(isset($_GET["htmlmakeup"]) && substr($_GET["htmlmakeup"], 0 ,strlen($this->htmlmakeup)) == $this->htmlmakeup) {
			$update_job = substr($_GET["htmlmakeup"], strlen($this->htmlmakeup));
			if ($update_job) {
				if (isset($GLOBALS['url_array_instances']) && count($GLOBALS['url_array_instances']) > 0) {
					foreach ($GLOBALS['url_array_instances'] as $source => $destination) {
						if (!$this->find(array("source_url" => $source))) {
							$this->create(array(
								"source_url" => $source,
								"destination_url" => $destination,
								"update_job" => $_GET["htmlmakeup"],
								"is_made" => 0,
							));
						}
					}
				}
			}
		}
	}

	public function start($rewrite_rules){
		$tmp_rules = $GLOBALS["rewrite"];
		$GLOBALS["rewrite"] = $rewrite_rules;
		$main_url = url("main", "index");
		$GLOBALS["rewrite"] = $tmp_rules;
		$job = uniqid($this->htmlmakeup);
		$this->clear();
		$this->create(array(
			"source_url" => $_SERVER['SCRIPT_NAME']."?c=main&a=index",
			"destination_url" => $main_url,
			"update_job" => $job,
			"is_made" => 0,
		));
		return $job;
	}

	public function clear(){
		$pages = $this->findAll();
		$this->delete(1);
		$base_url = $GLOBALS['http_scheme'].$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER["SCRIPT_NAME"]), '/\\');
		foreach($pages as $page){
			$file_uri = str_ireplace($base_url, APP_DIR, $page["destination_url"]);
			@unlink($file_uri);
		}
	}

	public function setUrl($job, $url_arg, $rewrite_rules){
		$tmp_rules = $GLOBALS["rewrite"];
		$GLOBALS["rewrite"] = $rewrite_rules;
		$url_arg_array = (isset($url_arg[2]) && is_array($url_arg[2])) ? $url_arg[2] : array();
		$destination_url = url($url_arg[0], $url_arg[1], $url_arg_array);
		$GLOBALS["rewrite"] = false;
		$source_url = url($url_arg[0], $url_arg[1], $url_arg_array);

		$this->create(array(
			"source_url" => $source_url,
			"destination_url" => $destination_url,
			"update_job" => $job,
			"is_made" => 0,
		));
		$GLOBALS["rewrite"] = $tmp_rules;
		return $job;
	}

	public function makeAll($job, $limit){
		$urls = $this->findAll(array("update_job" => $job, "is_made" => 0), null, "*", $limit);
		$made_list = array();
		if($urls){
			foreach($urls as $url_info){
				$made_list[] = $this->make($url_info, $job);
			}
			return $made_list;
		}else{
			return false;
		}
	}

	private function make($url_info, $job){
		$base_url = $GLOBALS['http_scheme'].$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER["SCRIPT_NAME"]), '/\\');
		$file_uri = str_ireplace($base_url, APP_DIR, $url_info["destination_url"]);
		$this->mkdirs(dirname($file_uri));

		$url = $base_url.$url_info["source_url"];
		if($this->get_http_response_code($url) == '200'){
			$page = file_get_contents($url."&htmlmakeup=".$job);
			@file_put_contents($file_uri, $page);
		}
		$this->update($url_info, array("is_made" => 1));
		return $url_info["destination_url"];
	}

	private function get_http_response_code($url) {
		$headers = get_headers($url);
		return substr($headers[0], 9, 3);
	}

	private function mkdirs($dir, $mode = 0777) {
		if (!is_dir($dir)) {
			$this->mkdirs(dirname($dir), $mode);
			return @mkdir($dir, $mode);
		}
		return true;
	}
}