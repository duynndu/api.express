<?php

namespace src\Models;

use src\Commons\Model;

class CommentModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    protected ?string $table = 'comments';

    public function getComment($article_id){
        $sql = "SELECT * FROM comments WHERE article_id = :article_id ORDER BY id DESC";
        return $this->query($sql,[
           ':article_id'=>$article_id
        ])->fetchAll();
    }
    public function getCommentOfThePost($id){
        $sql = "SELECT cm.* FROM comments cm
        JOIN articles a
        ON cm.article_id = a.id
        WHERE a.id = :id ORDER BY cm.id DESC";
        return $this->query($sql,[
           ':id'=>$id
        ])->fetchAll();
    }
}