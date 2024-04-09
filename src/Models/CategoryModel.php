<?php

namespace src\Models;

use src\Commons\Model;

class CategoryModel extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    protected ?string $table = 'categories';

    function getCategory($slug)
    {
        $sql = "SELECT * FROM categories WHERE slug = :slug";
        return $this->query($sql,[':slug'=>$slug])->fetch();
    }
}