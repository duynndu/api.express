<?php

namespace src\Controllers\Admin;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use src\Commons\Controller;
use src\Models\UserModel;

class AuthController extends Controller
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
    }

    public function login()
    {
        $_POST = json_decode(file_get_contents('php://input', true), true);
        if ($_POST['email'] && $_POST['password']) {
            $user = $this->userModel->getUser($_POST['email'], $_POST['password']);
            if ($user) {
                $token = JWT::encode([
                    'id' => $user['id'],
                    'role' => $user['role'],
                    'exp' => time() + 6000
                ], $_ENV['JWT_KEY'], 'HS256');

                echo json_encode([
                    'message' => 'Đăng nhập thành công',
                    'token' => $token
                ]);

            } else {
                echo json_encode(['message' => 'Tài khoản hoặc mật khẩu sai']);
            }
        }
    }

    public function interceptor()
    {
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];
            if (str_contains($authorizationHeader, 'Bearer')) {
                $bearerToken = str_replace('Bearer ', '', $authorizationHeader);
                veryfyToken($bearerToken);
            } else {
                echo json_encode([
                    'message'=> "Invalid Authorization format. Expected 'Bearer <token>'."
                ]);
                exit(0);
            }
        } else {
            echo json_encode([
                'message'=> "You not permission."
            ]);
            exit(0);
        }
    }
}
