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
            );
            $articleObj->update(array("article_id" => $article["article_id"]), $newrow);
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
			));
            $categoryObj->incr(array("category_id" => $category["category_id"]), "articles", 1);
			$this->addUpdate($article_id, $category["category_id"]);
            $this->tips("新文章发布成功", url("admin/article", "index"));
        }
    }

    private function addUpdate($article_id, $category_id){
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
		$htmlObj = new HtmlMaker($GLOBALS["htmlmakeup"]);
		$job = $htmlObj->setUrl("article-".$article_id,
			array("main", "view", array("article_id"=>$article_id)), $GLOBALS["rewrite_html"]);
		$htmlObj->makeAll($job, 1);
	}
}