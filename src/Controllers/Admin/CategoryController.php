<?php
namespace src\Controllers\Admin;

use src\Commons\Controller;
use src\Models\CategoryModel;

class CategoryController extends Controller
{
    private CategoryModel $categoryModel;
    public function __construct()
    {
        parent::__construct();
        $this->categoryModel = new CategoryModel();
    }

    function addCategory()
    {
        $_POST = json_decode(file_get_contents('php://input', true), true);

        $this->categoryModel->insert([
            'name' => $_POST['name'],
            'slug' => $this->slugify->slugify($_POST['name'])
        ]);
    }

    function editCategory()
    {
        $_POST = json_decode(file_get_contents('php://input', true), true);
        $this->categoryModel->update([
            'name' => $_POST['name'],
            'slug' => $this->slugify->slugify($_POST['name'])
        ],$_POST['id']);
    }

    function deleteCategories($ids)
    {
        $ids = explode(';', $ids);
        foreach ($ids as $id) {
            $this->categoryModel->delete($id);
        }
    }


}