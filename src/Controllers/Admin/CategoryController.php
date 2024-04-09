<?php
namespace src\Controllers\Admin;

use src\Commons\Controller;
use src\Models\CategoryModel;

class CategoryController extends Controller
{
    private $categoryModel;
    public function __construct()
    {
        parent::__construct();
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        $this->renderViewAdmin('category.list', [
            'title' => 'This is Dashboard',
            'categories'=>$this->categoryModel->getAll()
        ]);
    }
    public function add()
    {
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $this->categoryModel->insert([
                'name'=>$_POST['name'],
                'slug'=>$this->slugify->slugify($_POST['name'])
            ]);
            header('location: /admin/category');
        }
        $this->renderViewAdmin('category.add');
    }
    public function update($id=0)
    {
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $this->categoryModel->update([
                'name'=>$_POST['name'],
                'slug'=>$this->slugify->slugify($_POST['name'])
            ],$id);
            header('location: /admin/category');
        }
        $this->renderViewAdmin('category.edit',[
            'category'=>$this->categoryModel->getById($id)
        ]);
    }
    public function delete($id)
    {
        $this->categoryModel->delete($id);
        header('location: '.DIR_PATH.'admin/categories');
    }

}