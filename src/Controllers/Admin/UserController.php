<?php

namespace src\Controllers\Admin;

use src\Commons\Controller;
use src\Models\UserModel;

class UserController extends Controller
{
    private $userModel;
    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
    }
    public function index()
    {
        $this->renderViewAdmin('user.list',[
            'users'=>$this->userModel->getAll()
        ]);
    }
    public function show($id)
    {
        $this->renderViewAdmin('user.show',[
            'user'=>$this->userModel->getById($id)
        ]);
    }
    public function add()
    {
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $this->userModel->insert([
                'name'=>$_POST['name'],
                'email'=>$_POST['email'],
                'password'=>$_POST['password'],
            ]);
            header('location: /admin/user');
        }
        $this->renderViewAdmin('user.add');
    }
    public function update($id=0)
    {
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $this->userModel->update([
                'name'=>$_POST['name'],
                'email'=>$_POST['email'],
                'password'=>$_POST['password'],
            ],$id);
            header('location: /admin/user');
        }
        $this->renderViewAdmin('user.edit',[
            'user'=>$this->userModel->getById($id)
        ]);
    }
    public function delete($id)
    {
        $this->userModel->delete($id);
        header('location: /admin/user');
    }
}