<?php
class ArticleController extends BaseController {
    function actionIndex() {
        $page = (int)arg("p", 1);
        $articleObj = new Article();
        $categoryObj = new Category();
        $this->categorys = $categoryObj->findAll();
        $this->articles = $articleObj->findAll('', 'article_id DESC', "*", array($page, 10));
        $this->pager = $articleObj->page;

        $this->setRandStr();
    }

    function actionAdd() {
        $categoryObj = new Category();
        $this->categorys = $categoryObj->findAll('', "category_id DESC");
        if(count($this->categorys) == 0){
        	$this->tips("请先建分类再发文章", url("admin/category", "index"));
		}
		$templateObj = new Template();
		$this->templates = $templateObj->findAll(array("template_type"=>"view"), "template_id DESC");
		$this->temp_article_id = uniqid();
        $this->display("admin/article_form.html");
    }

    function actionEdit(){
        if(arg("article_id")){
            $articleObj = new Article();
            $article = $articleObj->find(array("article_id" => arg("article_id")));
            if($article){
                if($article["username"] == $_SESSION["username"]){
                    $categoryObj = new Category();
                    $this->categorys = $categoryObj->findAll('', "category_id DESC");
                    $this->info = $article;
					$templateObj = new Template();
					$this->templates = $templateObj->findAll(array("template_type"=>"view"), "template_id DESC");
					$this->temp_article_id = uniqid();
                    $this->display("admin/article_form.html");
                    return;
                }else{
                    $this->tips("您没有编辑别人文章的权限", url("admin/article", "index"));
                }
            }
        }
        $this->tips("参数错误", url("admin/article", "index"));
    }

    function actionDel(){
        if($this->checkRandStr()){
            $articleObj = new Article();
            $article = $articleObj->find(array("article_id" => arg("article_id")));
            if($article){
                if($article["username"] == $_SESSION["username"]){
                    $articleObj->delete(array("article_id" => $article["article_id"]));
                    $categoryObj = new Category();
                    $categoryObj->decr(array("category_id" => $article["category_id"]), "articles", 1);
					$imgObj = new Img();
					$imgObj->deleteByArticleId($article["article_id"]);
                    $this->tips("删除文章成功", url("admin/article", "index"));
                }else{
                    $this->tips("您没有删除别人文章的权限", url("admin/article", "index"));
                }
            }
        }
        $this->tips("参数错误", url("admin/article", "index"));
    }

    function actionSubmit() {
        $title = trim(arg("title"));
        $contents = trim(arg("contents"));
        $url = url("admin/article", "add");

        $articleObj = new Article();
        $article = null;
        if(arg("article_id")){
            $article = $articleObj->find(array("article_id" => arg("article_id")));
            if($article){
                if($article["username"] != $_SESSION["username"]){
                    $this->tips("您没有编辑别人文章的权限", url("admin/article", "index"));
                }
                $url = url("admin/article", "edit", array("id" => $article["article_id"]));
            }else{
                $this->tips("参数错误", url("admin/article", "index"));
            }
        }

        if (strlen($title) < 3) {
            $this->tips("标题不能少于三个字符", $url);
        }
        if (strlen($contents) < 10) {
            $this->tips("内容不能少于10个字符", $url);
        }
        $categoryObj = new Category();
        $category = $categoryObj->find(array("category_id" => arg("category_id")));
        if (!$category) {
            $this->tips("分类参数错误", $url);
        }
        if($article){
            $newrow = array(
                "category_id" => $category["category_id"],
                "title" => $title,
                "contents" => $contents,
                "updated" => time(),
				"template_id" => arg("template_id", 0)
            );
            $articleObj->update(array("article_id" => $article["article_id"]), $newrow);
			$this->setImg($article["article_id"]);
			$this->editUpdate($article["article_id"]);
            $this->tips("编辑成功！", url("admin/article", "index"));
        }else{
			$article_id = $articleObj->create(array(
				"username" => $_SESSION["username"],
				"category_id" => $category["category_id"],
				"title" => $title,
				"contents" => $contents,
				"created" => time(),
				"updated" => time(),
				"template_id" => arg("template_id", 0)
			));
			$this->setImg($article_id);
            $categoryObj->incr(array("category_id" => $category["category_id"]), "articles", 1);
			$this->addUpdate($article_id, $category["category_id"]);
            $this->tips("新文章发布成功", url("admin/article", "index"));
        }
    }

    // 将文章图片管理的临时ID换成实际ID
    private function setImg($article_id) {
		$temp_article_id = arg("temp_article_id");
		$imgObj = new Img();
		$imgObj->update(array("temp_article_id"=>$temp_article_id), array(
			"article_id" => $article_id,
			"temp_article_id" => ""
		));
	}

