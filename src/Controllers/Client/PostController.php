<?php

namespace src\Controllers\Client;


use src\commons\Controller;
use src\Models\PostModel;

class PostController extends Controller
{

    private PostModel $postModel;

    public function __construct()
    {
        parent::__construct();
        $this->postModel = new PostModel();
    }

    function getPosts($limit = '')
    {
        $post = $this->postModel->getAllPost(trim($limit));
        echo json_encode($post);
    }

    function getPost($slug)
    {
        $post = $this->postModel->getSinglePost($slug);
        $this->postModel->insertView($post['id']);

        echo json_encode($post);
    }

    function getTrendingPosts($limit = '')
    {
        $post = $this->postModel->getPostTrending($limit);
        echo json_encode($post);
    }

    function getPopularPosts($limit = '')
    {
        $post = $this->postModel->getPostPopular($limit);
        echo json_encode($post);
    }

    function getPostByKeyword($keyword, $page = 1)
    {
        $limit = 2;
        if (!is_numeric($page)) {
            $page = 1;
        }
        $startLimit = ($page - 1) * $limit;
        $allPostByKeyword = $this->postModel->getAllPostByKeyword($keyword, $startLimit, $limit);

        echo json_encode($allPostByKeyword);
    }

    function getPostByCategory($slug, $page = 1)
    {
        $limit = 2;
        if (!is_numeric($page)) {
            $page = 1;
        }
        $startLimit = ($page - 1) * $limit;
        $allPostByCategory = $this->postModel->getAllPostByCategory($slug, $startLimit, $limit);

        echo json_encode($allPostByCategory);
    }

}
