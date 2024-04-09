<?php


namespace src\Controllers\Client;


use src\commons\Controller;
use src\Models\CategoryModel;

class CategoryController extends Controller
{
    private CategoryModel $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    public function getCategories()
    {
        $categories = $this->categoryModel->getAll();
        echo json_encode($categories);
    }

    public function getCategory($slug)
    {
        $category = $this->categoryModel->getCategory($slug);

        echo json_encode($category);
    }
}