    // 接收上传图片
    function actionUpimg() {
		$localName = ""; $err = ""; $msg = "''";
		$tempPath = APP_DIR . $GLOBALS["upload"]["path"]."/".date("YmdHis").mt_rand(10000,99999).'.tmp';

		// 如果是php5.6出现“PHP Deprecated:  Automatically populating $HTTP_RAW_POST_DATA is deprecated”的问题
		// 请在php.ini内设置always_populate_raw_post_data = -1
		if(isset($_SERVER['HTTP_CONTENT_DISPOSITION']) && preg_match('/attachment;\s+name="(.+?)";\s+filename="(.+?)"/i',$_SERVER['HTTP_CONTENT_DISPOSITION'],$info)){
			//HTML5上传
			file_put_contents($tempPath, file_get_contents("php://input"));
			$localName = urldecode($info[2]);
		}else{//标准表单式上传
			$upfile = @$_FILES['filedata'];
			if(!isset($upfile)){
				$err = '文件域的name错误';
			}elseif(!empty($upfile['error'])){
				$err = $this->upimg_err[$upfile['error']];
			}elseif(empty($upfile['tmp_name']) || $upfile['tmp_name'] == 'none'){
				$err = '无文件上传';
			}else{
				move_uploaded_file($upfile['tmp_name'],$tempPath);
				$localName = $upfile['name'];
			}
		}
		if( $err == '' ){
			$fileInfo = pathinfo($localName); $extension = $fileInfo['extension'];
			if(preg_match('/^('.str_replace(',','|', $GLOBALS["upload"]["ext"]).')$/i',$extension)) {
				$bytes = filesize($tempPath);
				if($bytes > $GLOBALS["upload"]["maxSize"]){
					$err = '请不要上传大小超过'.$this->formatBytes($GLOBALS["upload"]["maxSize"]).'的文件';
				}else{
					$newFilename = date("YmdHis").mt_rand(1000,9999).'.'.$extension;
					$newFilepath = APP_DIR . $GLOBALS["upload"]["path"]."/".$newFilename;
					rename($tempPath, $newFilepath);
					@chmod($newFilepath,0755);
					$url = $GLOBALS["upload"]["path"]."/".$newFilename;
					$msg="{'url':'".$url."','localname':'".$this->jsonString($localName)."','id':'1'}";

					$newrow = array(
						"article_id" => 0,
						"upload_path" => $url,
						"temp_article_id" => arg("temp_article_id"),
						"created_date" => date("Ymd")
					);
					$imgObj = new Img();
					$imgObj->create($newrow);
				}
			}else{
				$err = '上传文件扩展名必需为：'. $GLOBALS["upload"]["ext"];
			}
			@unlink($tempPath);
		}
		echo "{'err':'".$this->jsonString($err)."','msg':".$msg."}";
	}

	private $upimg_err = array(
		1 => '文件大小超过了php.ini定义的upload_max_filesize值',
		2 => '文件大小超过了HTML定义的MAX_FILE_SIZE值',
		3 => '文件上传不完全',
		4 => '无文件上传',
		6 => '缺少临时文件夹',
		7 => '写文件失败',
		8 => '上传被其它扩展中断'
	);
    private function formatBytes($bytes) {
		if($bytes >= 1073741824) {
			$bytes = round($bytes / 1073741824 * 100) / 100 . 'GB';
		} elseif($bytes >= 1048576) {
			$bytes = round($bytes / 1048576 * 100) / 100 . 'MB';
		} elseif($bytes >= 1024) {
			$bytes = round($bytes / 1024 * 100) / 100 . 'KB';
		} else {
			$bytes = $bytes . 'Bytes';
		}
		return $bytes;
	}
	private function jsonString($str){
		return preg_replace("/([\\\\\/'])/",'\\\$1',$str);
	}

    private function addUpdate($article_id, $category_id){
		if($GLOBALS["htmlmakeup"] == false)return ;
		$urls = array(array("main", "index"));
		$articleObj = new Article();
		$index_pages = range(1, floor($articleObj->findCount(1)/5));
		foreach($index_pages as $p){
			$urls[] = array("main", "index", array("p" => $p));
		}
		$urls[] = array("main", "list", array("category_id"=>$category_id));
		$category_pages = range(1, floor($articleObj->findCount(array("category_id" => $category_id))/5));
		foreach($category_pages as $p){
			$urls[] = array("main", "list", array("category_id"=>$category_id, "p" => $p));
		}
		$urls[] = array("main", "view", array("article_id"=>$article_id));

		$htmlObj = new HtmlMaker($GLOBALS["htmlmakeup"]);
		$job = "article-".$article_id;
		foreach($urls as $url){
			$htmlObj->setUrl($job, $url, $GLOBALS["rewrite_html"]);
		}
		$htmlObj->makeAll($job, count($urls));
	}

	private function editUpdate($article_id){
		if($GLOBALS["htmlmakeup"] == false)return ;
		$htmlObj = new HtmlMaker($GLOBALS["htmlmakeup"]);
		$job = $htmlObj->setUrl("article-".$article_id,
			array("main", "view", array("article_id"=>$article_id)), $GLOBALS["rewrite_html"]);
		$htmlObj->makeAll($job, 1);
	}
}