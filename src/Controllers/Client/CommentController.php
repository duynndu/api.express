<?php


namespace src\Controllers\Client;


use src\commons\Controller;
use src\Models\CommentModel;

class CommentController extends Controller
{
    private CommentModel $commentModel;

    public function __construct()
    {
        $this->commentModel = new CommentModel();
    }

    function getCommentOfThePost($postId)
    {
        $comment = $this->commentModel->getCommentOfThePost($postId);

        echo json_encode($comment);
    }

//    public function postComment()
//    {
//        if (empty($_POST)) {
//            $_POST = [];
//            parse_str(file_get_contents('php://input'), $_POST);
//        }
//
//        print_r($_POST);
//        $_SESSION['cm_email'] = $_SESSION['cm_email'] ?? $_POST['email'];
//        $query = $this->commentModel->insert([
//            'email' => $_SESSION['cm_email'],
//            'message' => $_POST['message'],
//            'article_id' => $_POST['article_id']
//        ]);
//
//        $comment = $this->commentModel->getById($query['lastInsertId']);
//
//        echo json_encode($comment);
//    }
    public function postComment($data)
    {
        $query = $this->commentModel->insert([
            'email' => $data['email'],
            'message' => $data['message'],
            'article_id' => $data['article_id']
        ]);
        $comment = $this->commentModel->getById($query['lastInsertId']);

        return json_encode($comment);
    }
}