<?php


namespace src\Controllers\Client;


use src\commons\Controller;
use src\Models\UserModel;

class UserController extends Controller
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }
}