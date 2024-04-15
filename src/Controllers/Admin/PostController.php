<?php

namespace src\Controllers\Admin;

use src\Commons\Controller;
use src\Models\PostModel;
use src\Models\CategoryModel;

class PostController extends Controller
{
    private postModel $postModel;

    public function __construct()
    {
        parent::__construct();
        $this->postModel = new postModel();
    }

   function viewStatistics()
   {
       $allViewsPost = $this->postModel->viewStatistics($_GET['from'], $_GET['to'], $_GET['type']);
       $top3ViewsPost = [];
       $idTop3ViewsPost = $this->postModel->getPostPopular(3);

       foreach (array_column($idTop3ViewsPost, 'id') as $key => $value){
           $top3ViewsPost[$key] = $this->postModel->viewStatisticsByIdPost($_GET['from'], $_GET['to'],$value, $_GET['type']);
           $top3ViewsPost[$key] = [
               'view_sum' => array_column($top3ViewsPost[$key], 'view_count'),
               'post_id' => $value,
           ];
       }
       echo json_encode(array_merge([
               'view_sum' => array_column($allViewsPost, 'view_count'),
               'date' => array_column($allViewsPost, 'Date'),
           ], $top3ViewsPost)
       );
   }

    function addPost()
    {
        $_POST = json_decode(file_get_contents('php://input', true), true);

        $this->postModel->insert([
            'title' => $_POST['title'],
            'summary' => $_POST['summary'],
            'content' => $_POST['content'],
            'category_id' => $_POST['category_id'],
            'image' => $_POST['image'],
            'user_id' => 1,
            'slug' => $this->slugify->slugify($_POST['title'])
        ]);
    }

    function editPost()
    {
        $_POST = json_decode(file_get_contents('php://input', true), true);
        $this->postModel->update([
            'title' => $_POST['title'],
            'summary' => $_POST['summary'],
            'content' => $_POST['content'],
            'category_id' => $_POST['category_id'],
            'image' => $_POST['image'],
            'user_id' => 1,
            'slug' => $this->slugify->slugify($_POST['title'])
        ],$_POST['id']);
    }

    function deletePosts($ids)
    {
        $ids = explode(';', $ids);
        foreach ($ids as $id) {
            $this->postModel->delete($id);
        }
    }

}