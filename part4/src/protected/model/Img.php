<?php
class Img extends Model {
	public $table_name = "img";

	function deleteByArticleId($article_id) {
		$result = $this->findAll(array("article_id" => $article_id));
		if($result){
			foreach ($result as $r){
				@unlink( APP_DIR .$r["upload_path"]);
			}
			$this->delete(array("article_id" => $article_id));
		}
	}
}